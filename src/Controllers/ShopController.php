<?php

namespace Controllers;

use Core\View;
use Core\Database;
use Core\Lang;

class ShopController {
    
    private $db;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $config = require __DIR__ . '/../../config/database.php';
        $this->db = new Database($config);
        
        // Initialize language system
        Lang::init();
    }

    public function getCategoryDescendantIds($categoryId) {
        $ids = [(int)$categoryId];
        $queue = [(int)$categoryId];

        while (!empty($queue)) {
            $curr = array_shift($queue);
            $stmt = $this->db->query("SELECT id FROM categories WHERE parent_id = ?", [$curr]);
            $children = $stmt->fetchAll();
            foreach ($children as $ch) {
                $chId = (int)$ch['id'];
                if (!in_array($chId, $ids)) {
                    $ids[] = $chId;
                    $queue[] = $chId;
                }
            }
        }
        return $ids;
    }

    public function index() {
        // Get filter parameters
        $categoryId = $_GET['category'] ?? null;
        $subId = $_GET['sub'] ?? null;
        $search = $_GET['search'] ?? '';
        $isDeals = isset($_GET['deals']) && $_GET['deals'] == '1';

        // Fetch all categories first
        $allCategories = $this->db->query("SELECT categories.* FROM categories ORDER BY categories.name ASC")->fetchAll();

        $catById = [];
        $childrenMap = [];
        foreach ($allCategories as $cat) {
            $catById[$cat['id']] = $cat;
            $pId = !empty($cat['parent_id']) ? (int)$cat['parent_id'] : 0;
            if (!isset($childrenMap[$pId])) $childrenMap[$pId] = [];
            $childrenMap[$pId][] = (int)$cat['id'];
        }

        // Direct product counts per category
        $prodCountsByCat = [];
        $rawCounts = $this->db->query("SELECT category_id, COUNT(*) as cnt FROM products WHERE availability_status = 'in_stock' GROUP BY category_id")->fetchAll();
        foreach ($rawCounts as $rc) {
            $prodCountsByCat[$rc['category_id']] = (int)$rc['cnt'];
        }

        // Attach recursive product count and subcategory count to all categories
        foreach ($allCategories as &$cat) {
            $descIds = $this->getCategoryDescendantIds($cat['id']);
            $totalProds = 0;
            foreach ($descIds as $dId) {
                $totalProds += $prodCountsByCat[$dId] ?? 0;
            }
            $cat['total_product_count'] = $totalProds;
            $cat['sub_count'] = isset($childrenMap[$cat['id']]) ? count($childrenMap[$cat['id']]) : 0;
            $catById[$cat['id']]['total_product_count'] = $totalProds;
            $catById[$cat['id']]['sub_count'] = $cat['sub_count'];
        }
        unset($cat);

        // Smart Category Drill-Down:
        // If a subId is provided AND that subId has subcategories of its own (e.g. user clicked "রান্নার উপাদান ও মুদি"),
        // promote it to the active category so its subcategories are displayed in the SUBCATEGORY FILTER PILLS BAR!
        if ($subId && isset($catById[$subId]) && !empty($childrenMap[$subId])) {
            $categoryId = $subId;
            $subId = null;
        }

        // Determine main categories (top-level or children of single root)
        $rootIds = $childrenMap[0] ?? [];
        if (count($rootIds) === 1 && isset($childrenMap[$rootIds[0]])) {
            $mainCatIds = $childrenMap[$rootIds[0]];
        } else {
            $mainCatIds = $rootIds;
        }

        $mainCategories = [];
        foreach ($mainCatIds as $mId) {
            if (isset($catById[$mId])) {
                $mainCategories[] = $catById[$mId];
            }
        }

        // Sort main categories: feature 'রান্নাবান্না'/'খাদ্য ও মুদি' first, followed by categories with children/products
        usort($mainCategories, function($a, $b) use ($childrenMap) {
            $aIsCooking = (mb_strpos($a['name'], 'রান্না') !== false || mb_strpos($a['name'], 'খাদ্য') !== false);
            $bIsCooking = (mb_strpos($b['name'], 'রান্না') !== false || mb_strpos($b['name'], 'খাদ্য') !== false);
            if ($aIsCooking && !$bIsCooking) return -1;
            if (!$aIsCooking && $bIsCooking) return 1;
            $aHasChildren = !empty($childrenMap[$a['id']]) ? 1 : 0;
            $bHasChildren = !empty($childrenMap[$b['id']]) ? 1 : 0;
            if ($aHasChildren !== $bHasChildren) return $bHasChildren - $aHasChildren;
            return strcmp($a['name'], $b['name']);
        });

        // Determine if any filter is active
        $isFiltered = !empty($categoryId) || !empty($subId) || (isset($_GET['search']) && trim($_GET['search']) !== '') || $isDeals;

        // Determine active category, parent category and subcategories
        $activeCategory = null;
        $parentCategory = null;
        $subCategories = [];
        $targetCatId = null;
        $bannerTitle = null;

        if ($categoryId && isset($catById[$categoryId])) {
            $selectedCat = $catById[$categoryId];
            if (!empty($childrenMap[$categoryId])) {
                // Category itself has subcategories (e.g. user clicked "রান্নার উপাদান ও মুদি")
                $activeCategory = $selectedCat;
                if (!empty($selectedCat['parent_id']) && isset($catById[$selectedCat['parent_id']])) {
                    $parentCategory = $catById[$selectedCat['parent_id']];
                }
                foreach ($childrenMap[$categoryId] as $sId) {
                    if (isset($catById[$sId])) $subCategories[] = $catById[$sId];
                }
            } elseif (!empty($selectedCat['parent_id']) && isset($catById[$selectedCat['parent_id']])) {
                // Category is a leaf subcategory (e.g. "চাল ও শস্য"), show its siblings under parent
                $parentCategory = !empty($catById[$selectedCat['parent_id']]['parent_id']) && isset($catById[$catById[$selectedCat['parent_id']]['parent_id']]) 
                    ? $catById[$catById[$selectedCat['parent_id']]['parent_id']] 
                    : null;
                $activeCategory = $catById[$selectedCat['parent_id']];
                $subId = $categoryId;
                if (!empty($childrenMap[$activeCategory['id']])) {
                    foreach ($childrenMap[$activeCategory['id']] as $sId) {
                        if (isset($catById[$sId])) $subCategories[] = $catById[$sId];
                    }
                }
            } else {
                $activeCategory = $selectedCat;
            }
            $targetCatId = $subId ?: $categoryId;
            $bannerTitle = $activeCategory['name'] ?? null;
        } elseif ($subId && isset($catById[$subId])) {
            $selectedSub = $catById[$subId];
            if (!empty($selectedSub['parent_id']) && isset($catById[$selectedSub['parent_id']])) {
                $activeCategory = $catById[$selectedSub['parent_id']];
                if (!empty($activeCategory['parent_id']) && isset($catById[$activeCategory['parent_id']])) {
                    $parentCategory = $catById[$activeCategory['parent_id']];
                }
                if (!empty($childrenMap[$activeCategory['id']])) {
                    foreach ($childrenMap[$activeCategory['id']] as $sId) {
                        if (isset($catById[$sId])) $subCategories[] = $catById[$sId];
                    }
                }
            } else {
                $activeCategory = $selectedSub;
            }
            $targetCatId = $subId;
            $bannerTitle = $selectedSub['name'] ?? null;
        } elseif (!empty($search)) {
            $bannerTitle = 'অনুসন্ধান ফলাফল: "' . htmlspecialchars($search) . '"';
        } elseif ($isDeals) {
            $bannerTitle = 'বিশেষ অফার ও ডিলসমূহ';
        } else {
            // Home visit (no filters applied) - showcase top popular products storewide!
            $targetCatId = null;
            $activeCategory = null;
            $parentCategory = null;
            $subCategories = [];
            $bannerTitle = 'সবচেয়ে জনপ্রিয় পণ্যসমূহ';
        }

        // Build product query with sales volume calculation
        $sql = "SELECT products.*, categories.name as category_name,
                       COALESCE((SELECT SUM(oi.quantity) FROM order_items oi WHERE oi.product_id = products.id), 0) AS total_sold
                FROM products 
                LEFT JOIN categories ON products.category_id = categories.id
                WHERE products.availability_status = 'in_stock'";
        $params = [];

        if ($targetCatId) {
            $catIds = $this->getCategoryDescendantIds($targetCatId);
            if (!empty($catIds)) {
                $inPlaceholders = [];
                foreach ($catIds as $idx => $cId) {
                    $pKey = "cat_id_" . $idx;
                    $inPlaceholders[] = ":" . $pKey;
                    $params[$pKey] = $cId;
                }
                $sql .= " AND products.category_id IN (" . implode(',', $inPlaceholders) . ")";
            }
        }

        if ($search) {
            $sql .= " AND (products.name LIKE :search OR products.sku LIKE :search2 OR products.description LIKE :search3 OR products.tags LIKE :search4)";
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
            $params['search4'] = "%{$search}%";
        }

        if ($isDeals) {
            $sql .= " AND products.regular_price IS NOT NULL AND products.regular_price > products.sell_price";
        }

        // Sorting: Most popular products on top!
        // 1. Demand percentage (0-100)
        // 2. Real sales volume (total_sold)
        // 3. Discount priority (deals first)
        // 4. Recency (created_at DESC)
        $sql .= " ORDER BY products.demand_percentage DESC, total_sold DESC, (CASE WHEN products.regular_price > products.sell_price THEN 1 ELSE 0 END) DESC, products.created_at DESC";
        $stmt = $this->db->query($sql, $params);
        $products = $stmt->fetchAll();
        $topPopularProduct = !empty($products) ? $products[0] : null;

        // Fetch categories with in-stock products for filter tabs
        $stmtCat = $this->db->query("SELECT categories.*, COUNT(products.id) as product_count 
                                      FROM categories 
                                      LEFT JOIN products ON categories.id = products.category_id AND products.availability_status = 'in_stock'
                                      GROUP BY categories.id 
                                      HAVING product_count > 0
                                      ORDER BY categories.name ASC");
        $categories = $stmtCat->fetchAll();

        // Fetch special deals for "Deal of the Day" showcase
        $dealsStmt = $this->db->query("SELECT products.*, categories.name as category_name 
                                       FROM products 
                                       LEFT JOIN categories ON products.category_id = categories.id
                                       WHERE products.availability_status = 'in_stock' 
                                         AND products.regular_price IS NOT NULL 
                                         AND products.regular_price > products.sell_price
                                       ORDER BY (products.regular_price - products.sell_price) DESC 
                                       LIMIT 10");
        $dealProducts = $dealsStmt->fetchAll();
        if (empty($dealProducts)) {
            $dealProducts = array_slice($products, 0, 6);
        }

        // Subcategory list subtitle for the hero banner
        if ($isFiltered && !empty($subCategories)) {
            $subNames = array_map(function($s) { return $s['name']; }, $subCategories);
            $bannerSubtitle = implode(', ', array_slice($subNames, 0, 7));
        } elseif (!empty($search)) {
            $bannerSubtitle = count($products) . ' টি পণ্য পাওয়া গেছে';
        } elseif ($isDeals) {
            $bannerSubtitle = 'সেরা ছাড়ে আকর্ষণীয় নিত্যপ্রয়োজনীয় পণ্য';
        } else {
            $bannerSubtitle = 'সেরা মানের নিত্যপ্রয়োজনীয় পণ্য ও দ্রুত ডেলিভারি - আপনার দৈনন্দিন প্রয়োজনের সবকিছু এক জায়গায়';
        }

        $parentBackUrl = '';
        if ($parentCategory) {
            $parentBackUrl = '?category=' . $parentCategory['id'];
        }

        return View::render('shop/index', [
            'products' => $products,
            'topPopularProduct' => $topPopularProduct,
            'isFiltered' => $isFiltered,
            'bannerTitle' => $bannerTitle,
            'categories' => $categories,
            'allCategories' => $allCategories,
            'catById' => $catById,
            'mainCategories' => $mainCategories,
            'childrenMap' => $childrenMap,
            'parentCategory' => $parentCategory,
            'activeCategory' => $activeCategory,
            'activeSubId' => $subId,
            'bannerSubtitle' => $bannerSubtitle,
            'subCategories' => $subCategories,
            'parentBackUrl' => $parentBackUrl,
            'dealProducts' => $dealProducts,
            'currentCategory' => $categoryId,
            'search' => $search,
            'isDeals' => $isDeals
        ]);
    }

    public function category() {
        $catId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_GET['category']) ? (int)$_GET['category'] : 0);
        $subId = isset($_GET['sub']) && $_GET['sub'] !== '' ? (int)$_GET['sub'] : null;
        $search = trim($_GET['search'] ?? '');
        $sort = $_GET['sort'] ?? 'newest';
        $minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;
        $selectedBrand = isset($_GET['brand']) && $_GET['brand'] !== '' ? (int)$_GET['brand'] : null;
        $inStockOnly = isset($_GET['in_stock']) ? ($_GET['in_stock'] == '1') : true;
        $isDeals = isset($_GET['deals']) && $_GET['deals'] == '1';

        // Fetch all categories for relationships & header
        $allCategories = $this->db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
        $catById = [];
        $childrenMap = [];
        foreach ($allCategories as $c) {
            $catById[$c['id']] = $c;
            $pId = !empty($c['parent_id']) ? (int)$c['parent_id'] : 0;
            if (!isset($childrenMap[$pId])) $childrenMap[$pId] = [];
            $childrenMap[$pId][] = (int)$c['id'];
        }

        // Direct product counts per category
        $prodCountsByCat = [];
        $rawCounts = $this->db->query("SELECT category_id, COUNT(*) as cnt FROM products WHERE availability_status = 'in_stock' GROUP BY category_id")->fetchAll();
        foreach ($rawCounts as $rc) {
            $prodCountsByCat[$rc['category_id']] = (int)$rc['cnt'];
        }

        // Attach counts
        foreach ($allCategories as &$c) {
            $descIds = $this->getCategoryDescendantIds($c['id']);
            $tot = 0;
            foreach ($descIds as $d) {
                $tot += $prodCountsByCat[$d] ?? 0;
            }
            $c['total_product_count'] = $tot;
            $c['sub_count'] = isset($childrenMap[$c['id']]) ? count($childrenMap[$c['id']]) : 0;
            $catById[$c['id']]['total_product_count'] = $tot;
            $catById[$c['id']]['sub_count'] = $c['sub_count'];
        }
        unset($c);

        // Determine main categories
        $rootIds = $childrenMap[0] ?? [];
        if (count($rootIds) === 1 && isset($childrenMap[$rootIds[0]])) {
            $mainCatIds = $childrenMap[$rootIds[0]];
        } else {
            $mainCatIds = $rootIds;
        }
        $mainCategories = [];
        foreach ($mainCatIds as $mId) {
            if (isset($catById[$mId])) $mainCategories[] = $catById[$mId];
        }

        // If no catId given, render the complete All Categories directory page!
        if (!$catId || !isset($catById[$catId])) {
            return View::render('shop/all_categories', [
                'allCategories' => $allCategories,
                'mainCategories' => $mainCategories,
                'catById' => $catById,
                'childrenMap' => $childrenMap,
                'search' => $search
            ]);
        }

        $currentCategory = $catById[$catId];

        // Determine Parent Category and Subcategories list
        $parentCategory = null;
        $subCategories = [];

        if (!empty($childrenMap[$catId])) {
            // Category has children (e.g. user selected 'রান্নাবান্না')
            $parentCategory = $currentCategory;
            foreach ($childrenMap[$catId] as $childId) {
                if (isset($catById[$childId])) {
                    $subCategories[] = $catById[$childId];
                }
            }
        } elseif (!empty($currentCategory['parent_id']) && isset($catById[$currentCategory['parent_id']]) && $currentCategory['parent_id'] != 1) {
            // Subcategory whose parent is not root 1 (e.g. user selected 'চাল')
            $parentCategory = $catById[$currentCategory['parent_id']];
            if ($subId === null) {
                $subId = $catId; // Default subcategory to currentCategory
            }
            if (!empty($childrenMap[$parentCategory['id']])) {
                foreach ($childrenMap[$parentCategory['id']] as $childId) {
                    if (isset($catById[$childId])) {
                        $subCategories[] = $catById[$childId];
                    }
                }
            }
        } else {
            $parentCategory = $currentCategory;
        }

        $activeSubId = $subId;
        $activeSubCategory = ($activeSubId && isset($catById[$activeSubId])) ? $catById[$activeSubId] : null;

        // Determine category scope for products
        $targetScopeCatId = $activeSubId ?: ($parentCategory ? $parentCategory['id'] : $currentCategory['id']);
        $filterCatIds = $this->getCategoryDescendantIds($targetScopeCatId);
        
        $parentAllCatIds = $this->getCategoryDescendantIds($parentCategory ? $parentCategory['id'] : $currentCategory['id']);

        // Fetch brands available in this category hierarchy
        $brands = [];
        if (!empty($parentAllCatIds)) {
            $inPlaces = implode(',', array_fill(0, count($parentAllCatIds), '?'));
            $bStmt = $this->db->query("SELECT b.id, b.name, COUNT(p.id) as prod_count 
                FROM brands b 
                JOIN products p ON p.brand_id = b.id 
                WHERE p.category_id IN ($inPlaces) AND p.availability_status = 'in_stock'
                GROUP BY b.id, b.name 
                ORDER BY b.name ASC", $parentAllCatIds);
            $brands = $bStmt->fetchAll();
        }

        // Fetch min & max price in this category
        $minPriceBound = 0;
        $maxPriceBound = 1000;
        if (!empty($parentAllCatIds)) {
            $inPlaces = implode(',', array_fill(0, count($parentAllCatIds), '?'));
            $pStmt = $this->db->query("SELECT MIN(sell_price) as min_p, MAX(sell_price) as max_p FROM products WHERE category_id IN ($inPlaces)", $parentAllCatIds);
            $priceRow = $pStmt->fetch();
            if ($priceRow && $priceRow['max_p'] !== null) {
                $minPriceBound = (float)$priceRow['min_p'];
                $maxPriceBound = (float)$priceRow['max_p'];
            }
        }

        // Build product query with filters
        $sql = "SELECT products.*, categories.name as category_name, brands.name as brand_name 
                FROM products 
                LEFT JOIN categories ON products.category_id = categories.id
                LEFT JOIN brands ON products.brand_id = brands.id 
                WHERE 1=1";
        $params = [];

        if (!empty($filterCatIds)) {
            $inPlaceholders = [];
            foreach ($filterCatIds as $idx => $fId) {
                $pKey = "cat_" . $idx;
                $inPlaceholders[] = ":" . $pKey;
                $params[$pKey] = $fId;
            }
            $sql .= " AND products.category_id IN (" . implode(',', $inPlaceholders) . ")";
        }

        if ($inStockOnly) {
            $sql .= " AND products.availability_status = 'in_stock'";
        }

        if ($selectedBrand) {
            $sql .= " AND products.brand_id = :brand_id";
            $params['brand_id'] = $selectedBrand;
        }

        if ($minPrice !== null) {
            $sql .= " AND products.sell_price >= :min_price";
            $params['min_price'] = $minPrice;
        }

        if ($maxPrice !== null) {
            $sql .= " AND products.sell_price <= :max_price";
            $params['max_price'] = $maxPrice;
        }

        if ($isDeals) {
            $sql .= " AND products.regular_price IS NOT NULL AND products.regular_price > products.sell_price";
        }

        if ($search) {
            $sql .= " AND (products.name LIKE :search OR products.sku LIKE :search2 OR products.description LIKE :search3 OR products.tags LIKE :search4)";
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
            $params['search4'] = "%{$search}%";
        }

        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY products.sell_price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY products.sell_price DESC";
                break;
            case 'discount':
                $sql .= " ORDER BY (products.regular_price - products.sell_price) DESC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY products.name ASC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY products.created_at DESC";
                break;
        }

        $stmt = $this->db->query($sql, $params);
        $products = $stmt->fetchAll();

        return View::render('shop/category', [
            'products' => $products,
            'currentCategory' => $currentCategory,
            'parentCategory' => $parentCategory,
            'subCategories' => $subCategories,
            'activeSubId' => $activeSubId,
            'activeSubCategory' => $activeSubCategory,
            'allCategories' => $allCategories,
            'mainCategories' => $mainCategories,
            'childrenMap' => $childrenMap,
            'brands' => $brands,
            'selectedBrand' => $selectedBrand,
            'minPriceBound' => $minPriceBound,
            'maxPriceBound' => $maxPriceBound,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'sort' => $sort,
            'inStockOnly' => $inStockOnly,
            'isDeals' => $isDeals,
            'search' => $search
        ]);
    }

    public function shop() {
        $catId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_GET['category']) ? (int)$_GET['category'] : 0);
        $subId = isset($_GET['sub']) && $_GET['sub'] !== '' ? (int)$_GET['sub'] : null;
        $search = trim($_GET['search'] ?? '');
        $sort = $_GET['sort'] ?? 'newest';
        $minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;
        $selectedBrand = isset($_GET['brand']) && $_GET['brand'] !== '' ? (int)$_GET['brand'] : null;
        $inStockOnly = isset($_GET['in_stock']) ? ($_GET['in_stock'] == '1') : false;
        $isDeals = isset($_GET['deals']) && $_GET['deals'] == '1';

        // Fetch all categories for filter sidebar & header
        $allCategories = $this->db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
        $catById = [];
        $childrenMap = [];
        foreach ($allCategories as $c) {
            $catById[$c['id']] = $c;
            $pId = !empty($c['parent_id']) ? (int)$c['parent_id'] : 0;
            if (!isset($childrenMap[$pId])) $childrenMap[$pId] = [];
            $childrenMap[$pId][] = (int)$c['id'];
        }

        // Direct product counts per category
        $prodCountsByCat = [];
        $rawCounts = $this->db->query("SELECT category_id, COUNT(*) as cnt FROM products WHERE availability_status = 'in_stock' GROUP BY category_id")->fetchAll();
        foreach ($rawCounts as $rc) {
            $prodCountsByCat[$rc['category_id']] = (int)$rc['cnt'];
        }

        // Attach counts
        foreach ($allCategories as &$c) {
            $descIds = $this->getCategoryDescendantIds($c['id']);
            $tot = 0;
            foreach ($descIds as $d) {
                $tot += $prodCountsByCat[$d] ?? 0;
            }
            $c['total_product_count'] = $tot;
            $c['sub_count'] = isset($childrenMap[$c['id']]) ? count($childrenMap[$c['id']]) : 0;
            $catById[$c['id']]['total_product_count'] = $tot;
            $catById[$c['id']]['sub_count'] = $c['sub_count'];
        }
        unset($c);

        // Determine main categories
        $rootIds = $childrenMap[0] ?? [];
        if (count($rootIds) === 1 && isset($childrenMap[$rootIds[0]])) {
            $mainCatIds = $childrenMap[$rootIds[0]];
        } else {
            $mainCatIds = $rootIds;
        }
        $mainCategories = [];
        foreach ($mainCatIds as $mId) {
            if (isset($catById[$mId])) $mainCategories[] = $catById[$mId];
        }

        $currentCategory = ($catId && isset($catById[$catId])) ? $catById[$catId] : null;
        $filterCatIds = [];
        if ($currentCategory) {
            $targetId = ($subId && isset($catById[$subId])) ? $subId : $currentCategory['id'];
            $filterCatIds = $this->getCategoryDescendantIds($targetId);
        }

        // Fetch brands
        $brands = $this->db->query("SELECT b.id, b.name, COUNT(p.id) as prod_count 
            FROM brands b 
            JOIN products p ON p.brand_id = b.id 
            WHERE p.availability_status = 'in_stock'
            GROUP BY b.id, b.name 
            ORDER BY b.name ASC")->fetchAll();

        // Price bounds
        $priceRow = $this->db->query("SELECT MIN(sell_price) as min_p, MAX(sell_price) as max_p FROM products WHERE availability_status = 'in_stock'")->fetch();
        $minPriceBound = ($priceRow && $priceRow['min_p'] !== null) ? (float)$priceRow['min_p'] : 0;
        $maxPriceBound = ($priceRow && $priceRow['max_p'] !== null) ? (float)$priceRow['max_p'] : 1000;

        // Query products
        $sql = "SELECT products.*, categories.name as category_name, brands.name as brand_name 
                FROM products 
                LEFT JOIN categories ON products.category_id = categories.id
                LEFT JOIN brands ON products.brand_id = brands.id 
                WHERE 1=1";
        $params = [];

        if (!empty($filterCatIds)) {
            $inPlaceholders = [];
            foreach ($filterCatIds as $idx => $fId) {
                $pKey = "cat_" . $idx;
                $inPlaceholders[] = ":" . $pKey;
                $params[$pKey] = $fId;
            }
            $sql .= " AND products.category_id IN (" . implode(',', $inPlaceholders) . ")";
        }

        if ($inStockOnly) {
            $sql .= " AND products.availability_status = 'in_stock'";
        }

        if ($selectedBrand) {
            $sql .= " AND products.brand_id = :brand_id";
            $params['brand_id'] = $selectedBrand;
        }

        if ($minPrice !== null) {
            $sql .= " AND products.sell_price >= :min_price";
            $params['min_price'] = $minPrice;
        }

        if ($maxPrice !== null) {
            $sql .= " AND products.sell_price <= :max_price";
            $params['max_price'] = $maxPrice;
        }

        if ($isDeals) {
            $sql .= " AND products.regular_price IS NOT NULL AND products.regular_price > products.sell_price";
        }

        if ($search) {
            $sql .= " AND (products.name LIKE :search OR products.sku LIKE :search2 OR products.description LIKE :search3 OR products.tags LIKE :search4)";
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
            $params['search3'] = "%{$search}%";
            $params['search4'] = "%{$search}%";
        }

        // Sorting
        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY products.sell_price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY products.sell_price DESC";
                break;
            case 'deals':
                $sql .= " ORDER BY (products.regular_price - products.sell_price) DESC, products.id DESC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY products.name ASC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY products.created_at DESC, products.id DESC";
                break;
        }

        $stmt = $this->db->query($sql, $params);
        $products = $stmt->fetchAll();

        return View::render('shop/shop', [
            'products' => $products,
            'allCategories' => $allCategories,
            'mainCategories' => $mainCategories,
            'catById' => $catById,
            'childrenMap' => $childrenMap,
            'currentCategory' => $currentCategory,
            'brands' => $brands,
            'minPriceBound' => $minPriceBound,
            'maxPriceBound' => $maxPriceBound,
            'catId' => $catId,
            'search' => $search,
            'sort' => $sort,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'selectedBrand' => $selectedBrand,
            'inStockOnly' => $inStockOnly,
            'isDeals' => $isDeals,
        ]);
    }

    public function product() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $sku = trim($_GET['sku'] ?? '');

        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';

        if (!$id && !$sku) {
            header("Location: {$base}/");
            exit;
        }

        if ($id) {
            $stmt = $this->db->query("
                SELECT p.*, c.name as category_name, c.parent_id as category_parent_id, b.name as brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.id = ?
            ", [$id]);
        } else {
            $stmt = $this->db->query("
                SELECT p.*, c.name as category_name, c.parent_id as category_parent_id, b.name as brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.sku = ?
            ", [$sku]);
        }
        $product = $stmt->fetch();

        if (!$product) {
            header("Location: {$base}/");
            exit;
        }

        // Fetch parent category if exists
        $parentCategory = null;
        if (!empty($product['category_parent_id'])) {
            $pCatStmt = $this->db->query("SELECT * FROM categories WHERE id = ?", [$product['category_parent_id']]);
            $parentCategory = $pCatStmt->fetch();
        }

        // Fetch related products (same category or popular items)
        $relatedProducts = [];
        if (!empty($product['category_id'])) {
            $relStmt = $this->db->query("
                SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = ? AND p.id != ? AND p.availability_status = 'in_stock' 
                ORDER BY p.id DESC LIMIT 8
            ", [$product['category_id'], $product['id']]);
            $relatedProducts = $relStmt->fetchAll();
        }

        if (count($relatedProducts) < 4) {
            $moreStmt = $this->db->query("
                SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id != ? AND p.availability_status = 'in_stock' 
                ORDER BY p.id DESC LIMIT 6
            ", [$product['id']]);
            $moreProds = $moreStmt->fetchAll();
            $existingIds = array_column($relatedProducts, 'id');
            foreach ($moreProds as $mp) {
                if (!in_array($mp['id'], $existingIds) && count($relatedProducts) < 8) {
                    $relatedProducts[] = $mp;
                }
            }
        }

        // Parse custom variants or heuristics
        $variants = [];
        if (!empty($product['unit_variants_json'])) {
            $decoded = json_decode($product['unit_variants_json'], true);
            if (is_array($decoded)) {
                $variants = $decoded;
            }
        }

        // Locale
        $locale = Lang::locale();

        return View::render('shop/product_detail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'parentCategory' => $parentCategory,
            'variants' => $variants,
            'locale' => $locale
        ]);
    }

    public function cart() {
        $cart = $_SESSION['cart'] ?? [];
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $locale = Lang::locale();
        $spendMoreOffers = \Models\Setting::getSpendMoreOffersData($subtotal, $locale);
        $deliveryCalc = \Models\Setting::calculateDeliveryCharge($subtotal);

        return View::render('shop/cart', [
            'cart' => $cart,
            'subtotal' => $subtotal,
            'spendMoreOffers' => $spendMoreOffers,
            'deliveryCalc' => $deliveryCalc
        ]);
    }

    public function addToCart() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'] ?? null;
            $quantity = (int)($_POST['quantity'] ?? 1);
            $variantTitle = trim($_POST['variant_title'] ?? '');
            $variantPrice = isset($_POST['variant_price']) && $_POST['variant_price'] !== '' ? floatval($_POST['variant_price']) : null;
            $variantQty = isset($_POST['variant_qty']) && $_POST['variant_qty'] !== '' ? floatval($_POST['variant_qty']) : 1.000;

            if ($productId) {
                // Fetch product to verify and get current price
                $stmt = $this->db->query("SELECT * FROM products WHERE id = ?", [$productId]);
                $product = $stmt->fetch();

                if ($product && $product['availability_status'] === 'in_stock') {
                    if (!isset($_SESSION['cart'])) {
                        $_SESSION['cart'] = [];
                    }

                    $cartKey = $variantTitle ? $productId . '_' . md5($variantTitle) : (string)$productId;
                    $price = ($variantPrice !== null && $variantPrice > 0) ? $variantPrice : floatval($product['sell_price']);
                    $displayName = $variantTitle ? $product['name'] . " ({$variantTitle})" : $product['name'];
                    $regPrice = null;
                    if (!empty($product['regular_price'])) {
                        $regPrice = $variantTitle ? floatval($product['regular_price']) * ($variantQty > 0 ? $variantQty : 1) : floatval($product['regular_price']);
                    }

                    if (isset($_SESSION['cart'][$cartKey])) {
                        $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
                    } else {
                        $_SESSION['cart'][$cartKey] = [
                            'cart_key' => $cartKey,
                            'product_id' => $product['id'],
                            'name' => $displayName,
                            'price' => $price,
                            'regular_price' => $regPrice,
                            'image' => !empty($product['image_path']) ? $product['image_path'] : '/sodai-dorkar/public/images/default-product.svg',
                            'quantity' => $quantity,
                            'base_qty' => $variantQty > 0 ? $variantQty : 1.000,
                            'variant_title' => $variantTitle,
                            'stock' => $product['stock_qty']
                        ];
                    }
                    
                    // AJAX response
                    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        $cartCount = 0;
                        $cartTotal = 0;
                        foreach ($_SESSION['cart'] as $item) {
                            $cartCount += $item['quantity'];
                            $cartTotal += $item['price'] * $item['quantity'];
                        }
                        $spendMoreOffers = \Models\Setting::getSpendMoreOffersData($cartTotal, Lang::locale());
                        echo json_encode([
                            'status' => 'success',
                            'cart_count' => $cartCount,
                            'cart_total' => $cartTotal,
                            'cart' => $_SESSION['cart'],
                            'product_name' => $displayName,
                            'spend_more_offers' => $spendMoreOffers,
                            'message' => Lang::get('toast_added_to_cart')
                        ]);
                        exit;
                    }

                    header('Location: /sodai-dorkar/public/cart');
                    exit;
                }
            }
        }
        header('Location: /sodai-dorkar/public/');
        exit;
    }

    public function removeFromCart() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $key = $_POST['product_id'] ?? ($_POST['cart_key'] ?? null);
            if ($key && isset($_SESSION['cart'][$key])) {
                unset($_SESSION['cart'][$key]);
            }

            // AJAX response
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                $cartCount = 0;
                $subtotal = 0;
                foreach (($_SESSION['cart'] ?? []) as $item) {
                    $cartCount += $item['quantity'];
                    $subtotal += $item['price'] * $item['quantity'];
                }
                $spendMoreOffers = \Models\Setting::getSpendMoreOffersData($subtotal, Lang::locale());
                echo json_encode([
                    'status' => 'success',
                    'cart_count' => $cartCount,
                    'cart_total' => $subtotal,
                    'subtotal' => $subtotal,
                    'cart' => $_SESSION['cart'] ?? [],
                    'spend_more_offers' => $spendMoreOffers
                ]);
                exit;
            }
        }
        header('Location: /sodai-dorkar/public/cart');
        exit;
    }
    
    public function updateCart() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $key = $_POST['product_id'] ?? ($_POST['cart_key'] ?? null);
            $quantity = (int)($_POST['quantity'] ?? 1);
            if ($key && isset($_SESSION['cart'][$key])) {
                if ($quantity > 0) {
                    $_SESSION['cart'][$key]['quantity'] = $quantity;
                } else {
                    unset($_SESSION['cart'][$key]);
                }
            }

            // AJAX response
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                $cartCount = 0;
                $subtotal = 0;
                foreach (($_SESSION['cart'] ?? []) as $item) {
                    $cartCount += $item['quantity'];
                    $subtotal += $item['price'] * $item['quantity'];
                }
                $itemTotal = 0;
                if (isset($_SESSION['cart'][$key])) {
                    $itemTotal = $_SESSION['cart'][$key]['price'] * $_SESSION['cart'][$key]['quantity'];
                }
                $spendMoreOffers = \Models\Setting::getSpendMoreOffersData($subtotal, Lang::locale());
                echo json_encode([
                    'status' => 'success',
                    'cart_count' => $cartCount,
                    'cart_total' => $subtotal,
                    'subtotal' => $subtotal,
                    'item_total' => $itemTotal,
                    'quantity' => $quantity,
                    'cart' => $_SESSION['cart'] ?? [],
                    'spend_more_offers' => $spendMoreOffers
                ]);
                exit;
            }
        }
        header('Location: /sodai-dorkar/public/cart');
        exit;
    }

    public function checkout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            header('Location: /sodai-dorkar/public/');
            exit;
        }

        // Must be logged in
        if (empty($_SESSION['customer_id'])) {
            header('Location: /sodai-dorkar/public/checkout/auth');
            exit;
        }

        // Stock validation — remove items that are no longer available
        foreach ($cart as $key => $item) {
            $pid = $item['product_id'] ?? $key;
            $stmt = $this->db->query("SELECT availability_status, sell_price FROM products WHERE id = ?", [$pid]);
            $product = $stmt->fetch();
            if (!$product || $product['availability_status'] !== 'in_stock') {
                unset($_SESSION['cart'][$key]);
            }
        }

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            header('Location: /sodai-dorkar/public/');
            exit;
        }
        
        // Fetch customer details
        $stmt = $this->db->query("
            SELECT c.*, a.name as area_name, z.name as zone_name, p.name as point_name 
            FROM customers c
            LEFT JOIN areas a ON c.area_id = a.id
            LEFT JOIN zones z ON c.zone_id = z.id
            LEFT JOIN points p ON c.point_id = p.id
            WHERE c.id = ?
        ", [$_SESSION['customer_id']]);
        $customer = $stmt->fetch();

        // Calculate subtotal & estimated weight
        $subtotal = 0;
        $totalWeightKg = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
            $totalWeightKg += floatval($item['base_qty'] ?? 1.0) * intval($item['quantity'] ?? 1);
        }

        // Calculate delivery charge
        $deliveryCalc = \Models\Setting::calculateDeliveryCharge(
            $subtotal,
            $customer['area_id'] ?? null,
            $customer['point_id'] ?? null,
            [
                'is_cod' => true,
                'weight_kg' => $totalWeightKg
            ]
        );

        $ecommerceSettings = \Models\Setting::getMultiple([
            'min_order_amount',
            'max_order_amount',
            'express_delivery_enabled',
            'express_delivery_charge',
            'express_delivery_cutoff',
            'time_slots_enabled',
            'time_slot_morning_fee',
            'time_slot_afternoon_fee',
            'time_slot_evening_fee',
            'delivery_bad_weather_surcharge_enabled',
            'delivery_bad_weather_fee',
            'delivery_bad_weather_notice'
        ]);

        $spendMoreOffers = \Models\Setting::getSpendMoreOffersData($subtotal, Lang::locale());

        return View::render('shop/checkout', [
            'cart' => $cart,
            'customer' => $customer,
            'deliveryCalc' => $deliveryCalc,
            'ecommerceSettings' => $ecommerceSettings,
            'subtotal' => $subtotal,
            'spendMoreOffers' => $spendMoreOffers
        ]);
    }

    public function placeOrder() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (empty($_SESSION['customer_id'])) {
            header('Location: /sodai-dorkar/public/checkout/auth');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cart = $_SESSION['cart'] ?? [];
            if (empty($cart)) {
                header('Location: /sodai-dorkar/public/');
                exit;
            }

            try {
                $pdo = $this->db->getConnection();
                $pdo->beginTransaction();

                // 1. Get customer details for the order
                $customerId = $_SESSION['customer_id'];
                $stmt = $this->db->query("SELECT area_id, zone_id, point_id, address_details, phone FROM customers WHERE id = ?", [$customerId]);
                $customer = $stmt->fetch();
                
                if (!$customer) {
                    throw new \Exception("Customer not found");
                }

                $areaId = $customer['area_id'] ?? null;
                $zoneId = $customer['zone_id'] ?? null;
                $pointId = $customer['point_id'] ?? null;
                $address = $customer['address_details'];
                $phone = $customer['phone'];

                // 2. Validate Order Subtotal & Weight
                $subtotal = 0;
                $totalWeightKg = 0;
                foreach ($cart as $item) {
                    $subtotal += $item['price'] * $item['quantity'];
                    $totalWeightKg += floatval($item['base_qty'] ?? 1.0) * intval($item['quantity'] ?? 1);
                }

                $ecommerceSettings = \Models\Setting::getMultiple([
                    'min_order_amount',
                    'max_order_amount'
                ]);

                $minOrder = floatval($ecommerceSettings['min_order_amount'] ?? 0);
                $maxOrder = floatval($ecommerceSettings['max_order_amount'] ?? 0);

                if ($minOrder > 0 && $subtotal < $minOrder) {
                    throw new \Exception("Minimum order amount is ৳" . number_format($minOrder, 2));
                }
                if ($maxOrder > 0 && $subtotal > $maxOrder) {
                    throw new \Exception("Maximum order amount is ৳" . number_format($maxOrder, 2));
                }

                // Payment method & options
                $rawPayment = $_POST['payment_method'] ?? 'cash';
                $paymentMethod = ($rawPayment === 'cod') ? 'cash' : $rawPayment;
                $isExpress = !empty($_POST['is_express']);
                
                // Delivery Calculation
                $deliveryCalc = \Models\Setting::calculateDeliveryCharge(
                    $subtotal,
                    $areaId,
                    $pointId,
                    [
                        'is_cod' => ($paymentMethod === 'cash'),
                        'is_express' => $isExpress,
                        'weight_kg' => $totalWeightKg
                    ]
                );

                // Spend-More Promotional Offers Calculation
                $spendMoreOffers = \Models\Setting::getSpendMoreOffersData($subtotal, Lang::locale());
                $milestoneDiscount = floatval($spendMoreOffers['discount_amount'] ?? 0);
                
                $deliveryCharge = $deliveryCalc['final_charge'] ?? 0.00;
                $deliveryDiscount = ($deliveryCalc['discount'] ?? 0.00) + $milestoneDiscount;
                $originalAmount = $subtotal + floatval($deliveryCalc['base_charge'] ?? $deliveryCharge);
                $totalAmount = max(0, round($subtotal - $milestoneDiscount + $deliveryCharge, 2));

                // Time slot / delivery instructions
                $selectedSlot = trim($_POST['delivery_slot'] ?? '');
                $riderNote = !empty($selectedSlot) ? "Preferred Window: " . $selectedSlot : null;
                if ($isExpress) {
                    $riderNote = "⚡ EXPRESS 30-MIN PRIORITY. " . ($riderNote ?? '');
                }

                // Add Free Gift & Perk details to parcel instructions
                $adminNote = null;
                if (!empty($spendMoreOffers['unlocked_gifts'])) {
                    $giftText = "🎁 FREE GIFT: " . implode(', ', $spendMoreOffers['unlocked_gifts']);
                    $riderNote = $giftText . ". " . ($riderNote ?? '');
                    $adminNote = $giftText;
                }
                if (!empty($spendMoreOffers['unlocked_perks'])) {
                    $perkText = "🌟 SPECIAL PERK: " . implode(', ', $spendMoreOffers['unlocked_perks']);
                    $adminNote = ($adminNote ? $adminNote . " | " : "") . $perkText;
                }
                $amountChangeReason = $milestoneDiscount > 0 ? "Promotional offer discount: ৳" . number_format($milestoneDiscount, 2) : null;

                $this->db->query("INSERT INTO orders (customer_id, area_id, zone_id, point_id, status, total_amount, original_amount, delivery_charge, delivery_discount, amount_changed_by, amount_change_reason, payment_method, rider_note, admin_note, delivery_address, contact_number) VALUES (?, ?, ?, ?, 'pending', ?, ?, ?, ?, 'system', ?, ?, ?, ?, ?, ?)",
                    [$customerId, $areaId, $zoneId, $pointId, $totalAmount, $originalAmount, $deliveryCharge, $deliveryDiscount, $amountChangeReason, $paymentMethod, $riderNote, $adminNote, $address, $phone]);
                $orderId = $pdo->lastInsertId();

                // 3. Create Order Items & Deduct Base Stock
                $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, unit_title, base_qty, quantity, price) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtStock = $pdo->prepare("UPDATE products SET stock_qty = stock_qty - ? WHERE id = ?");

                foreach ($cart as $key => $item) {
                    $pid = $item['product_id'] ?? $key;
                    $unitTitle = $item['variant_title'] ?? null;
                    $baseQty = floatval($item['base_qty'] ?? 1.000);
                    $qtyOrdered = intval($item['quantity']);
                    $deductTotal = $baseQty * $qtyOrdered;

                    $stmtItem->execute([$orderId, $pid, $unitTitle, $baseQty, $qtyOrdered, $item['price']]);
                    $stmtStock->execute([$deductTotal, $pid]);
                }

                $pdo->commit();
                
                // Auto-assignment happens later in Dispatch now

                // Add tracking log after commit
                try {
                    $trackOrderModel = new \Models\Order();
                    $trackOrderModel->addTrackingLog($orderId, 'pending', 'Consignment created by Customer (Web).');
                } catch (\Exception $ex) {}

                // Recalculate Product Demand (On-Demand Feature)
                try {
                    \Models\Product::recalculateDemand();
                } catch (\Exception $ex) {
                    // Fail gracefully
                }

                // Clear cart
                unset($_SESSION['cart']);

                // Redirect to success
                header('Location: /sodai-dorkar/public/order/success?order_id=' . $orderId);
                exit;

            } catch (\Exception $e) {
                $pdo->rollBack();
                die("Order placement failed: " . $e->getMessage());
            }
        }
    }
    
    public function success() {
        $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;
        $order = null;
        $orderItems = [];
        $crossSellingProducts = [];

        if ($orderId) {
            $orderStmt = $this->db->query("SELECT * FROM orders WHERE id = :id LIMIT 1", ['id' => $orderId]);
            $order = $orderStmt->fetch();
            if ($order) {
                $itemsStmt = $this->db->query("
                    SELECT oi.*, p.category_id, p.image_path, p.name as product_name, p.selling_unit, p.base_unit
                    FROM order_items oi
                    LEFT JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = :order_id
                ", ['order_id' => $orderId]);
                $orderItems = $itemsStmt->fetchAll();
            }
        }

        // Collect category IDs and product IDs to exclude from recommendations
        $excludeIds = [0];
        $categoryIds = [];
        if (!empty($orderItems)) {
            foreach ($orderItems as $item) {
                if (!empty($item['product_id'])) $excludeIds[] = (int)$item['product_id'];
                if (!empty($item['category_id'])) $categoryIds[] = (int)$item['category_id'];
            }
        }
        $categoryIds = array_unique(array_filter($categoryIds));
        $excludePlaceholders = implode(',', array_map('intval', array_unique($excludeIds)));

        // 1. First fetch products from related categories of purchased items
        if (!empty($categoryIds)) {
            $catPlaceholders = implode(',', array_map('intval', $categoryIds));
            $sql = "SELECT p.*, c.name as category_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.availability_status = 'in_stock' 
                      AND p.id NOT IN ($excludePlaceholders)
                      AND p.category_id IN ($catPlaceholders)
                    ORDER BY (p.regular_price - p.sell_price) DESC, p.id DESC
                    LIMIT 8";
            $crossSellingProducts = $this->db->query($sql)->fetchAll();
        }

        // 2. If fewer than 8 items found, top up with deals or top in-stock products
        if (count($crossSellingProducts) < 8) {
            $existingCrossIds = array_merge($excludeIds, array_column($crossSellingProducts, 'id'));
            $existingPlaceholders = implode(',', array_map('intval', array_unique($existingCrossIds)));
            $needed = 8 - count($crossSellingProducts);
            $sql = "SELECT p.*, c.name as category_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.availability_status = 'in_stock' 
                      AND p.id NOT IN ($existingPlaceholders)
                    ORDER BY 
                      (CASE WHEN p.regular_price IS NOT NULL AND p.regular_price > p.sell_price THEN 0 ELSE 1 END),
                      (p.regular_price - p.sell_price) DESC, 
                      p.id DESC
                    LIMIT $needed";
            $topProducts = $this->db->query($sql)->fetchAll();
            $crossSellingProducts = array_merge($crossSellingProducts, $topProducts);
        }

        return View::render('shop/success', [
            'orderId' => $orderId,
            'order' => $order,
            'orderItems' => $orderItems,
            'crossSellingProducts' => $crossSellingProducts
        ]);
    }

    public function setLanguage() {
        $lang = $_POST['lang'] ?? $_GET['lang'] ?? 'en';
        Lang::setLocale($lang);
        
        // Redirect back to previous page
        $referer = $_SERVER['HTTP_REFERER'] ?? '/sodai-dorkar/public/';
        // Remove any existing lang param from referer
        $referer = preg_replace('/([?&])lang=[^&]+/', '', $referer);
        header('Location: ' . $referer);
        exit;
    }

    public function cartData() {
        header('Content-Type: application/json');
        $cart = $_SESSION['cart'] ?? [];
        $cartCount = 0;
        $cartTotal = 0;
        foreach ($cart as $item) {
            $cartCount += $item['quantity'];
            $cartTotal += $item['price'] * $item['quantity'];
        }
        echo json_encode([
            'status' => 'success',
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal,
            'cart' => $cart
        ]);
        exit;
    }

    public function getZones() {
        header('Content-Type: application/json');
        $areaId = $_GET['area_id'] ?? null;
        if (!$areaId) {
            echo json_encode([]);
            exit;
        }
        $stmt = $this->db->query("SELECT id, name FROM zones WHERE area_id = ? ORDER BY name ASC", [$areaId]);
        echo json_encode($stmt->fetchAll());
        exit;
    }

    public function getPoints() {
        header('Content-Type: application/json');
        $zoneId = $_GET['zone_id'] ?? null;
        if (!$zoneId) {
            echo json_encode([]);
            exit;
        }
        $stmt = $this->db->query("SELECT id, name FROM points WHERE zone_id = ? ORDER BY name ASC", [$zoneId]);
        echo json_encode($stmt->fetchAll());
        exit;
    }
}

<?php

namespace Controllers;

use Core\Controller;
use Models\Product;
use Models\Vendor;

use Core\Middleware;

class ProductController extends Controller {
    
    public function __construct() {
        Middleware::permission('products');
    }
    
    public function index() {
        $productModel = new Product();
        $vendorModel = new Vendor();
        $categoryModel = new \Models\Category();

        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = 50;

        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'category_id' => $_GET['category_id'] ?? '',
            'vendor_id' => $_GET['vendor_id'] ?? '',
            'stock_status' => $_GET['stock_status'] ?? '',
            'availability_status' => $_GET['availability_status'] ?? '',
            'is_verified' => $_GET['is_verified'] ?? ''
        ];

        $totalProducts = $productModel->count($filters);
        $totalPages = max(1, (int)ceil($totalProducts / $perPage));
        if ($page > $totalPages && $totalProducts > 0) {
            $page = $totalPages;
        }
        $offset = max(0, ($page - 1) * $perPage);

        $products = $productModel->all($filters, $perPage, $offset);
        $vendors = $vendorModel->all();
        $categories = $categoryModel->all();
        $packagingUnits = (new \Models\PackagingUnit())->all();

        return $this->view('admin/products/index', [
            'title' => 'Products & Inventory', 
            'products' => $products,
            'vendors' => $vendors,
            'categories' => $categories,
            'packagingUnits' => $packagingUnits,
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'perPage' => $perPage
        ]);
    }

    public function dashboard() {
        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');

        // Inventory KPIs
        $totalProducts = (int)$db->query("SELECT COUNT(*) as cnt FROM products")->fetch()['cnt'];
        $inStock = (int)$db->query("SELECT COUNT(*) as cnt FROM products WHERE stock_qty >= 10")->fetch()['cnt'];
        $lowStock = (int)$db->query("SELECT COUNT(*) as cnt FROM products WHERE stock_qty > 0 AND stock_qty < 10")->fetch()['cnt'];
        $outOfStock = (int)$db->query("SELECT COUNT(*) as cnt FROM products WHERE stock_qty <= 0")->fetch()['cnt'];

        $valuation = $db->query("SELECT 
            COALESCE(SUM(buy_price * stock_qty), 0) as cost_val, 
            COALESCE(SUM(sell_price * stock_qty), 0) as retail_val 
            FROM products WHERE stock_qty > 0")->fetch();
        $costValuation = (float)($valuation['cost_val'] ?? 0);
        $retailValuation = (float)($valuation['retail_val'] ?? 0);
        $expectedProfit = $retailValuation - $costValuation;

        $unverifiedCount = (int)$db->query("SELECT COUNT(*) as cnt FROM products WHERE is_verified = 0")->fetch()['cnt'];
        $pendingAvailability = (int)$db->query("SELECT COUNT(*) as cnt FROM products WHERE availability_status = 'pending'")->fetch()['cnt'];

        // Category breakdown
        $catDistribution = $db->query("SELECT c.name, COUNT(p.id) as product_count, COALESCE(SUM(p.stock_qty), 0) as total_stock
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id
            GROUP BY c.id, c.name
            HAVING product_count > 0
            ORDER BY product_count DESC
            LIMIT 6")->fetchAll();

        // Recent products
        $recentProducts = $db->query("SELECT p.*, c.name as category_name, v.name as vendor_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN vendors v ON p.vendor_id = v.id 
            ORDER BY p.id DESC LIMIT 8")->fetchAll();

        // Low stock items
        $lowStockItems = $db->query("SELECT p.*, c.name as category_name, v.name as vendor_name, v.contact as vendor_contact 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN vendors v ON p.vendor_id = v.id 
            WHERE p.stock_qty < 10 
            ORDER BY p.stock_qty ASC LIMIT 8")->fetchAll();

        return $this->view('admin/products/dashboard', [
            'title' => 'Product Analytics & Inventory Dashboard',
            'totalProducts' => $totalProducts,
            'inStock' => $inStock,
            'lowStock' => $lowStock,
            'outOfStock' => $outOfStock,
            'costValuation' => $costValuation,
            'retailValuation' => $retailValuation,
            'expectedProfit' => $expectedProfit,
            'unverifiedCount' => $unverifiedCount,
            'pendingAvailability' => $pendingAvailability,
            'catDistribution' => $catDistribution,
            'recentProducts' => $recentProducts,
            'lowStockItems' => $lowStockItems
        ]);
    }

    private function generateSku() {
        return 'SKU-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
    }

    private function processVariants($variantsInput) {
        if (empty($variantsInput)) return null;
        if (is_string($variantsInput)) {
            $decoded = json_decode($variantsInput, true);
            return is_array($decoded) ? json_encode($decoded, JSON_UNESCAPED_UNICODE) : null;
        }
        if (is_array($variantsInput)) {
            $cleaned = [];
            foreach ($variantsInput as $v) {
                $title = trim($v['title'] ?? '');
                $qty = floatval($v['qty'] ?? 1);
                $price = floatval($v['price'] ?? 0);
                if (!empty($title) && $price >= 0) {
                    $cleaned[] = [
                        'title' => $title,
                        'qty' => $qty,
                        'price' => $price,
                        'is_default' => !empty($v['is_default']) ? 1 : 0
                    ];
                }
            }
            return !empty($cleaned) ? json_encode($cleaned, JSON_UNESCAPED_UNICODE) : null;
        }
        return null;
    }

    public function store() {
        $name = trim($_POST['name'] ?? '');
        $sku = trim($_POST['sku'] ?? '');
        
        // Auto-generate SKU if empty
        if (empty($sku)) {
            $sku = $this->generateSku();
        }

        $description = $_POST['description'] ?? '';
        $tags = trim($_POST['tags'] ?? '');
        $buy_price = floatval($_POST['buy_price'] ?? 0);
        $regular_price = !empty($_POST['regular_price']) ? floatval($_POST['regular_price']) : null;
        $discount_type = $_POST['discount_type'] ?? 'none';
        $discount_value = floatval($_POST['discount_value'] ?? 0);
        $sell_price = floatval($_POST['sell_price'] ?? 0);

        // Auto-detect discount if regular_price > sell_price
        if ($regular_price && $regular_price > $sell_price && $discount_type === 'none') {
            $discount_type = 'fixed';
            $discount_value = $regular_price - $sell_price;
        }

        $stock_qty = floatval($_POST['stock_qty'] ?? 0);
        $vendor_id = !empty($_POST['vendor_id']) ? intval($_POST['vendor_id']) : null;
        $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
        $brand_id = !empty($_POST['brand_id']) ? intval($_POST['brand_id']) : null;
        
        $availability_status = $_POST['availability_status'] ?? 'pending';
        $is_verified = !empty($_POST['is_verified']) ? 1 : 0;

        // Multi-unit fields
        $unit_type = $_POST['unit_type'] ?? 'piece';
        $base_unit = trim($_POST['base_unit'] ?? 'pcs');
        $purchase_unit = trim($_POST['purchase_unit'] ?? '');
        $purchase_unit_qty = floatval($_POST['purchase_unit_qty'] ?? 1.000);
        $selling_unit = trim($_POST['selling_unit'] ?? $base_unit);
        $unit_variants_json = $this->processVariants($_POST['variants'] ?? ($_POST['unit_variants_json'] ?? null));

        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $image_path = null;

        // Handle Image Upload safely
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $mime = '';
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
                finfo_close($finfo);
            } elseif (function_exists('mime_content_type')) {
                $mime = mime_content_type($_FILES['image']['tmp_name']);
            } else {
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $extMimes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
                $mime = $extMimes[$ext] ?? '';
            }

            if (in_array($mime, $allowedMimeTypes)) {
                $uploadDir = __DIR__ . '/../../public/uploads/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $cleanBase = preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['image']['name']));
                $fileName = time() . '_' . $cleanBase;
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $image_path = (!empty($base) ? $base : '') . '/uploads/products/' . $fileName;
                }
            }
        }

        if ($name && $sell_price >= 0) {
            try {
                $productModel = new Product();
                $productModel->create([
                    'name' => $name,
                    'sku' => ($sku !== '') ? $sku : null,
                    'description' => $description,
                    'tags' => $tags ?: null,
                    'buy_price' => $buy_price,
                    'regular_price' => $regular_price,
                    'discount_type' => $discount_type,
                    'discount_value' => $discount_value,
                    'sell_price' => $sell_price,
                    'stock_qty' => $stock_qty,
                    'vendor_id' => $vendor_id,
                    'category_id' => $category_id,
                    'brand_id' => $brand_id,
                    'image_path' => $image_path,
                    'unit_type' => $unit_type,
                    'base_unit' => $base_unit,
                    'purchase_unit' => $purchase_unit ?: null,
                    'purchase_unit_qty' => $purchase_unit_qty > 0 ? $purchase_unit_qty : 1.000,
                    'selling_unit' => $selling_unit ?: $base_unit,
                    'unit_variants_json' => $unit_variants_json,
                    'availability_status' => $availability_status,
                    'is_verified' => $is_verified
                ]);
                $_SESSION['success'] = "নতুন পণ্য সফলভাবে তৈরি করা হয়েছে!";
            } catch (\Throwable $e) {
                error_log("Product create failed: " . $e->getMessage());
                $_SESSION['error'] = "পণ্য তৈরি ব্যর্থ: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "পণ্যের নাম ও সঠিক মূল্য দিন!";
        }
        
        header("Location: {$base}/admin/products");
        exit;
    }

    public function edit() {
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: {$base}/admin/products");
            exit;
        }

        $productModel = new Product();
        $product = $productModel->find($id);

        if (!$product) {
            $_SESSION['error'] = "পণ্যটি খুঁজে পাওয়া যায়নি!";
            header("Location: {$base}/admin/products");
            exit;
        }

        $vendorModel = new Vendor();
        $vendors = $vendorModel->all();

        $categoryModel = new \Models\Category();
        $categories = $categoryModel->all();

        $brandModel = new \Models\Brand();
        $brands = $brandModel->all();

        // Decode variants if present
        $variants = [];
        if (!empty($product['unit_variants_json'])) {
            $variants = json_decode($product['unit_variants_json'], true) ?: [];
        }

        $packagingUnits = (new \Models\PackagingUnit())->all();

        return $this->view('admin/products/edit', [
            'title' => 'Edit Product',
            'product' => $product,
            'vendors' => $vendors,
            'categories' => $categories,
            'brands' => $brands,
            'variants' => $variants,
            'packagingUnits' => $packagingUnits
        ]);
    }

    public function update() {
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $id = $_POST['id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $sku = trim($_POST['sku'] ?? '');
        $description = $_POST['description'] ?? '';
        $tags = trim($_POST['tags'] ?? '');
        $buy_price = floatval($_POST['buy_price'] ?? 0);
        $regular_price = !empty($_POST['regular_price']) ? floatval($_POST['regular_price']) : null;
        $discount_type = $_POST['discount_type'] ?? 'none';
        $discount_value = floatval($_POST['discount_value'] ?? 0);
        $sell_price = floatval($_POST['sell_price'] ?? 0);

        // Auto-detect discount if regular_price > sell_price
        if ($regular_price && $regular_price > $sell_price && $discount_type === 'none') {
            $discount_type = 'fixed';
            $discount_value = $regular_price - $sell_price;
        }

        $stock_qty = floatval($_POST['stock_qty'] ?? 0);
        $vendor_id = !empty($_POST['vendor_id']) ? intval($_POST['vendor_id']) : null;
        $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
        $brand_id = !empty($_POST['brand_id']) ? intval($_POST['brand_id']) : null;

        $availability_status = $_POST['availability_status'] ?? 'pending';
        $is_verified = !empty($_POST['is_verified']) ? 1 : 0;

        // Multi-unit fields
        $unit_type = $_POST['unit_type'] ?? 'piece';
        $base_unit = trim($_POST['base_unit'] ?? 'pcs');
        $purchase_unit = trim($_POST['purchase_unit'] ?? '');
        $purchase_unit_qty = floatval($_POST['purchase_unit_qty'] ?? 1.000);
        $selling_unit = trim($_POST['selling_unit'] ?? $base_unit);
        $unit_variants_json = $this->processVariants($_POST['variants'] ?? ($_POST['unit_variants_json'] ?? null));

        $data = [
            'name' => $name,
            'sku' => ($sku !== '') ? $sku : null,
            'description' => $description,
            'tags' => $tags ?: null,
            'buy_price' => $buy_price,
            'regular_price' => $regular_price,
            'discount_type' => $discount_type,
            'discount_value' => $discount_value,
            'sell_price' => $sell_price,
            'stock_qty' => $stock_qty,
            'vendor_id' => $vendor_id,
            'category_id' => $category_id,
            'brand_id' => $brand_id,
            'unit_type' => $unit_type,
            'base_unit' => $base_unit,
            'purchase_unit' => $purchase_unit ?: null,
            'purchase_unit_qty' => $purchase_unit_qty > 0 ? $purchase_unit_qty : 1.000,
            'selling_unit' => $selling_unit ?: $base_unit,
            'unit_variants_json' => $unit_variants_json,
            'availability_status' => $availability_status,
            'is_verified' => $is_verified
        ];

        // Handle Image Upload safely
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $mime = '';
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
                finfo_close($finfo);
            } elseif (function_exists('mime_content_type')) {
                $mime = mime_content_type($_FILES['image']['tmp_name']);
            } else {
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $extMimes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
                $mime = $extMimes[$ext] ?? '';
            }

            if (in_array($mime, $allowedMimeTypes)) {
                $uploadDir = __DIR__ . '/../../public/uploads/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $cleanBase = preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['image']['name']));
                $fileName = time() . '_' . $cleanBase;
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $data['image_path'] = (!empty($base) ? $base : '') . '/uploads/products/' . $fileName;
                }
            }
        }

        if (!$id || !$name) {
            $_SESSION['error'] = "পণ্যের নাম ও আইডি আবশ্যক!";
            header("Location: {$base}/admin/products" . ($id ? "/edit?id={$id}" : ""));
            exit;
        }

        try {
            $productModel = new Product();
            $productModel->update($id, $data);
            $_SESSION['success'] = "পণ্যটি সফলভাবে আপডেট করা হয়েছে!";
        } catch (\Throwable $e) {
            error_log("Product update failed for ID {$id}: " . $e->getMessage());
            $_SESSION['error'] = "পণ্য আপডেট করতে সমস্যা হয়েছে: " . $e->getMessage();
            header("Location: {$base}/admin/products/edit?id={$id}");
            exit;
        }
        
        header("Location: {$base}/admin/products");
        exit;
    }

    public function destroy() {
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $id = $_POST['id'] ?? null;
        if ($id) {
            try {
                $productModel = new Product();
                $productModel->delete($id);
                $_SESSION['success'] = "পণ্যটি মুছে ফেলা হয়েছে!";
            } catch (\Throwable $e) {
                $_SESSION['error'] = "মুছে ফেলতে সমস্যা হয়েছে: " . $e->getMessage();
            }
        }
        header("Location: {$base}/admin/products");
        exit;
    }

    public function bulkImportIndex() {
        return $this->view('admin/products/bulk_import', ['title' => 'Bulk Import Products']);
    }

    public function bulkImportDemo() {
        $filename = "products_demo_" . date('Y-m-d') . ".csv";
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        
        // Add BOM to fix UTF-8 in Excel
        fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        // Write header
        fputcsv($output, [
            'Name', 'SKU', 'Description', 'Buy Price', 'Regular Price', 'Sell Price', 'Stock Qty', 
            'Category Path', 'Brand Name', 'Vendor Name', 'Unit Type', 'Base Unit', 
            'Purchase Unit', 'Purchase Unit Qty', 'Selling Unit'
        ]);
        
        // Write demo data
        fputcsv($output, [
            'Miniket Rice', 'SKU-123456', 'Premium miniket rice', '3000', '3500', '3200', '50',
            'Food > Rice > Miniket', 'Teer', 'Rahim Traders', 'sack_kg', 'kg',
            'Sack', '50', 'kg'
        ]);
        
        fputcsv($output, [
            'Rupchanda Soyabean Oil 5L', '', '5 Liter Soyabean Oil', '750', '820', '800', '100',
            'Food > Oil', 'Rupchanda', '', 'drum_liter', 'liter',
            'Carton', '4', 'liter'
        ]);

        fclose($output);
        exit;
    }

    public function bulkImportPreview() {
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            header('Location: /sodai-dorkar/public/admin/products/bulk-import');
            exit;
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $products = [];
        
        if (($handle = fopen($file, "r")) !== FALSE) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Heuristic to skip header row
                if ($row === 0 && (strtolower($data[0]) === 'name' || strtolower($data[0]) === 'product name')) {
                    $row++;
                    continue;
                }
                
                $sku = $data[1] ?? '';
                if (empty($sku)) {
                    $sku = $this->generateSku();
                }

                $products[] = [
                    'name' => $data[0] ?? '',
                    'sku' => $sku,
                    'description' => $data[2] ?? '',
                    'buy_price' => $data[3] ?? 0,
                    'regular_price' => $data[4] ?? '',
                    'sell_price' => $data[5] ?? 0,
                    'stock_qty' => $data[6] ?? 0,
                    'category_path' => $data[7] ?? '',
                    'brand_name' => $data[8] ?? '',
                    'vendor_name' => $data[9] ?? '',
                    'unit_type' => $data[10] ?? 'piece',
                    'base_unit' => $data[11] ?? 'pcs',
                    'purchase_unit' => $data[12] ?? '',
                    'purchase_unit_qty' => $data[13] ?? 1.000,
                    'selling_unit' => $data[14] ?? ($data[11] ?? 'pcs')
                ];
                $row++;
            }
            fclose($handle);
        }

        return $this->view('admin/products/bulk_preview', [
            'title' => 'Preview Import',
            'products' => $products
        ]);
    }

    public function bulkImportStore() {
        $products = $_POST['products'] ?? [];
        $productModel = new Product();
        $categoryModel = new \Models\Category();
        $brandModel = new \Models\Brand();
        $vendorModel = new Vendor();
        $count = 0;

        foreach ($products as $product) {
            if (isset($product['ignore']) && $product['ignore'] == 1) continue;
            
            if (!empty($product['name']) && !empty($product['sell_price'])) {
                
                // 1. Handle Brand
                $brandId = null;
                $brandName = trim($product['brand_name'] ?? '');
                if ($brandName) {
                    $brand = $brandModel->findByName($brandName);
                    if ($brand) {
                        $brandId = $brand['id'];
                    } else {
                        $brandId = $brandModel->create(['name' => $brandName]);
                    }
                }

                $categoryId = null;
                $categoryPath = trim($product['category_path'] ?? '');
                if ($categoryPath) {
                    $pathParts = array_map('trim', explode('>', $categoryPath));
                    $parentId = null;
                    
                    foreach ($pathParts as $catName) {
                        if (empty($catName)) continue;
                        
                        $allCats = $categoryModel->all();
                        $foundCat = null;
                        foreach($allCats as $c) {
                            if (strcasecmp($c['name'], $catName) === 0 && $c['parent_id'] == $parentId) {
                                $foundCat = $c;
                                break;
                            }
                        }

                        if ($foundCat) {
                            $parentId = $foundCat['id'];
                        } else {
                            $parentId = $categoryModel->create([
                                'name' => $catName,
                                'description' => '',
                                'parent_id' => $parentId
                            ]);
                        }
                    }
                    $categoryId = $parentId;
                }

                // 3. Handle Vendor
                $vendorId = null;
                $vendorName = trim($product['vendor_name'] ?? '');
                if ($vendorName) {
                    $allVendors = $vendorModel->all();
                    foreach ($allVendors as $v) {
                        if (strcasecmp($v['name'], $vendorName) === 0) {
                            $vendorId = $v['id'];
                            break;
                        }
                    }
                    if (!$vendorId) {
                        // Assuming Vendor model can be created with name and empty phone
                        $vendorId = $vendorModel->create(['name' => $vendorName, 'phone' => '']);
                    }
                }

                $baseUnit = !empty($product['base_unit']) ? trim($product['base_unit']) : 'pcs';

                $regularPrice = !empty($product['regular_price']) ? floatval($product['regular_price']) : null;
                $sellPrice = floatval($product['sell_price'] ?? 0);
                $discountType = 'none';
                $discountValue = 0;
                if ($regularPrice && $regularPrice > $sellPrice) {
                    $discountType = 'fixed';
                    $discountValue = $regularPrice - $sellPrice;
                }

                $productModel->create([
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'description' => $product['description'] ?? '',
                    'buy_price' => floatval($product['buy_price'] ?? 0),
                    'regular_price' => $regularPrice,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'sell_price' => $sellPrice,
                    'stock_qty' => floatval($product['stock_qty'] ?? 0),
                    'vendor_id' => $vendorId,
                    'category_id' => $categoryId,
                    'brand_id' => $brandId,
                    'image_path' => null,
                    'unit_type' => $product['unit_type'] ?? 'piece',
                    'base_unit' => $baseUnit,
                    'purchase_unit' => !empty($product['purchase_unit']) ? trim($product['purchase_unit']) : null,
                    'purchase_unit_qty' => floatval($product['purchase_unit_qty'] ?? 1.000),
                    'selling_unit' => !empty($product['selling_unit']) ? trim($product['selling_unit']) : $baseUnit,
                    'unit_variants_json' => null
                ]);
                $count++;
            }
        }
        
        header('Location: /sodai-dorkar/public/admin/products');
        exit;
    }

    public function bulkChunkImport() {
        header('Content-Type: application/json; charset=utf-8');

        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true);
        if (!$input) {
            $input = $_POST;
        }

        $items = $input['items'] ?? [];
        $duplicateAction = $input['duplicate_action'] ?? 'update'; // 'update' or 'skip'

        if (empty($items) || !is_array($items)) {
            echo json_encode([
                'success' => false,
                'message' => 'No items provided in this batch.',
                'added' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => 0,
                'error_messages' => []
            ]);
            exit;
        }

        $productModel = new Product();
        $categoryModel = new \Models\Category();
        $brandModel = new \Models\Brand();
        $vendorModel = new Vendor();

        $added = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;
        $errorMessages = [];

        $allCategories = $categoryModel->all();
        $allBrands = $brandModel->all();
        $allVendors = $vendorModel->all();

        foreach ($items as $idx => $p) {
            $name = trim($p['name'] ?? '');
            $sku = trim($p['sku'] ?? '');

            if (empty($name)) {
                $errors++;
                $errorMessages[] = "Row " . ($idx + 1) . ": Product name is missing.";
                continue;
            }

            try {
                // Check if product already exists (by SKU or Name)
                $existing = $productModel->findExisting($sku, $name);

                if ($existing) {
                    if ($duplicateAction === 'skip') {
                        $skipped++;
                        continue;
                    } else {
                        // Update stock and prices of existing product
                        $buyPrice = isset($p['buy_price']) && $p['buy_price'] !== '' ? floatval($p['buy_price']) : floatval($existing['buy_price']);
                        $regularPrice = !empty($p['regular_price']) ? floatval($p['regular_price']) : (!empty($existing['regular_price']) ? floatval($existing['regular_price']) : null);
                        $sellPrice = isset($p['sell_price']) && floatval($p['sell_price']) > 0 ? floatval($p['sell_price']) : floatval($existing['sell_price']);
                        $stockQty = isset($p['stock_qty']) && $p['stock_qty'] !== '' ? floatval($p['stock_qty']) : floatval($existing['stock_qty']);

                        $discountType = 'none';
                        $discountValue = 0;
                        if ($regularPrice && $regularPrice > $sellPrice) {
                            $discountType = 'fixed';
                            $discountValue = $regularPrice - $sellPrice;
                        }

                        $productModel->updateStockAndPrices($existing['id'], [
                            'buy_price' => $buyPrice,
                            'regular_price' => $regularPrice,
                            'discount_type' => $discountType,
                            'discount_value' => $discountValue,
                            'sell_price' => $sellPrice,
                            'stock_qty' => $stockQty,
                            'unit_type' => !empty($p['unit_type']) ? $p['unit_type'] : $existing['unit_type'],
                            'base_unit' => !empty($p['base_unit']) ? $p['base_unit'] : $existing['base_unit']
                        ]);

                        $updated++;
                        continue;
                    }
                }

                // New Product: Generate SKU if empty
                if (empty($sku)) {
                    $sku = $this->generateSku();
                }

                // 1. Brand Handling
                $brandId = null;
                $brandName = trim($p['brand_name'] ?? '');
                if ($brandName) {
                    foreach ($allBrands as $b) {
                        if (strcasecmp($b['name'], $brandName) === 0) {
                            $brandId = $b['id'];
                            break;
                        }
                    }
                    if (!$brandId) {
                        $brandId = $brandModel->create(['name' => $brandName]);
                        $allBrands = $brandModel->all();
                    }
                }

                // 2. Category Hierarchy Handling (e.g. "Food > Rice > Miniket")
                $categoryId = null;
                $categoryPath = trim($p['category_path'] ?? '');
                if ($categoryPath) {
                    $pathParts = array_map('trim', explode('>', $categoryPath));
                    $parentId = null;
                    foreach ($pathParts as $catName) {
                        if (empty($catName)) continue;
                        $foundCat = null;
                        foreach ($allCategories as $c) {
                            if (strcasecmp($c['name'], $catName) === 0 && ($c['parent_id'] == $parentId || ($parentId === null && empty($c['parent_id'])))) {
                                $foundCat = $c;
                                break;
                            }
                        }
                        if ($foundCat) {
                            $parentId = $foundCat['id'];
                            if (empty($foundCat['image_path']) && !empty($p['image_path'])) {
                                $catImg = $this->downloadAndSaveCategoryImage($p['image_path'], $catName);
                                if ($catImg) {
                                    $categoryModel->update($foundCat['id'], [
                                        'image_path' => $catImg, 
                                        'name' => $foundCat['name'], 
                                        'description' => $foundCat['description'], 
                                        'parent_id' => $foundCat['parent_id']
                                    ]);
                                    $allCategories = $categoryModel->all();
                                }
                            }
                        } else {
                            $catImg = !empty($p['image_path']) ? $this->downloadAndSaveCategoryImage($p['image_path'], $catName) : null;
                            $parentId = $categoryModel->create([
                                'name' => $catName,
                                'description' => $catName . ' - Auto-created via Bulk Import',
                                'parent_id' => $parentId,
                                'image_path' => $catImg
                            ]);
                            $allCategories = $categoryModel->all();
                        }
                    }
                    $categoryId = $parentId;
                }

                // 3. Vendor Handling
                $vendorId = null;
                $vendorName = trim($p['vendor_name'] ?? '');
                if ($vendorName) {
                    foreach ($allVendors as $v) {
                        if (strcasecmp($v['name'], $vendorName) === 0) {
                            $vendorId = $v['id'];
                            break;
                        }
                    }
                    if (!$vendorId) {
                        $vendorId = $vendorModel->create([
                            'name' => $vendorName, 
                            'contact' => $p['vendor_phone'] ?? '', 
                            'address' => ''
                        ]);
                        $allVendors = $vendorModel->all();
                    }
                }

                $buyPrice = floatval($p['buy_price'] ?? 0);
                $sellPrice = floatval($p['sell_price'] ?? 0);
                $regularPrice = !empty($p['regular_price']) ? floatval($p['regular_price']) : null;
                $stockQty = floatval($p['stock_qty'] ?? 0);

                $discountType = 'none';
                $discountValue = 0;
                if ($regularPrice && $regularPrice > $sellPrice) {
                    $discountType = 'fixed';
                    $discountValue = $regularPrice - $sellPrice;
                }

                $baseUnit = !empty($p['base_unit']) ? trim($p['base_unit']) : 'pcs';

                $productModel->create([
                    'name' => $name,
                    'sku' => $sku,
                    'description' => trim($p['description'] ?? ''),
                    'buy_price' => $buyPrice,
                    'regular_price' => $regularPrice,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'sell_price' => $sellPrice,
                    'stock_qty' => $stockQty,
                    'vendor_id' => $vendorId,
                    'category_id' => $categoryId,
                    'brand_id' => $brandId,
                    'image_path' => null,
                    'unit_type' => $p['unit_type'] ?? 'piece',
                    'base_unit' => $baseUnit,
                    'purchase_unit' => !empty($p['purchase_unit']) ? trim($p['purchase_unit']) : null,
                    'purchase_unit_qty' => floatval($p['purchase_unit_qty'] ?? 1.000),
                    'selling_unit' => !empty($p['selling_unit']) ? trim($p['selling_unit']) : $baseUnit,
                    'unit_variants_json' => null,
                    'is_verified' => 1,
                    'availability_status' => $stockQty > 0 ? 'in_stock' : 'out_of_stock'
                ]);

                $added++;
            } catch (\Exception $e) {
                $errors++;
                $errorMessages[] = "Error on '{$name}': " . $e->getMessage();
            }
        }

        echo json_encode([
            'success' => true,
            'added' => $added,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
            'error_messages' => $errorMessages
        ]);
        exit;
    }

    public function bulkStoreManual() {
        $products = $_POST['products'] ?? [];
        $productModel = new Product();
        $uploadDir = __DIR__ . '/../../public/uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        foreach ($products as $index => $p) {
            $name = trim($p['name'] ?? '');
            $sku = trim($p['sku'] ?? '');
            $buy_price = floatval($p['buy_price'] ?? 0);
            $regular_price = !empty($p['regular_price']) ? floatval($p['regular_price']) : null;
            $sell_price = floatval($p['sell_price'] ?? 0);
            $discount_type = $p['discount_type'] ?? 'none';
            $discount_value = floatval($p['discount_value'] ?? 0);

            // Auto-detect discount if regular_price > sell_price
            if ($regular_price && $regular_price > $sell_price && $discount_type === 'none') {
                $discount_type = 'fixed';
                $discount_value = $regular_price - $sell_price;
            }

            $stock_qty = floatval($p['stock_qty'] ?? 0);
            $category_id = !empty($p['category_id']) ? intval($p['category_id']) : null;
            $vendor_id = !empty($p['vendor_id']) ? intval($p['vendor_id']) : null;
            $description = trim($p['description'] ?? '');

            // Multi-unit fields
            $unit_type = $p['unit_type'] ?? 'piece';
            $base_unit = !empty($p['base_unit']) ? trim($p['base_unit']) : 'pcs';
            $purchase_unit = !empty($p['purchase_unit']) ? trim($p['purchase_unit']) : null;
            $purchase_unit_qty = floatval($p['purchase_unit_qty'] ?? 1.000);
            $selling_unit = !empty($p['selling_unit']) ? trim($p['selling_unit']) : $base_unit;

            // Handle Image Upload for this specific row
            $image_path = null;
            if (isset($_FILES['product_images']['name'][$index]) && $_FILES['product_images']['error'][$index] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['product_images']['tmp_name'][$index];
                
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $tmpName);
                finfo_close($finfo);

                if (in_array($mime, $allowedMimeTypes)) {
                    $origName = basename($_FILES['product_images']['name'][$index]);
                    $cleanName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $origName);
                    $fileName = time() . '_' . $index . '_' . $cleanName;
                    $targetPath = $uploadDir . $fileName;

                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $image_path = '/sodai-dorkar/public/uploads/products/' . $fileName;
                    }
                }
            }

            // Auto-generate variants for common bulk templates if requested
            $variantsJson = null;
            if (!empty($p['auto_variants']) && $p['auto_variants'] == 1) {
                if ($unit_type === 'sack_kg' || ($base_unit === 'kg' && $purchase_unit_qty > 1)) {
                    // e.g. 1 kg, 5 kg, Full sack
                    $variants = [
                        ['title' => '১ কেজি', 'qty' => 1, 'price' => $sell_price, 'is_default' => 1],
                        ['title' => '৫ কেজি', 'qty' => 5, 'price' => round($sell_price * 5 * 0.98), 'is_default' => 0]
                    ];
                    if ($purchase_unit_qty > 5) {
                        $variants[] = [
                            'title' => (floor($purchase_unit_qty) == $purchase_unit_qty ? intval($purchase_unit_qty) : $purchase_unit_qty) . ' কেজি ' . ($purchase_unit ?: 'বস্তা'),
                            'qty' => $purchase_unit_qty,
                            'price' => round($sell_price * $purchase_unit_qty * 0.95),
                            'is_default' => 0
                        ];
                    }
                    $variantsJson = json_encode($variants, JSON_UNESCAPED_UNICODE);
                } elseif ($unit_type === 'drum_liter' || ($base_unit === 'liter' && $purchase_unit_qty > 1)) {
                    // e.g. 500 ml, 1 liter, 5 liter
                    $variants = [
                        ['title' => '৫০০ মিলি', 'qty' => 0.5, 'price' => round($sell_price * 0.5 * 1.05), 'is_default' => 0],
                        ['title' => '১ লিটার', 'qty' => 1, 'price' => $sell_price, 'is_default' => 1],
                        ['title' => '৫ লিটার', 'qty' => 5, 'price' => round($sell_price * 5 * 0.98), 'is_default' => 0]
                    ];
                    $variantsJson = json_encode($variants, JSON_UNESCAPED_UNICODE);
                } elseif ($unit_type === 'box_piece' || ($base_unit === 'pcs' && $purchase_unit_qty > 1)) {
                    // e.g. 1 piece, 4 piece pack, 1 full box
                    $variants = [
                        ['title' => '১ পিস', 'qty' => 1, 'price' => $sell_price, 'is_default' => 1]
                    ];
                    if ($purchase_unit_qty >= 4) {
                        $variants[] = ['title' => '৪ পিস প্যাক', 'qty' => 4, 'price' => round($sell_price * 4 * 0.97), 'is_default' => 0];
                    }
                    if ($purchase_unit_qty > 4) {
                        $variants[] = ['title' => '১ বক্স (' . intval($purchase_unit_qty) . ' পিস)', 'qty' => $purchase_unit_qty, 'price' => round($sell_price * $purchase_unit_qty * 0.93), 'is_default' => 0];
                    }
                    $variantsJson = json_encode($variants, JSON_UNESCAPED_UNICODE);
                }
            }

            if (empty($sku)) {
                $sku = $this->generateSku();
            }

            if (!empty($name) && $sell_price >= 0) {
                $productModel->create([
                    'name' => $name,
                    'sku' => $sku,
                    'description' => $description,
                    'buy_price' => $buy_price,
                    'regular_price' => $regular_price,
                    'discount_type' => $discount_type,
                    'discount_value' => $discount_value,
                    'sell_price' => $sell_price,
                    'stock_qty' => $stock_qty,
                    'vendor_id' => $vendor_id,
                    'category_id' => $category_id,
                    'image_path' => $image_path,
                    'unit_type' => $unit_type,
                    'base_unit' => $base_unit,
                    'purchase_unit' => $purchase_unit,
                    'purchase_unit_qty' => $purchase_unit_qty > 0 ? $purchase_unit_qty : 1.000,
                    'selling_unit' => $selling_unit,
                    'unit_variants_json' => $variantsJson
                ]);
            }
        }

        header('Location: /sodai-dorkar/public/admin/products');
        exit;
    }

    public function verificationIndex() {
        $categoryId = $_GET['category'] ?? null;
        
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.is_verified = 0";
        
        $params = [];
        if ($categoryId) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }
        
        $sql .= " ORDER BY p.id DESC";
        
        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        $stmt = $db->query($sql, $params);
        $products = $stmt->fetchAll();
        
        $categoryModel = new \Models\Category();
        $categories = $categoryModel->all();
        
        return $this->view('admin/products/verification', [
            'title' => 'Price Verification',
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => $categoryId
        ]);
    }

    public function verificationUpdate() {
        $products = $_POST['products'] ?? [];
        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        
        foreach ($products as $id => $data) {
            $buyPrice = floatval($data['buy_price'] ?? 0);
            $sellPrice = floatval($data['sell_price'] ?? 0);
            
            $sql = "UPDATE products SET buy_price = :buy_price, sell_price = :sell_price, is_verified = 1 WHERE id = :id";
            $db->query($sql, [
                'buy_price' => $buyPrice,
                'sell_price' => $sellPrice,
                'id' => $id
            ]);
        }
        
        header('Location: /sodai-dorkar/public/admin/products/verification');
        exit;
    }

    public function onDemandIndex() {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.demand_percentage > 0
                ORDER BY p.demand_percentage DESC, p.id DESC";
        
        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        $stmt = $db->query($sql);
        $products = $stmt->fetchAll();
        
        return $this->view('admin/products/on_demand', [
            'title' => 'On-Demand Products',
            'products' => $products
        ]);
    }

    public function availabilityIndex() {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.availability_status = 'pending'
                ORDER BY p.id DESC";
        
        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        $stmt = $db->query($sql);
        $products = $stmt->fetchAll();
        
        return $this->view('admin/products/availability', [
            'title' => 'Availability Status',
            'products' => $products
        ]);
    }

    public function availabilityUpdate() {
        $selectedIds = $_POST['selected_products'] ?? [];
        $allIds = $_POST['all_products'] ?? [];
        
        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        
        // Items not selected become out_of_stock
        $outOfStockIds = array_diff($allIds, $selectedIds);
        
        if (!empty($selectedIds)) {
            $inStockStr = implode(',', array_map('intval', $selectedIds));
            $db->query("UPDATE products SET availability_status = 'in_stock' WHERE id IN ($inStockStr)");
        }
        
        if (!empty($outOfStockIds)) {
            $outOfStockStr = implode(',', array_map('intval', $outOfStockIds));
            $db->query("UPDATE products SET availability_status = 'out_of_stock' WHERE id IN ($outOfStockStr)");
        }
        
        header('Location: /sodai-dorkar/public/admin/products/availability');
        exit;
    }

    public function bulkStatusUpdate() {
        if (!\Core\CSRF::validate($_POST['csrf_token'] ?? '')) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'CSRF security token invalid!']);
                exit;
            }
            $_SESSION['error'] = 'নিরাপত্তা ত্রুটি! আবার চেষ্টা করুন।';
            header('Location: /sodai-dorkar/public/admin/products');
            exit;
        }

        $productIds = $_POST['product_ids'] ?? [];
        if (is_string($productIds)) {
            $productIds = explode(',', $productIds);
        }
        $productIds = array_filter(array_map('intval', (array)$productIds));

        if (empty($productIds)) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'কোনো পণ্য সিলেক্ট করা হয়নি!']);
                exit;
            }
            $_SESSION['error'] = 'কোনো পণ্য সিলেক্ট করা হয়নি!';
            header('Location: /sodai-dorkar/public/admin/products');
            exit;
        }

        $availabilityStatus = trim($_POST['availability_status'] ?? '');
        $isVerified = isset($_POST['is_verified']) && $_POST['is_verified'] !== '' && $_POST['is_verified'] !== 'no_change' ? intval($_POST['is_verified']) : null;

        $updates = [];
        $params = [];

        $validStatuses = ['in_stock', 'out_of_stock', 'pending'];
        if (in_array($availabilityStatus, $validStatuses)) {
            $updates[] = "availability_status = :avail_status";
            $params['avail_status'] = $availabilityStatus;
        }

        if ($isVerified !== null && in_array($isVerified, [0, 1])) {
            $updates[] = "is_verified = :is_verified";
            $params['is_verified'] = $isVerified;
        }

        if (empty($updates)) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'কোনো স্ট্যাটাস নির্বাচন করা হয়নি!']);
                exit;
            }
            $_SESSION['error'] = 'কোনো স্ট্যাটাস পরিবর্তন নির্বাচন করা হয়নি!';
            header('Location: /sodai-dorkar/public/admin/products');
            exit;
        }

        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        $idPlaceholders = implode(',', array_map('intval', $productIds));
        $updateSql = "UPDATE products SET " . implode(', ', $updates) . " WHERE id IN ($idPlaceholders)";
        $db->query($updateSql, $params);

        $count = count($productIds);
        $statusLabels = [
            'in_stock' => 'ইন স্টক (In Stock)',
            'out_of_stock' => 'স্টক শেষ (Out of Stock)',
            'pending' => 'পেন্ডিং (Pending)'
        ];
        $updatedText = isset($statusLabels[$availabilityStatus]) ? $statusLabels[$availabilityStatus] : 'স্ট্যাটাস';
        $msg = "সফলভাবে {$count} টি পণ্যের স্ট্যাটাস [{$updatedText}] এ আপডেট করা হয়েছে!";

        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => $msg,
                'count' => $count,
                'product_ids' => $productIds,
                'availability_status' => $availabilityStatus,
                'is_verified' => $isVerified
            ]);
            exit;
        }

        $_SESSION['success'] = $msg;
        header('Location: /sodai-dorkar/public/admin/products');
        exit;
    }

    private function isAjaxRequest() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
            || (isset($_POST['is_ajax']) && $_POST['is_ajax'] == '1');
    }
    public function procurementIndex() {
        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        
        $sql = "SELECT oi.product_id, oi.unit_title, SUM(oi.quantity) as total_needed, 
                       p.name as product_name, p.image_path, p.stock_qty, p.buy_price, 
                       v.name as vendor_name, v.id as vendor_id, v.contact as vendor_phone
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                JOIN products p ON oi.product_id = p.id
                LEFT JOIN vendors v ON p.vendor_id = v.id
                WHERE o.status = 'pending' AND p.stock_qty <= 0
                GROUP BY oi.product_id, oi.unit_title, p.name, p.image_path, p.stock_qty, p.buy_price, v.name, v.id, v.contact
                ORDER BY v.name ASC, p.name ASC";
        
        $stmt = $db->query($sql);
        $items = $stmt->fetchAll();

        // Group by vendor for display
        $groupedItems = [];
        foreach ($items as $item) {
            $vid = $item['vendor_id'] ?: 'unknown';
            if (!isset($groupedItems[$vid])) {
                $groupedItems[$vid] = [
                    'vendor_name' => $item['vendor_name'] ?: 'No Vendor Assigned',
                    'vendor_phone' => $item['vendor_phone'] ?: '',
                    'products' => []
                ];
            }
            $groupedItems[$vid]['products'][] = $item;
        }

        return $this->view('admin/products/procurement', [
            'title' => 'Procurement List',
            'groupedItems' => $groupedItems
        ]);
    }

    public function imageFinderIndex() {
        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        $categoryModel = new \Models\Category();
        $categories = $categoryModel->all();

        $filter = $_GET['filter'] ?? 'missing'; // 'missing', 'has_image', 'all'
        $categoryId = !empty($_GET['category_id']) ? intval($_GET['category_id']) : null;
        $search = trim($_GET['search'] ?? '');

        // KPI Counts
        $totalProducts = (int)$db->query("SELECT COUNT(*) as cnt FROM products")->fetch()['cnt'];
        $missingCount = (int)$db->query("SELECT COUNT(*) as cnt FROM products WHERE image_path IS NULL OR TRIM(image_path) = ''")->fetch()['cnt'];
        $hasImageCount = $totalProducts - $missingCount;

        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE 1=1";
        $params = [];

        if ($filter === 'missing') {
            $sql .= " AND (p.image_path IS NULL OR TRIM(p.image_path) = '')";
        } elseif ($filter === 'has_image') {
            $sql .= " AND (p.image_path IS NOT NULL AND TRIM(p.image_path) != '')";
        }

        if ($categoryId) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }

        if (!empty($search)) {
            $sql .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $sql .= " ORDER BY p.id DESC";

        $products = $db->query($sql, $params)->fetchAll();

        return $this->view('admin/products/image_finder', [
            'title' => 'Auto Image Finder (ওয়েব থেকে ছবি অনুসন্ধান ও সেভ)',
            'products' => $products,
            'categories' => $categories,
            'totalProducts' => $totalProducts,
            'missingCount' => $missingCount,
            'hasImageCount' => $hasImageCount,
            'filter' => $filter,
            'categoryId' => $categoryId,
            'search' => $search
        ]);
    }

    public function searchWebImages() {
        header('Content-Type: application/json; charset=utf-8');
        $query = trim($_GET['query'] ?? '');
        $source = strtolower(trim($_GET['source'] ?? 'all'));

        if (empty($query)) {
            echo json_encode(['success' => false, 'message' => 'সার্চ কিওয়ার্ড দেওয়া হয়নি (Query is empty)', 'results' => []], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $results = [];

        // 1. Shwapno API
        if ($source === 'all' || $source === 'shwapno') {
            $shwapnoResults = $this->fetchShwapnoImages($query);
            foreach ($shwapnoResults as $item) {
                $results[] = $item;
            }

            // Broader query if few results
            if (count($shwapnoResults) < 4) {
                $cleanedQuery = trim(preg_replace('/\b(\d+\s*(?:kg|gm|g|ltr|ml|liter|কেজি|গ্রাম|লিটার|বস্তা|প্যাকেট))\b/iu', '', $query));
                if ($cleanedQuery && strtolower($cleanedQuery) !== strtolower($query)) {
                    $moreShwapno = $this->fetchShwapnoImages($cleanedQuery);
                    $existingImages = array_column($results, 'image');
                    foreach ($moreShwapno as $item) {
                        if (!in_array($item['image'], $existingImages)) {
                            $results[] = $item;
                            $existingImages[] = $item['image'];
                        }
                    }
                }
            }
        }

        // 2. Wikimedia Commons API (Great for raw groceries, spices, fruits, vegetables, fish, etc.)
        if ($source === 'all' || $source === 'wikimedia') {
            $wikiResults = $this->fetchWikimediaImages($query);
            $existingImages = array_column($results, 'image');
            foreach ($wikiResults as $item) {
                if (!in_array($item['image'], $existingImages)) {
                    $results[] = $item;
                    $existingImages[] = $item['image'];
                }
            }
        }

        // 3. OpenFoodFacts fallback
        if ($source === 'all' || $source === 'openfoodfacts') {
            $offResults = $this->fetchOpenFoodFactsImages($query);
            $existingImages = array_column($results, 'image');
            foreach ($offResults as $item) {
                if (!in_array($item['image'], $existingImages)) {
                    $results[] = $item;
                    $existingImages[] = $item['image'];
                }
            }
        }

        echo json_encode([
            'success' => true,
            'query' => $query,
            'source' => $source,
            'count' => count($results),
            'results' => array_slice($results, 0, 24)
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function fetchShwapnoImages($query) {
        $ch = curl_init("https://www.shwapno.com/api/search?q=" . urlencode($query));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Referer: https://www.shwapno.com/"
        ]);
        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $items = [];
        if ($code === 200 && $res) {
            $data = json_decode($res, true);
            if (!empty($data['products']) && is_array($data['products'])) {
                foreach ($data['products'] as $item) {
                    $p = $item['product'] ?? $item;
                    $largeImg = $p['picture']['largeDeviceUrl']['imageUrl'] 
                        ?? $p['picture']['largeDeviceUrl']['fullSizeImageUrl'] 
                        ?? '';
                    $smallImg = $p['picture']['smallDeviceUrl']['imageUrl'] 
                        ?? $largeImg;

                    if ($largeImg) {
                        $priceStr = '';
                        if (!empty($p['price']['price'])) {
                            $priceStr = $p['price']['price'];
                        } elseif (!empty($p['price']['priceValue'])) {
                            $priceStr = '৳' . $p['price']['priceValue'];
                        }

                        $items[] = [
                            'title' => $p['name'] ?? $query,
                            'image' => $largeImg,
                            'thumbnail' => $smallImg ?: $largeImg,
                            'source' => 'Shwapno Official',
                            'sku' => $p['sku'] ?? '',
                            'price' => $priceStr
                        ];
                    }
                }
            }
        }
        return $items;
    }

    private function fetchOpenFoodFactsImages($query) {
        $ch = curl_init("https://world.openfoodfacts.org/cgi/search.pl?search_terms=" . urlencode($query) . "&search_simple=1&action=process&json=1&page_size=6");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, "SodaiDorkar/1.0 (admin@sodaidorkar.com)");
        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $items = [];
        if ($code === 200 && $res) {
            $data = json_decode($res, true);
            if (!empty($data['products']) && is_array($data['products'])) {
                foreach ($data['products'] as $p) {
                    $img = $p['image_front_url'] ?? $p['image_url'] ?? '';
                    if ($img) {
                        $items[] = [
                            'title' => $p['product_name'] ?? $query,
                            'image' => $img,
                            'thumbnail' => $p['image_front_small_url'] ?? $img,
                            'source' => 'Web / Grocery DB',
                            'sku' => $p['code'] ?? '',
                            'price' => ''
                        ];
                    }
                }
            }
        }
        return $items;
    }

    private function fetchWikimediaImages($query) {
        $clean = trim(preg_replace('/\b(\d+\s*(?:kg|gm|g|ltr|ml|liter|কেজি|গ্রাম|লিটার|বস্তা|প্যাকেট))\b/iu', '', $query));
        $searchTerms = $clean ?: $query;
        $url = "https://commons.wikimedia.org/w/api.php?action=query&generator=search&gsrnamespace=6&gsrsearch=" . urlencode($searchTerms) . "&gsrlimit=10&prop=imageinfo&iiprop=url|thumburl&iiurlwidth=400&format=json";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "SodaiDorkar/1.0 (info@freshemart.com)");
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        $items = [];
        if ($res) {
            $data = json_decode($res, true);
            if (!empty($data['query']['pages'])) {
                foreach ($data['query']['pages'] as $page) {
                    if (!empty($page['imageinfo'][0])) {
                        $info = $page['imageinfo'][0];
                        $imgUrl = $info['url'] ?? '';
                        $thumbUrl = $info['thumburl'] ?? $imgUrl;
                        if ($imgUrl && preg_match('/\.(jpe?g|png|webp)/i', $imgUrl)) {
                            $rawTitle = preg_replace('/^File:/i', '', $page['title'] ?? $query);
                            $cleanTitle = preg_replace('/\.(jpe?g|png|webp)$/i', '', $rawTitle);
                            $cleanTitle = str_replace(['_', '-'], ' ', $cleanTitle);
                            
                            $items[] = [
                                'title' => $cleanTitle,
                                'image' => $imgUrl,
                                'thumbnail' => $thumbUrl,
                                'source' => 'Wikimedia Commons',
                                'sku' => '',
                                'price' => ''
                            ];
                        }
                    }
                }
            }
        }
        return $items;
    }

    public function saveWebImage() {
        header('Content-Type: application/json; charset=utf-8');

        // Check CSRF token from header or post
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
        if (!\Core\CSRF::verify($csrfToken)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'CSRF security token mismatch! অনুগ্রহ করে পেজ রিফ্রেশ করুন।'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $productId = intval($_POST['product_id'] ?? 0);
        $imageUrl = trim($_POST['image_url'] ?? '');

        if (!$productId || empty($imageUrl)) {
            echo json_encode(['success' => false, 'message' => 'পণ্য আইডি অথবা ইমেজ লিংক সঠিক নয়!'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Validate URL format
        if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            echo json_encode(['success' => false, 'message' => 'অবৈধ ইমেজ ইউআরএল (Invalid URL)'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Download the image using cURL
        $ch = curl_init($imageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36");
        curl_setopt($ch, CURLOPT_REFERER, "https://www.shwapno.com/");
        $imageData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 200 || empty($imageData)) {
            echo json_encode(['success' => false, 'message' => 'ইমেজ ডাউনলোড করা যায়নি (HTTP ' . $httpCode . '): ' . $curlErr], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Validate image data with finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $imageData);
        finfo_close($finfo);

        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif'
        ];

        if (!isset($allowedMimes[$mime])) {
            // Check if content-type header helps
            if (stripos($contentType, 'webp') !== false) {
                $ext = 'webp';
            } elseif (stripos($contentType, 'png') !== false) {
                $ext = 'png';
            } elseif (stripos($contentType, 'jpeg') !== false || stripos($contentType, 'jpg') !== false) {
                $ext = 'jpg';
            } else {
                echo json_encode(['success' => false, 'message' => 'অসমর্থিত ইমেজ ফরম্যাট (' . $mime . ')! শুধুমাত্র JPG, PNG, WEBP গ্রহণযোগ্য।'], JSON_UNESCAPED_UNICODE);
                exit;
            }
        } else {
            $ext = $allowedMimes[$mime];
        }

        $uploadDir = __DIR__ . '/../../public/uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = 'prod_' . $productId . '_' . time() . '_' . substr(md5(uniqid()), 0, 6) . '.' . $ext;
        $targetFile = $uploadDir . $fileName;

        if (file_put_contents($targetFile, $imageData) === false) {
            echo json_encode(['success' => false, 'message' => 'সার্ভারে ইমেজ ফাইল সংরক্ষণ করা যায়নি (Folder permission issue)!'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Relative path calculation for localhost and live server
        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $imagePath = ($base ?: '') . '/uploads/products/' . $fileName;

        // Update database
        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        $db->query("UPDATE products SET image_path = ? WHERE id = ?", [$imagePath, $productId]);

        echo json_encode([
            'success' => true,
            'message' => 'ইমেজ সফলভাবে ডাউনলোড ও সেভ করা হয়েছে!',
            'image_path' => $imagePath,
            'product_id' => $productId
        ], JSON_UNESCAPED_UNICODE);
            exit;
    }

    public function autoMatchSingle() {
        header('Content-Type: application/json; charset=utf-8');

        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
        if (!\Core\CSRF::verify($csrfToken)) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'CSRF security token mismatch!'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $productId = intval($_POST['product_id'] ?? 0);
        if (!$productId) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid product ID'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        $product = $db->query("SELECT * FROM products WHERE id = ?", [$productId])->fetch();

        if (!$product) {
            echo json_encode(['status' => 'error', 'message' => 'পণ্য পাওয়া যায়নি'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // If product already has an image, skip
        if (!empty($product['image_path']) && trim($product['image_path']) !== '') {
            echo json_encode([
                'status' => 'already_has_image',
                'product_id' => $productId,
                'product_name' => $product['name'],
                'image_path' => $product['image_path'],
                'message' => 'পূর্বে থেকেই ছবি যুক্ত আছে'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $productName = trim($product['name']);
        
        // 1. Fetch candidates from Shwapno
        $candidates = $this->fetchShwapnoImages($productName);

        // If 0 candidates and name has common package noise, try broader query
        if (empty($candidates)) {
            $cleaned = trim(preg_replace('/\b(\d+\s*(?:kg|gm|g|ltr|ml|liter|কেজি|গ্রাম|লিটার|বস্তা|প্যাকেট))\b/iu', '', $productName));
            if ($cleaned && strtolower($cleaned) !== strtolower($productName)) {
                $candidates = $this->fetchShwapnoImages($cleaned);
            }
        }

        if (empty($candidates)) {
            echo json_encode([
                'status' => 'skipped',
                'product_id' => $productId,
                'product_name' => $productName,
                'reason' => 'ওয়েবে কোনো ফলাফল পাওয়া যায়নি'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 2. Evaluate each candidate for a 100% exact match
        $matchedCandidate = null;
        $matchedReason = '';

        foreach ($candidates as $cand) {
            $matchResult = $this->evaluateExactMatch($productName, $cand['title']);
            if ($matchResult['matched']) {
                $matchedCandidate = $cand;
                $matchedReason = $matchResult['reason'];
                break;
            }
        }

        // If no 100% match found, SKIP!
        if (!$matchedCandidate) {
            echo json_encode([
                'status' => 'skipped',
                'product_id' => $productId,
                'product_name' => $productName,
                'reason' => '১০০% নিশ্চিত মিল নেই (স্কিপ করা হয়েছে)',
                'top_candidate' => $candidates[0]['title'] ?? null
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 3. Download & Save the matched image
        $downloadResult = $this->downloadAndSaveImageFile($productId, $matchedCandidate['image']);
        if (!$downloadResult['success']) {
            echo json_encode([
                'status' => 'error',
                'product_id' => $productId,
                'product_name' => $productName,
                'message' => $downloadResult['message']
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Update database
        $db->query("UPDATE products SET image_path = ? WHERE id = ?", [$downloadResult['image_path'], $productId]);

        echo json_encode([
            'status' => 'matched',
            'product_id' => $productId,
            'product_name' => $productName,
            'matched_title' => $matchedCandidate['title'],
            'image_path' => $downloadResult['image_path'],
            'reason' => $matchedReason,
            'message' => '১০০% মিলেছে এবং সেভ করা হয়েছে!'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function evaluateExactMatch($dbName, $sourceTitle) {
        $norm1 = $this->normalizeProductString($dbName);
        $norm2 = $this->normalizeProductString($sourceTitle);

        // 1. Direct identical match
        if ($norm1 === $norm2) {
            return ['matched' => true, 'confidence' => 100, 'reason' => 'Exact string match'];
        }

        // 2. Strict quantity/numeric tokens check: If DB has 5L and source has 1L or 2L, REJECT!
        $q1 = $this->extractQuantityTokens($norm1);
        $q2 = $this->extractQuantityTokens($norm2);
        if (!empty($q1) && !empty($q2)) {
            if (array_diff($q1, $q2) || array_diff($q2, $q1)) {
                return ['matched' => false, 'confidence' => 0, 'reason' => 'Package size mismatch (' . implode(',', $q1) . ' vs ' . implode(',', $q2) . ')'];
            }
        }

        // 3. Token coverage check: Every word (>1 char) in DB name must exist in source title
        $tokens1 = explode(' ', $norm1);
        $tokens2 = explode(' ', $norm2);
        
        $missingTokens = [];
        foreach ($tokens1 as $t) {
            if (mb_strlen($t, 'UTF-8') <= 1) continue;
            if (!in_array($t, $tokens2)) {
                $missingTokens[] = $t;
            }
        }

        if (empty($missingTokens)) {
            return ['matched' => true, 'confidence' => 98, 'reason' => 'All tokens verified in source title'];
        }

        // 4. Similarity ratio
        similar_text($norm1, $norm2, $percent);
        if ($percent >= 92 && empty($missingTokens)) {
            return ['matched' => true, 'confidence' => round($percent), 'reason' => 'High similarity score (' . round($percent) . '%)'];
        }

        return ['matched' => false, 'confidence' => round($percent), 'reason' => 'Missing tokens'];
    }

    private function normalizeProductString($str) {
        $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $str);
        // Standardize units
        $str = preg_replace('/\b(\d+)\s*(l|ltr|liter|liters|লিটার)\b/u', '$1ltr', $str);
        $str = preg_replace('/\b(\d+)\s*(g|gm|gms|gram|grams|গ্রাম)\b/u', '$1gm', $str);
        $str = preg_replace('/\b(\d+)\s*(kg|kgs|কেজি)\b/u', '$1kg', $str);
        $str = preg_replace('/\b(\d+)\s*(ml|মিলি)\b/u', '$1ml', $str);
        return trim(preg_replace('/\s+/', ' ', $str));
    }

    private function extractQuantityTokens($str) {
        preg_match_all('/\b\d+(?:kg|gm|g|ltr|l|ml|pcs|pack|pieces|কেজি|গ্রাম|লিটার|মিলি|পিস|প্যাকেট|টি)?\b/iu', $str, $matches);
        $tokens = [];
        foreach ($matches[0] as $m) {
            $m = trim($m);
            if ($m !== '') $tokens[] = $m;
        }
        return $tokens;
    }

    private function downloadAndSaveImageFile($productId, $imageUrl) {
        $ch = curl_init($imageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36");
        curl_setopt($ch, CURLOPT_REFERER, "https://www.shwapno.com/");
        $imageData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 200 || empty($imageData)) {
            return ['success' => false, 'message' => 'ইমেজ ডাউনলোড করা যায়নি (HTTP ' . $httpCode . '): ' . $curlErr];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $imageData);
        finfo_close($finfo);

        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif'
        ];

        if (!isset($allowedMimes[$mime])) {
            if (stripos($contentType, 'webp') !== false) {
                $ext = 'webp';
            } elseif (stripos($contentType, 'png') !== false) {
                $ext = 'png';
            } elseif (stripos($contentType, 'jpeg') !== false || stripos($contentType, 'jpg') !== false) {
                $ext = 'jpg';
            } else {
                return ['success' => false, 'message' => 'অসমর্থিত ফরম্যাট (' . $mime . ')'];
            }
        } else {
            $ext = $allowedMimes[$mime];
        }

        $uploadDir = __DIR__ . '/../../public/uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = 'prod_' . $productId . '_' . time() . '_' . substr(md5(uniqid()), 0, 6) . '.' . $ext;
        $targetFile = $uploadDir . $fileName;

        if (file_put_contents($targetFile, $imageData) === false) {
            return ['success' => false, 'message' => 'ফাইল রাইট করা যায়নি'];
        }

        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $imagePath = ($base ?: '') . '/uploads/products/' . $fileName;

        return ['success' => true, 'image_path' => $imagePath];
    }

    public function downloadAndSaveCategoryImage($imageUrl, $categoryName = '') {
        $imageUrl = trim($imageUrl);
        if (empty($imageUrl)) return null;

        $base = (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
        $uploadDir = __DIR__ . '/../../public/uploads/categories/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // If already a local file path on server
        if (strpos($imageUrl, 'http://') !== 0 && strpos($imageUrl, 'https://') !== 0) {
            $cleanLocal = preg_replace('#^/sodai-dorkar/public/#', '', $imageUrl);
            $cleanLocal = ltrim($cleanLocal, '/');
            $fullLocalSource = __DIR__ . '/../../public/' . $cleanLocal;
            if (file_exists($fullLocalSource)) {
                $ext = pathinfo($fullLocalSource, PATHINFO_EXTENSION) ?: 'webp';
                $newFileName = 'cat_' . time() . '_' . substr(md5(uniqid($categoryName)), 0, 6) . '.' . $ext;
                if (copy($fullLocalSource, $uploadDir . $newFileName)) {
                    return ($base ?: '') . '/uploads/categories/' . $newFileName;
                }
            }
        }

        if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return null;
        }

        // Download image from remote web via cURL
        $ch = curl_init($imageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36");
        curl_setopt($ch, CURLOPT_REFERER, "https://www.shwapno.com/");
        $imageData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($httpCode !== 200 || empty($imageData)) {
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $imageData);
        finfo_close($finfo);

        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif'
        ];

        $ext = $allowedMimes[$mime] ?? (stripos($contentType, 'webp') !== false ? 'webp' : 'jpg');
        $fileName = 'cat_' . time() . '_' . substr(md5(uniqid($categoryName)), 0, 6) . '.' . $ext;
        $targetFile = $uploadDir . $fileName;

        if (file_put_contents($targetFile, $imageData) !== false) {
            return ($base ?: '') . '/uploads/categories/' . $fileName;
        }

        return null;
    }

    public function ensureCategoryExistsWithImage($categoryName, $imageUrl = null, $parentId = null, $description = null) {
        $categoryName = trim($categoryName);
        if (empty($categoryName)) {
            return null;
        }

        $db = null;
        try {
            $cfg = require __DIR__ . '/../../config/database.php';
            $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['dbname']};charset=utf8mb4";
            $pdo = new \PDO($dsn, $cfg['user'], $cfg['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT => 2
            ]);
            $db = new \Core\Database($cfg);
        } catch (\Throwable $e) {
            $db = null;
        }

        if (!$db) return null;
        $categoryModel = new \Models\Category();

        // 1. Check if category already exists (exact name or case-insensitive)
        $existing = $db->query("SELECT id, name, parent_id, image_path FROM categories WHERE LOWER(TRIM(name)) = LOWER(?) LIMIT 1", [$categoryName])->fetch();

        // Also check if existing name matches before parenthesis, e.g. "চা ও কফি" from "চা ও কফি (Tea & Coffee)"
        if (!$existing && strpos($categoryName, '(') !== false) {
            $shortName = trim(explode('(', $categoryName)[0]);
            if ($shortName) {
                $existing = $db->query("SELECT id, name, parent_id, image_path FROM categories WHERE LOWER(TRIM(name)) = LOWER(?) LIMIT 1", [$shortName])->fetch();
            }
        }

        // Also check English part inside parenthesis, e.g. "Tea & Coffee"
        if (!$existing && preg_match('/\((.*?)\)/', $categoryName, $m)) {
            $engPart = trim($m[1]);
            if ($engPart) {
                $existing = $db->query("SELECT id, name, parent_id, image_path FROM categories WHERE LOWER(TRIM(name)) = LOWER(?) LIMIT 1", [$engPart])->fetch();
            }
        }

        $categoryImagePath = null;
        if (!empty($imageUrl)) {
            $categoryImagePath = $this->downloadAndSaveCategoryImage($imageUrl, $categoryName);
        }

        // Fallback to fetchShwapnoCategoryImage if no image yet
        if (!$categoryImagePath) {
            $remoteCatImg = $this->fetchShwapnoCategoryImage($categoryName);
            if ($remoteCatImg) {
                $categoryImagePath = $this->downloadAndSaveCategoryImage($remoteCatImg, $categoryName);
            }
        }

        if ($existing) {
            $updates = [];
            $params = [];
            // If existing category has no image, update with the newly downloaded category image
            if (empty($existing['image_path']) && $categoryImagePath) {
                $updates[] = "image_path = ?";
                $params[] = $categoryImagePath;
            }
            // If existing category has no parent_id and parentId is provided, link it
            if (empty($existing['parent_id']) && $parentId) {
                $updates[] = "parent_id = ?";
                $params[] = $parentId;
            }

            if (!empty($updates)) {
                $params[] = $existing['id'];
                $db->query("UPDATE categories SET " . implode(', ', $updates) . " WHERE id = ?", $params);
            }

            return [
                'id' => (int)$existing['id'],
                'name' => $existing['name'],
                'parent_id' => $parentId ?: ($existing['parent_id'] ?? null),
                'created' => false,
                'image_path' => $existing['image_path'] ?: $categoryImagePath
            ];
        }

        // 2. Auto-create new category with the image, English slug and parent_id!
        $catItem = \Core\ShwapnoCatalog::findItem($categoryName);
        $categorySlug = ($catItem && !empty($catItem['slug'])) ? $catItem['slug'] : null;

        $newCatId = $categoryModel->create([
            'name' => $categoryName,
            'slug' => $categorySlug,
            'parent_id' => $parentId ?: null,
            'description' => $description ?: ($categoryName . ' - স্বয়ংক্রিয়ভাবে তৈরি ক্যাটাগরি'),
            'image_path' => $categoryImagePath
        ]);

        return [
            'id' => (int)$newCatId,
            'name' => $categoryName,
            'created' => true,
            'image_path' => $categoryImagePath
        ];
    }

    public function shwapnoImporterIndex() {
        $categoryModel = new \Models\Category();
        $vendorModel = new \Models\Vendor();
        $categories = $categoryModel->all();
        $vendors = $vendorModel->all();

        // Build category hierarchy for dropdown
        $categoriesById = [];
        foreach ($categories as $c) {
            $categoriesById[$c['id']] = $c;
            $categoriesById[$c['id']]['children'] = [];
        }
        $categoryHierarchy = [];
        foreach ($categoriesById as $id => &$cat) {
            if (!empty($cat['parent_id']) && isset($categoriesById[$cat['parent_id']])) {
                $categoriesById[$cat['parent_id']]['children'][] = &$cat;
            } else {
                $categoryHierarchy[] = &$cat;
            }
        }
        unset($cat);

        // Multi-level Shwapno catalog tree & flattened list
        $categoryTree = \Core\ShwapnoCatalog::getTree();
        $categoryFlatList = \Core\ShwapnoCatalog::getFlattenedList();

        return $this->view('admin/products/shwapno_importer', [
            'title' => 'Shwapno Category Auto-Importer (ক্যাটাগরি অনুযায়ী পণ্য ও ছবি ইমপোর্ট)',
            'categories' => $categories,
            'categoryHierarchy' => $categoryHierarchy,
            'categoryTree' => $categoryTree,
            'categoryFlatList' => $categoryFlatList,
            'vendors' => $vendors
        ]);
    }

    public function shwapnoCategoryFetch() {
        header('Content-Type: application/json; charset=utf-8');
        $catInput = trim($_GET['category'] ?? '');

        if (empty($catInput)) {
            echo json_encode(['success' => false, 'message' => 'ক্যাটাগরি নির্ধারণ করা হয়নি!'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // If user passed a full URL e.g. https://www.shwapno.com/fresh-fruits
        if (filter_var($catInput, FILTER_VALIDATE_URL)) {
            $parsed = parse_url($catInput, PHP_URL_PATH);
            $catInput = trim($parsed, '/');
        }

        // Look up in ShwapnoCatalog tree
        $catalogItem = \Core\ShwapnoCatalog::findItem($catInput);
        $resolvedCategoryName = $catInput;
        $parentName = null;
        $parentSlug = null;
        $categorySlug = $catInput;
        $level = 1;
        $searchQuery = null;

        if ($catalogItem) {
            $resolvedCategoryName = $catalogItem['name'];
            $parentName = $catalogItem['parent_name'] ?? null;
            $parentSlug = $catalogItem['parent_slug'] ?? null;
            $categorySlug = $catalogItem['slug'];
            $level = $catalogItem['level'] ?? 1;
            $searchQuery = $catalogItem['search_query'] ?? null;
        } else {
            // Preset fallback map
            $popularCategoryMap = [
                'rice' => 'চাল ও শস্য (Rice & Grains)',
                'soybean oil' => 'ভোজ্য তেল ও ঘি (Oil & Ghee)',
                'tea' => 'চা ও কফি (Tea & Coffee)',
                'dairy' => 'দুধ ও দুগ্ধজাত (Dairy & Milk)',
                'fresh-fruits' => 'তাজা ফলমূল (Fresh Fruits)',
                'fresh-vegetables' => 'তাজা শাকসবজি (Fresh Vegetables)',
                'spices' => 'মসলা ও রান্নার উপাদান (Spices)',
                'fish' => 'মাছ ও সামুদ্রিক খাদ্য (Fish & Seafood)',
                'meat' => 'মাংস ও ডিম (Meat & Eggs)',
                'beverages' => 'জুস ও পানীয় (Beverages)',
                'snacks' => 'বিস্কুট ও স্ন্যাক্স (Snacks & Bakery)',
                'flours' => 'আটা, ময়দা ও সুজি (Flour & Suji)',
                'cleaning' => 'পরিষ্কার পরিচ্ছন্নতা (Cleaning)',
                'baby-food-care' => 'শিশু খাদ্য ও যত্ন (Baby Care)',
                'personal-care' => 'পার্সোনাল কেয়ার (Personal Care)'
            ];
            $lowerKey = strtolower($catInput);
            $resolvedCategoryName = $popularCategoryMap[$lowerKey] ?? ucwords(str_replace(['-', '_'], ' ', $catInput));
        }

        $page = max(1, intval($_GET['page'] ?? 1));
        $cleanSlug = trim(str_replace(['-', '_'], ' ', $categorySlug));

        // Candidate search queries in priority order for comprehensive catalog results
        $slugKeywordMap = [
            'rice' => 'rice',
            'packed-rice' => 'packed rice',
            'loose-rice' => 'loose rice',
            'polao-rice' => 'polao rice',
            'oil' => 'cooking oil',
            'soybean-oil' => 'soybean oil',
            'mustard-oil' => 'mustard oil',
            'sunflower-oil' => 'sunflower oil',
            'ghee' => 'ghee',
            'olive-oil' => 'olive oil',
            'fish' => 'fish',
            'fresh-water-fish' => 'fresh water fish',
            'sea-fish' => 'sea fish',
            'meat' => 'meat',
            'beef' => 'beef',
            'chicken' => 'chicken',
            'mutton' => 'mutton',
            'meat-and-eggs' => 'meat',
            'meat-and-fish' => 'meat fish',
            'fruits-and-vegetables' => 'fresh fruits',
            'fresh-fruits' => 'fresh fruits',
            'fresh-vegetables' => 'fresh vegetables',
            'dry-fruits' => 'dry fruits',
            'spices' => 'spices',
            'daal-or-lentil' => 'dal lentil',
            'flours' => 'flour atta',
            'dairy' => 'milk dairy',
            'baby-food' => 'baby food',
            'cleaning' => 'cleaning',
            'personal-care' => 'personal care',
            'tea' => 'tea',
            'coffee' => 'coffee',
            'snacks' => 'biscuits snacks'
        ];

        $candidateQueries = [];
        if (!empty($slugKeywordMap[$categorySlug])) {
            $candidateQueries[] = $slugKeywordMap[$categorySlug];
        }
        if (!empty($searchQuery) && !in_array($searchQuery, $candidateQueries)) {
            $candidateQueries[] = $searchQuery;
        }
        if (!empty($cleanSlug) && !in_array($cleanSlug, $candidateQueries)) {
            $candidateQueries[] = $cleanSlug;
        }
        if (!empty($parentSlug)) {
            $cleanParent = trim(str_replace(['-', '_'], ' ', $parentSlug));
            if (!empty($cleanParent) && !in_array($cleanParent, $candidateQueries)) {
                $candidateQueries[] = $cleanParent;
            }
        }

        // Known catalogIds for fallback /api/category/products endpoint
        $knownCatalogIds = [
            'rice' => '65ed45e3e429af37f903ae15',
            'packed-rice' => '65ed45e3e429af37f903ae15',
            'loose-rice' => '65ed45e3e429af37f903ae15',
            'polao-rice' => '65ed45e3e429af37f903ae15',
            'oil' => '65ed45dfe429af37f903aded',
            'soybean-oil' => '65ed45dfe429af37f903aded',
            'mustard-oil' => '65ed45dfe429af37f903aded',
            'sunflower-oil' => '65ed45dfe429af37f903aded',
            'olive-oil' => '65ed45dfe429af37f903aded',
            'ghee' => '65ed45dfe429af37f903aded',
            'fish' => '65ed45e2e429af37f903ae0b',
            'fresh-water-fish' => '65ed45e2e429af37f903ae0b',
            'sea-fish' => '65ed45e2e429af37f903ae0b',
            'meat' => '65ed45e2e429af37f903ae0f',
            'beef' => '65ed45e2e429af37f903ae0f',
            'chicken' => '65ed45e2e429af37f903ae0f',
            'mutton' => '65ed45e2e429af37f903ae0f',
            'fresh-fruits' => '65ed45e2e429af37f903ae07',
            'fresh-vegetables' => '65ed45e2e429af37f903ae08',
            'dairy' => '65ed45e0e429af37f903adf4',
            'liquid-milk' => '65ed45e0e429af37f903adf4',
            'powder-milk' => '65ed45e0e429af37f903adf4',
            'baby-food' => '65ed45e1e429af37f903adff',
            'spices' => '65ed45e4e429af37f903ae22',
            'powder-spice' => '65ed45e4e429af37f903ae22',
            'whole-spice' => '65ed45e4e429af37f903ae22',
            'salt-and-sugar' => '65ed45e4e429af37f903ae23',
            'daal-or-lentil' => '65ed45e3e429af37f903ae18',
            'flours' => '65ed45e4e429af37f903ae27',
            'shemai-and-suji' => '65ed45e6e429af37f903ae39',
            'sauces-and-pickles' => '65ed45e0e429af37f903adf3',
            'canned-food' => '65ed45e7e429af37f903ae45',
            'home-cleaning' => '65ed45e0e429af37f903adf5',
            'personal-care' => '65ed45e9e429af37f903ae5c',
            'beauty-and-health' => '65ed45e9e429af37f903ae5b',
            'pet-care' => '65ed45e1e429af37f903ae02',
            'gadget' => '6684075f8c4b6f7aaf96d8c4'
        ];

        $catalogId = $knownCatalogIds[$categorySlug] ?? ($knownCatalogIds[strtolower($catInput)] ?? null);
        $products = [];
        $totalPages = 1;
        $totalCount = 0;
        $currentPage = $page;
        $hasNextPage = false;
        $hasPrevPage = ($page > 1);

        $requestType = ($page > 1) ? '20' : '0';

        // 1. Try Search API with candidate queries (provides rich catalog with up to 300+ items across 6 pages)
        foreach ($candidateQueries as $q) {
            $urlSearch = "https://www.shwapno.com/api/search?q=" . urlencode($q) . "&pageNumber={$page}&requestType={$requestType}";
            $ch = curl_init($urlSearch);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 12);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36");
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json", "Referer: https://www.shwapno.com/"]);
            $resSearch = curl_exec($ch);
            $codeSearch = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($codeSearch === 200 && $resSearch) {
                $dataSearch = json_decode($resSearch, true);
                if (!empty($dataSearch['products'])) {
                    $products = $dataSearch['products'];
                    $totalPages = intval($dataSearch['totalPages'] ?? 1);
                    $totalCount = intval($dataSearch['totalCount'] ?? count($products));
                    $currentPage = intval($dataSearch['currentPage'] ?? $page);
                    $hasNextPage = ($currentPage < $totalPages);
                    $hasPrevPage = ($currentPage > 1);
                    break;
                }
            }
        }

        // 2. Fallback to Category API if search returned empty and catalogId exists
        if (empty($products) && $catalogId) {
            $urlCat = "https://www.shwapno.com/api/category/products?id={$catalogId}&pageNumber={$page}";
            $ch = curl_init($urlCat);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 12);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36");
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json", "Referer: https://www.shwapno.com/"]);
            $resCat = curl_exec($ch);
            $codeCat = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($codeCat === 200 && $resCat) {
                $dataCat = json_decode($resCat, true);
                if (!empty($dataCat['products'])) {
                    $products = $dataCat['products'];
                    $totalPages = intval($dataCat['totalPages'] ?? 1);
                    $totalCount = intval($dataCat['totalItems'] ?? count($products));
                    $currentPage = intval($dataCat['pageNumber'] ?? $page);
                    $hasNextPage = !empty($dataCat['hasNextPage']) || ($currentPage < $totalPages);
                    $hasPrevPage = !empty($dataCat['hasPreviousPage']) || ($currentPage > 1);
                }
            }
        }

        if (empty($products)) {
            echo json_encode([
                'success' => false,
                'message' => "Shwapno-তে '{$catInput}' ক্যাটাগরির কোনো পণ্য পাওয়া যায়নি (পৃষ্ঠা {$page})। অনুগ্রহ করে অন্য নাম বা ইংরেজি নাম চেষ্টা করুন।",
                'products' => [],
                'current_page' => $page,
                'total_pages' => 0,
                'total_count' => 0,
                'has_next_page' => false,
                'has_prev_page' => false
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Check each product against local database to see if it already exists
        $db = null;
        try {
            $cfg = require __DIR__ . '/../../config/database.php';
            $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['dbname']};charset=utf8mb4";
            $pdo = new \PDO($dsn, $cfg['user'], $cfg['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT => 1
            ]);
            $db = new \Core\Database($cfg);
        } catch (\Throwable $e) {
            // Database temporarily offline, proceed without duplicate marking
            $db = null;
        }
        $parsedItems = [];
        $newCount = 0;
        $existingCount = 0;

        foreach ($products as $item) {
            $p = $item['product'] ?? $item;
            $name = trim($p['name'] ?? '');
            if (!$name) continue;

            $sku = trim($p['sku'] ?? '');
            $sellPrice = floatval($p['price']['priceValue'] ?? 0);
            $oldPrice = !empty($p['price']['oldPriceValue']) ? floatval($p['price']['oldPriceValue']) : null;
            
            $img = $p['picture']['largeDeviceUrl']['imageUrl'] 
                ?? $p['picture']['largeDeviceUrl']['fullSizeImageUrl'] 
                ?? $p['picture']['smallDeviceUrl']['imageUrl'] 
                ?? '';
            $thumb = $p['picture']['smallDeviceUrl']['imageUrl'] ?? $img;

            $unit = trim($p['unit'] ?? 'Piece');
            $unitType = 'piece';
            $baseUnit = 'pcs';
            if (stripos($unit, 'kg') !== false) {
                $unitType = 'weight';
                $baseUnit = 'kg';
            } elseif (stripos($unit, 'ltr') !== false || stripos($unit, 'liter') !== false || stripos($unit, 'ml') !== false) {
                $unitType = 'liquid';
                $baseUnit = 'ltr';
            }

            // Check duplicate in local DB
            $exists = false;
            $existingId = null;
            if ($db) {
                try {
                    if ($sku) {
                        $chk = $db->query("SELECT id FROM products WHERE sku = ? LIMIT 1", [$sku])->fetch();
                        if ($chk) {
                            $exists = true;
                            $existingId = $chk['id'];
                        }
                    }
                    if (!$exists) {
                        $chk2 = $db->query("SELECT id FROM products WHERE name = ? LIMIT 1", [$name])->fetch();
                        if ($chk2) {
                            $exists = true;
                            $existingId = $chk2['id'];
                        }
                    }
                } catch (\Throwable $e) {
                    $exists = false;
                }
            }

            if ($exists) {
                $existingCount++;
            } else {
                $newCount++;
            }

            // Extract variants if available
            $variants = [];
            if (!empty($p['uomOptions']) && is_array($p['uomOptions'])) {
                foreach ($p['uomOptions'] as $uom) {
                    $vTitle = trim($uom['name'] ?? '');
                    $vPrice = floatval($uom['price']['priceValue'] ?? $sellPrice);
                    if ($vTitle) {
                        $variants[] = [
                            'title' => $vTitle,
                            'qty' => 1,
                            'price' => $vPrice > 0 ? $vPrice : $sellPrice,
                            'is_default' => !empty($uom['isPreSelected']) ? 1 : 0
                        ];
                    }
                }
            }

            $parsedItems[] = [
                'name' => $name,
                'sku' => $sku,
                'sell_price' => $sellPrice,
                'old_price' => $oldPrice,
                'unit' => $unit,
                'unit_type' => $unitType,
                'base_unit' => $baseUnit,
                'image_url' => $img,
                'thumbnail' => $thumb,
                'category_name' => $resolvedCategoryName,
                'variants' => $variants,
                'exists_in_db' => $exists,
                'existing_id' => $existingId
            ];
        }

        $categoryImage = !empty($parsedItems[0]['image_url']) ? $parsedItems[0]['image_url'] : '';

        echo json_encode([
            'success' => true,
            'category' => $categorySlug,
            'category_name' => $resolvedCategoryName,
            'parent_slug' => $parentSlug,
            'parent_name' => $parentName,
            'level' => $level,
            'category_image' => $categoryImage,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'total_count' => $totalCount,
            'has_next_page' => $hasNextPage,
            'has_prev_page' => $hasPrevPage,
            'count' => count($parsedItems),
            'new_count' => $newCount,
            'existing_count' => $existingCount,
            'products' => $parsedItems
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function shwapnoImportSingle() {
        header('Content-Type: application/json; charset=utf-8');

        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
        if (!\Core\CSRF::verify($csrfToken)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'CSRF Token mismatch!'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'পণ্যের নাম দেওয়া হয়নি!'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $sku = trim($_POST['sku'] ?? '');
        if (empty($sku)) {
            $sku = 'SKU-' . strtoupper(substr(md5($name . microtime()), 0, 8));
        }

        $sellPrice = floatval($_POST['sell_price'] ?? 0);
        $oldPrice = !empty($_POST['old_price']) ? floatval($_POST['old_price']) : null;
        $discountType = 'none';
        $discountValue = 0;
        if ($oldPrice && $oldPrice > $sellPrice) {
            $discountType = 'fixed';
            $discountValue = $oldPrice - $sellPrice;
        }

        $categoryId = (!empty($_POST['category_id']) && is_numeric($_POST['category_id']) && intval($_POST['category_id']) > 0) ? intval($_POST['category_id']) : null;
        $categoryName = trim($_POST['category_name'] ?? '');
        $categorySlug = trim($_POST['category_slug'] ?? '');
        $parentSlug = trim($_POST['parent_slug'] ?? '');
        $parentName = trim($_POST['parent_name'] ?? '');
        $level = intval($_POST['level'] ?? 1);
        $vendorId = !empty($_POST['vendor_id']) ? intval($_POST['vendor_id']) : null;
        $stockQty = !empty($_POST['stock_qty']) ? floatval($_POST['stock_qty']) : 50;
        $unitType = $_POST['unit_type'] ?? 'piece';
        $baseUnit = $_POST['base_unit'] ?? 'pcs';
        $imageUrl = trim($_POST['image_url'] ?? '');
        $variantsJson = !empty($_POST['variants']) ? (is_string($_POST['variants']) ? $_POST['variants'] : json_encode($_POST['variants'], JSON_UNESCAPED_UNICODE)) : null;

        $createdCategoryInfo = null;

        // Auto-create category with image and proper parent_id if category_id is empty/auto and category_name is present
        if (empty($categoryId) && !empty($categoryName)) {
            $catItem = \Core\ShwapnoCatalog::findItem($categorySlug ?: $categoryName);
            if ($catItem) {
                $parentSlug = $parentSlug ?: ($catItem['parent_slug'] ?? null);
                $parentName = $parentName ?: ($catItem['parent_name'] ?? null);
            }

            $parentId = null;
            if (!empty($parentSlug) || !empty($parentName)) {
                $dbConn = new \Core\Database(require __DIR__ . '/../../config/database.php');
                $parentId = $this->resolveOrBuildCategoryHierarchy($dbConn, $parentSlug, $parentName);
            }

            $catResult = $this->ensureCategoryExistsWithImage($categoryName, $imageUrl, $parentId);
            if ($catResult) {
                $categoryId = $catResult['id'];
                if (!empty($catResult['created'])) {
                    $createdCategoryInfo = $catResult;
                }
            }
        }

        $db = new \Core\Database(require __DIR__ . '/../../config/database.php');
        $productModel = new Product();

        // Check if already exists
        $existing = $db->query("SELECT id, sku FROM products WHERE sku = ? OR name = ? LIMIT 1", [$sku, $name])->fetch();

        // Download & save image if provided
        $localImagePath = null;
        if (!empty($imageUrl) && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            $tempId = $existing['id'] ?? time();
            $saveRes = $this->downloadAndSaveImageFile($tempId, $imageUrl);
            if ($saveRes['success']) {
                $localImagePath = $saveRes['image_path'];
            }
        }

        if ($existing) {
            // Update existing product price & image if missing
            $sql = "UPDATE products SET sell_price = ?, regular_price = ?, discount_type = ?, discount_value = ?";
            $params = [$sellPrice, $oldPrice, $discountType, $discountValue];
            if ($localImagePath) {
                $sql .= ", image_path = ?";
                $params[] = $localImagePath;
            }
            if ($categoryId) {
                $sql .= ", category_id = ?";
                $params[] = $categoryId;
            }
            $sql .= " WHERE id = ?";
            $params[] = $existing['id'];
            $db->query($sql, $params);

            $msg = 'বিদ্যমান পণ্য আপডেট করা হয়েছে!';
            if ($createdCategoryInfo) {
                $msg .= ' এবং "' . $createdCategoryInfo['name'] . '" ক্যাটাগরি ছবি সহ তৈরি হয়েছে!';
            }

            echo json_encode([
                'success' => true,
                'status' => 'updated',
                'id' => $existing['id'],
                'name' => $name,
                'category_id' => $categoryId,
                'category_created' => !empty($createdCategoryInfo),
                'category_info' => $createdCategoryInfo,
                'image_path' => $localImagePath,
                'message' => $msg
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Make sure SKU is unique
        $skuCheck = $db->query("SELECT id FROM products WHERE sku = ?", [$sku])->fetch();
        if ($skuCheck) {
            $sku = $sku . '-' . rand(10, 99);
        }

        // Insert new product
        $productModel->create([
            'name' => $name,
            'sku' => $sku,
            'description' => 'Shwapno থেকে সংগৃহীত - ' . $name,
            'buy_price' => round($sellPrice * 0.85, 2), // 15% estimated margin
            'regular_price' => $oldPrice,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'sell_price' => $sellPrice,
            'stock_qty' => $stockQty,
            'vendor_id' => $vendorId,
            'category_id' => $categoryId,
            'brand_id' => null,
            'image_path' => $localImagePath,
            'unit_type' => $unitType,
            'base_unit' => $baseUnit,
            'purchase_unit' => null,
            'purchase_unit_qty' => 1.000,
            'selling_unit' => $baseUnit,
            'unit_variants_json' => $variantsJson,
            'availability_status' => 'available',
            'is_verified' => 1,
            'demand_percentage' => 50
        ]);

        $newId = $db->lastInsertId();

        $msg = 'নতুন পণ্য হিসেবে সফলভাবে ইনপুট হয়েছে!';
        if ($createdCategoryInfo) {
            $msg .= ' এবং "' . $createdCategoryInfo['name'] . '" ক্যাটাগরি ছবি সহ স্বয়ংক্রিয়ভাবে তৈরি হয়েছে!';
        }

        echo json_encode([
            'success' => true,
            'status' => 'created',
            'id' => $newId,
            'name' => $name,
            'category_id' => $categoryId,
            'category_created' => !empty($createdCategoryInfo),
            'category_info' => $createdCategoryInfo,
            'image_path' => $localImagePath,
            'message' => $msg
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function fetchShwapnoCategoryImage($slug) {
        $slug = trim($slug);
        if (empty($slug)) return null;

        $searchQuery = null;

        // 1. Look up in ShwapnoCatalog tree for curated search query
        $catalogItem = \Core\ShwapnoCatalog::findItem($slug);
        if ($catalogItem && !empty($catalogItem['search_query'])) {
            $searchQuery = $catalogItem['search_query'];
        }

        if (!$searchQuery) {
            $catSearchMap = [
                'rice' => 'miniket rice',
                'soybean oil' => 'rupchanda soybean oil',
                'tea' => 'taaza tea',
                'dairy' => 'diploma milk powder',
                'fresh-fruits' => 'apple fuji',
                'fresh-vegetables' => 'fresh tomato',
                'spices' => 'radhuni cumin',
                'fish' => 'rui fish',
                'meat' => 'beef premium',
                'beverages' => 'mango juice',
                'snacks' => 'digestive biscuit',
                'flours' => 'pusti atta',
                'cleaning' => 'wheel detergent',
                'baby-food-care' => 'cerelac baby food',
                'personal-care' => 'lux soap'
            ];
            $lowerSlug = strtolower($slug);
            $searchQuery = $catSearchMap[$lowerSlug] ?? $catSearchMap[$slug] ?? str_replace(['-', '_'], ' ', $slug);
        }

        // Search Shwapno API with representative query
        $url = "https://www.shwapno.com/api/search?q=" . urlencode($searchQuery);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json", "Referer: https://www.shwapno.com/"]);
        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code === 200 && $res) {
            $data = json_decode($res, true);
            if (!empty($data['products'][0]['product']['picture']['largeDeviceUrl']['imageUrl'])) {
                return $data['products'][0]['product']['picture']['largeDeviceUrl']['imageUrl'];
            }
            if (!empty($data['products'][0]['product']['picture']['mediumDeviceUrl']['imageUrl'])) {
                return $data['products'][0]['product']['picture']['mediumDeviceUrl']['imageUrl'];
            }
        }

        return null;
    }

    /**
     * Helper to find existing category in DB by full name or prefix/english inside parens
     */
    private function findCategoryInDb($db, $name) {
        $clean = trim($name);
        if (empty($clean)) return null;

        $existing = $db->query("SELECT id, name, parent_id, image_path FROM categories WHERE LOWER(TRIM(name)) = LOWER(?) LIMIT 1", [$clean])->fetch();
        if ($existing) return $existing;

        if (strpos($clean, '(') !== false) {
            $shortName = trim(explode('(', $clean)[0]);
            if ($shortName) {
                $existing = $db->query("SELECT id, name, parent_id, image_path FROM categories WHERE LOWER(TRIM(name)) = LOWER(?) LIMIT 1", [$shortName])->fetch();
                if ($existing) return $existing;
            }
        }

        if (preg_match('/\((.*?)\)/', $clean, $m)) {
            $engPart = trim($m[1]);
            if ($engPart) {
                $existing = $db->query("SELECT id, name, parent_id, image_path FROM categories WHERE LOWER(TRIM(name)) = LOWER(?) LIMIT 1", [$engPart])->fetch();
                if ($existing) return $existing;
            }
        }

        return null;
    }

    /**
     * Recursively resolve or create parent category in database
     */
    private function resolveOrBuildCategoryHierarchy($db, $parentSlug, $parentName = null) {
        if (empty($parentSlug) && empty($parentName)) return null;

        // 1. Check if parent already in DB
        if (!empty($parentName)) {
            $found = $this->findCategoryInDb($db, $parentName);
            if ($found) return (int)$found['id'];
        }
        if (!empty($parentSlug)) {
            $found = $this->findCategoryInDb($db, $parentSlug);
            if ($found) return (int)$found['id'];
        }

        // 2. Look up parent in ShwapnoCatalog
        $parentCatalog = \Core\ShwapnoCatalog::findItem($parentSlug ?: $parentName);
        if (!$parentCatalog) {
            $nameToUse = $parentName ?: ucwords(str_replace(['-', '_'], ' ', $parentSlug));
            $catModel = new \Models\Category();
            return (int)$catModel->create([
                'name' => $nameToUse,
                'parent_id' => null,
                'description' => $nameToUse . ' - স্বয়ংক্রিয়ভাবে তৈরি ক্যাটাগরি',
                'image_path' => null
            ]);
        }

        // 3. If parent has its own parent (e.g. grandparent for leaf)
        $grandparentId = null;
        if (!empty($parentCatalog['parent_slug']) || !empty($parentCatalog['parent_name'])) {
            $grandparentId = $this->resolveOrBuildCategoryHierarchy($db, $parentCatalog['parent_slug'], $parentCatalog['parent_name']);
        }

        // 4. Download representative image for parent
        $parentImg = $this->fetchShwapnoCategoryImage($parentCatalog['slug']);
        $savedImg = null;
        if ($parentImg) {
            $savedImg = $this->downloadAndSaveCategoryImage($parentImg, $parentCatalog['name']);
        }

        // 5. Create parent in DB
        $catModel = new \Models\Category();
        return (int)$catModel->create([
            'name' => $parentCatalog['name'],
            'slug' => $parentCatalog['slug'],
            'parent_id' => $grandparentId,
            'description' => $parentCatalog['name'] . ' - Shwapno থেকে স্বয়ংক্রিয়ভাবে সংগৃহীত ক্যাটাগরি',
            'image_path' => $savedImg
        ]);
    }

    public function shwapnoSetupCategory() {
        header('Content-Type: application/json; charset=utf-8');
        $slug = trim($_POST['slug'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $parentSlug = trim($_POST['parent_slug'] ?? '');
        $parentName = trim($_POST['parent_name'] ?? '');
        $level = intval($_POST['level'] ?? 1);

        // If name is empty but slug is provided, find in catalog
        if (empty($name) && !empty($slug)) {
            $catItem = \Core\ShwapnoCatalog::findItem($slug);
            if ($catItem) {
                $name = $catItem['name'];
                $parentSlug = $parentSlug ?: ($catItem['parent_slug'] ?? '');
                $parentName = $parentName ?: ($catItem['parent_name'] ?? '');
                $level = $catItem['level'] ?? $level;
            }
        }

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'ক্যাটাগরির নাম প্রদান করা হয়নি!'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
        }

        $db = null;
        try {
            $cfg = require __DIR__ . '/../../config/database.php';
            $db = new \Core\Database($cfg);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => 'ডাটাবেস কানেকশন ব্যর্থ: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $categoryModel = new \Models\Category();

        // 1. Resolve parent_id if parent is specified
        $parentId = null;
        if (!empty($parentName) || !empty($parentSlug)) {
            $parentId = $this->resolveOrBuildCategoryHierarchy($db, $parentSlug, $parentName);
        }

        // 2. Check if category already exists (exact name or case-insensitive)
        $existing = $this->findCategoryInDb($db, $name);

        // Fetch category image from Shwapno
        $remoteImageUrl = $this->fetchShwapnoCategoryImage($slug);
        $savedImagePath = null;
        if ($remoteImageUrl) {
            $savedImagePath = $this->downloadAndSaveCategoryImage($remoteImageUrl, $name);
        }

        if ($existing) {
            $updates = [];
            $params = [];

            // Update image if missing and newly downloaded
            if (empty($existing['image_path']) && $savedImagePath) {
                $updates[] = "image_path = ?";
                $params[] = $savedImagePath;
            }

            // Update parent_id if currently null and we have a resolved parent
            if (empty($existing['parent_id']) && $parentId) {
                $updates[] = "parent_id = ?";
                $params[] = $parentId;
            }

            if (!empty($updates)) {
                $params[] = $existing['id'];
                $db->query("UPDATE categories SET " . implode(', ', $updates) . " WHERE id = ?", $params);
            }

            echo json_encode([
                'success' => true,
                'status' => !empty($updates) ? 'updated' : 'already_exists',
                'id' => (int)$existing['id'],
                'name' => $existing['name'],
                'parent_id' => $parentId ?: $existing['parent_id'],
                'parent_name' => $parentName,
                'level' => $level,
                'image_path' => $savedImagePath ?: $existing['image_path'],
                'message' => !empty($updates) ? 'ক্যাটাগরির তথ্য ও হায়ারার্কি আপডেট করা হয়েছে' : 'ক্যাটাগরি ইতোমধ্যে ডাটাবেসে বিদ্যমান'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Auto-create new category with image, slug and parent_id
        $newId = $categoryModel->create([
            'name' => $name,
            'slug' => $slug,
            'parent_id' => $parentId ?: null,
            'description' => $name . ' - Shwapno থেকে স্বয়ংক্রিয়ভাবে সংগৃহীত ক্যাটাগরি',
            'image_path' => $savedImagePath
        ]);

        echo json_encode([
            'success' => true,
            'status' => 'created',
            'id' => (int)$newId,
            'name' => $name,
            'parent_id' => $parentId ?: null,
            'parent_name' => $parentName,
            'level' => $level,
            'image_path' => $savedImagePath,
            'message' => 'নতুন ক্যাটাগরি প্যারেন্ট সহ সফলভাবে তৈরি হয়েছে'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function shwapnoSetupAllCategories() {
        header('Content-Type: application/json; charset=utf-8');

        $catalogFlat = \Core\ShwapnoCatalog::getFlattenedList();
        $db = null;
        try {
            $cfg = require __DIR__ . '/../../config/database.php';
            $db = new \Core\Database($cfg);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => 'ডাটাবেস কানেকশন ব্যর্থ: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $categoryModel = new \Models\Category();
        $results = [];
        $createdCount = 0;
        $existingCount = 0;

        foreach ($catalogFlat as $cat) {
            $parentId = null;
            if (!empty($cat['parent_slug']) || !empty($cat['parent_name'])) {
                $parentId = $this->resolveOrBuildCategoryHierarchy($db, $cat['parent_slug'], $cat['parent_name']);
            }

            $existing = $this->findCategoryInDb($db, $cat['name']);
            if ($existing) {
                $existingCount++;
                if (empty($existing['parent_id']) && $parentId) {
                    $db->query("UPDATE categories SET parent_id = ? WHERE id = ?", [$parentId, $existing['id']]);
                }
                $results[] = [
                    'id' => (int)$existing['id'],
                    'name' => $existing['name'],
                    'parent_id' => $parentId ?: $existing['parent_id'],
                    'level' => $cat['level'],
                    'created' => false,
                    'image_path' => $existing['image_path']
                ];
            } else {
                $imgUrl = $this->fetchShwapnoCategoryImage($cat['slug']);
                $savedImg = null;
                if ($imgUrl) {
                    $savedImg = $this->downloadAndSaveCategoryImage($imgUrl, $cat['name']);
                }
                $newId = $categoryModel->create([
                    'name' => $cat['name'],
                    'slug' => $cat['slug'],
                    'parent_id' => $parentId,
                    'description' => $cat['name'] . ' - Shwapno থেকে স্বয়ংক্রিয়ভাবে সংগৃহীত ক্যাটাগরি',
                    'image_path' => $savedImg
                ]);
                $createdCount++;
                $results[] = [
                    'id' => (int)$newId,
                    'name' => $cat['name'],
                    'parent_id' => $parentId,
                    'level' => $cat['level'],
                    'created' => true,
                    'image_path' => $savedImg
                ];
            }
        }

        $allCategories = $categoryModel->all();

        echo json_encode([
            'success' => true,
            'created_count' => $createdCount,
            'existing_count' => $existingCount,
            'total_processed' => count($catalogFlat),
            'results' => $results,
            'all_categories' => $allCategories,
            'message' => "মোট {$createdCount}টি নতুন ক্যাটাগরি তৈরি ও {$existingCount}টি ক্যাটাগরি সফলভাবে যাচাই করা হয়েছে।"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}




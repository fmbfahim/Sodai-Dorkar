<?php

namespace Controllers;

use Core\Controller;
use Models\Category;
use Core\Middleware;
use Core\ShwapnoCatalog;

class CategoryController extends Controller {
    
    public function __construct() {
        Middleware::permission('categories_brands');
    }
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $categoryModel = new Category();
        $allCategories = $categoryModel->all();

        // 1. Build Tree
        $tree = $this->buildTree($allCategories);

        // 2. Flatten for display with depth
        $categories = $this->flattenTree($tree);

        // Active / Last selected parent category
        $selectedParentId = $_GET['parent_id'] ?? ($_SESSION['last_category_parent_id'] ?? '');

        return $this->view('admin/categories/index', [
            'title' => 'Product Categories', 
            'categories' => $categories,
            'allCategories' => $allCategories,
            'selectedParentId' => $selectedParentId,
            'base' => $this->getBaseUrl()
        ]);
    }

    private function getBaseUrl() {
        return (strpos($_SERVER['REQUEST_URI'] ?? '', '/sodai-dorkar/public') !== false) ? '/sodai-dorkar/public' : '';
    }

    private function buildTree(array $elements, $parentId = null) {
        $branch = array();
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    private function flattenTree($tree, $depth = 0) {
        $result = [];
        foreach ($tree as $node) {
            $node['depth'] = $depth;
            $children = $node['children'] ?? [];
            unset($node['children']);
            $result[] = $node;
            if (!empty($children)) {
                $result = array_merge($result, $this->flattenTree($children, $depth + 1));
            }
        }
        return $result;
    }

    public function store() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $rawName = trim($_POST['name'] ?? '');
        $rawSlug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $parentId = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
        $selectedImageUrl = trim($_POST['selected_image_url'] ?? '');

        if (empty($rawName)) {
            header('Location: ' . $this->getBaseUrl() . '/admin/categories?error=empty_name');
            exit;
        }

        // Clean name & slug:
        // Rule: Category Name = pure Bengali; Category Slug = pure English
        $parsed = $this->resolveCleanNameAndSlug($rawName, $rawSlug);
        $name = $parsed['name'];
        $slug = $parsed['slug'];

        $imagePath = null;

        // 1. Handle user uploaded image file
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = $this->handleUploadedImage($_FILES['image']);
            if ($uploaded) {
                $imagePath = $uploaded;
            }
        }

        // 2. Handle selected preview image URL if no file uploaded
        if (!$imagePath && !empty($selectedImageUrl) && filter_var($selectedImageUrl, FILTER_VALIDATE_URL)) {
            $imagePath = $this->downloadAndSaveImage($selectedImageUrl, $slug ?: $name);
        }

        // 3. AUTO-ATTACH IMAGE: If still no image, automatically search & download a suitable image!
        if (!$imagePath) {
            $autoUrl = $this->findCategoryImageCandidate($name, $slug);
            if ($autoUrl) {
                $imagePath = $this->downloadAndSaveImage($autoUrl, $slug ?: $name);
            }
        }

        $categoryModel = new Category();
        $newId = $categoryModel->create([
            'name' => $name,
            'slug' => $slug,
            'parent_id' => $parentId,
            'description' => $description,
            'image_path' => $imagePath
        ]);

        // REMEMBER PARENT CATEGORY: Store in session so it never resets on reload!
        $_SESSION['last_category_parent_id'] = $parentId ?: '';

        $redirectUrl = $this->getBaseUrl() . '/admin/categories';
        if ($parentId) {
            $redirectUrl .= '?parent_id=' . urlencode($parentId);
        }
        
        header('Location: ' . $redirectUrl);
        exit;
    }

    public function edit() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . $this->getBaseUrl() . '/admin/categories');
            exit;
        }

        $categoryModel = new Category();
        $category = $categoryModel->find($id);
        if (!$category) {
            header('Location: ' . $this->getBaseUrl() . '/admin/categories');
            exit;
        }

        $categories = $categoryModel->all();

        return $this->view('admin/categories/edit', [
            'title' => 'Edit Category', 
            'category' => $category,
            'categories' => $categories,
            'base' => $this->getBaseUrl()
        ]);
    }

    public function update() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $id = $_POST['id'] ?? null;
        $rawName = trim($_POST['name'] ?? '');
        $rawSlug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $parentId = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
        $selectedImageUrl = trim($_POST['selected_image_url'] ?? '');
        $removeImage = !empty($_POST['remove_image']);

        if (!$id || empty($rawName)) {
            header('Location: ' . $this->getBaseUrl() . '/admin/categories');
            exit;
        }

        $parsed = $this->resolveCleanNameAndSlug($rawName, $rawSlug);
        $name = $parsed['name'];
        $slug = $parsed['slug'];

        $categoryModel = new Category();
        $existing = $categoryModel->find($id);

        $data = [
            'name' => $name,
            'slug' => $slug,
            'parent_id' => $parentId,
            'description' => $description
        ];

        if ($removeImage) {
            $data['image_path'] = null;
        } else {
            $newImagePath = null;

            // 1. Check file upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $newImagePath = $this->handleUploadedImage($_FILES['image']);
            }
            // 2. Check selected image URL
            elseif (!empty($selectedImageUrl) && filter_var($selectedImageUrl, FILTER_VALIDATE_URL)) {
                $newImagePath = $this->downloadAndSaveImage($selectedImageUrl, $slug ?: $name);
            }
            // 3. If existing category has no image, auto-fetch
            elseif (empty($existing['image_path'])) {
                $autoUrl = $this->findCategoryImageCandidate($name, $slug);
                if ($autoUrl) {
                    $newImagePath = $this->downloadAndSaveImage($autoUrl, $slug ?: $name);
                }
            }

            if ($newImagePath) {
                $data['image_path'] = $newImagePath;
            }
        }

        $categoryModel->update($id, $data);

        // Update last selected parent if applicable
        if ($parentId) {
            $_SESSION['last_category_parent_id'] = $parentId;
        }

        header('Location: ' . $this->getBaseUrl() . '/admin/categories');
        exit;
    }

    public function destroy() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            try {
                $categoryModel = new Category();
                $categoryModel->delete($id);
            } catch (\PDOException $e) {
                header('Location: ' . $this->getBaseUrl() . '/admin/categories?error=cannot_delete');
                exit;
            }
        }
        header('Location: ' . $this->getBaseUrl() . '/admin/categories');
        exit;
    }

    /**
     * AJAX endpoint to search 4-6 candidate images for a given category query
     */
    public function fetchImageCandidates() {
        header('Content-Type: application/json; charset=utf-8');
        $query = trim($_GET['query'] ?? '');
        if (empty($query)) {
            echo json_encode(['success' => false, 'images' => [], 'message' => 'অনুসন্ধান কুয়েরি প্রদান করুন।']);
            exit;
        }

        $candidates = $this->searchImageCandidates($query);
        echo json_encode([
            'success' => true,
            'query' => $query,
            'count' => count($candidates),
            'images' => $candidates
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * AJAX endpoint: 1-Click Auto Image Assignment for existing categories in the table
     */
    public function autoImage() {
        header('Content-Type: application/json; charset=utf-8');
        $id = intval($_POST['id'] ?? ($_GET['id'] ?? 0));
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ক্যাটাগরি আইডি পাওয়া যায়নি!'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $categoryModel = new Category();
        $cat = $categoryModel->find($id);
        if (!$cat) {
            echo json_encode(['success' => false, 'message' => 'ক্যাটাগরি বিদ্যমান নেই!'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $imgUrl = $this->findCategoryImageCandidate($cat['name'], $cat['slug'] ?? '');
        if (!$imgUrl) {
            echo json_encode(['success' => false, 'message' => 'ক্যাটাগরির জন্য কোনো ছবি খুঁজে পাওয়া যায়নি।'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $savedPath = $this->downloadAndSaveImage($imgUrl, $cat['slug'] ?: $cat['name']);
        if (!$savedPath) {
            echo json_encode(['success' => false, 'message' => 'ছবি ডাউনলোড ব্যর্থ হয়েছে।'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $categoryModel->update($id, ['image_path' => $savedPath, 'name' => $cat['name'], 'description' => $cat['description'], 'parent_id' => $cat['parent_id']]);

        echo json_encode([
            'success' => true,
            'id' => $id,
            'name' => $cat['name'],
            'image_path' => Category::getImageUrl($savedPath, $this->getBaseUrl()),
            'message' => 'ক্যাটাগরিতে ছবি সফলভাবে যুক্ত হয়েছে!'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ==========================================
    // HELPER FUNCTIONS
    // ==========================================

    /**
     * Resolve Name and Slug ensuring:
     * - Name is purely Bengali
     * - Slug is purely English
     */
    private function resolveCleanNameAndSlug($rawName, $rawSlug) {
        $name = trim($rawName);
        $slug = trim($rawSlug);

        // If name has parenthesis like "চাল (Rice)" or "Rice (চাল)"
        if (preg_match('/^(.*?)\((.*?)\)$/u', $name, $matches)) {
            $part1 = trim($matches[1]);
            $part2 = trim($matches[2]);

            // Detect which part has Bengali characters
            $isPart1Bn = preg_match('/[\x{0980}-\x{09FF}]/u', $part1);
            $isPart2Bn = preg_match('/[\x{0980}-\x{09FF}]/u', $part2);

            if ($isPart1Bn && !$isPart2Bn) {
                $name = $part1;
                if (empty($slug)) $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $part2), '-'));
            } elseif ($isPart2Bn && !$isPart1Bn) {
                $name = $part2;
                if (empty($slug)) $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $part1), '-'));
            } else {
                $name = $part1;
            }
        }

        // If slug is still empty, look up in ShwapnoCatalog
        if (empty($slug)) {
            $catalogItem = ShwapnoCatalog::findItem($name);
            if ($catalogItem && !empty($catalogItem['slug'])) {
                $slug = $catalogItem['slug'];
            }
        }

        // Fallback slug generation
        if (empty($slug)) {
            $engCharsOnly = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
            if (!empty($engCharsOnly)) {
                $slug = $engCharsOnly;
            } else {
                $slug = 'category-' . substr(md5($name . microtime()), 0, 6);
            }
        }

        return ['name' => $name, 'slug' => $slug];
    }

    private function handleUploadedImage($file) {
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedMimeTypes)) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/categories/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
        $fileName = 'cat_' . time() . '_' . substr(md5(uniqid()), 0, 6) . '.' . $ext;
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return '/uploads/categories/' . $fileName;
        }

        return null;
    }

    /**
     * Search multiple candidates from Shwapno API
     */
    private function searchImageCandidates($query) {
        $query = trim($query);
        $candidates = [];

        // 1. Look up catalog item for curated query
        $catalogItem = ShwapnoCatalog::findItem($query);
        $searchQueries = [];
        if ($catalogItem && !empty($catalogItem['search_query'])) {
            $searchQueries[] = $catalogItem['search_query'];
        }
        $searchQueries[] = $query;
        if (!empty($catalogItem['name_en'])) {
            $searchQueries[] = $catalogItem['name_en'];
        }

        foreach (array_unique($searchQueries) as $q) {
            if (count($candidates) >= 6) break;
            $url = "https://www.shwapno.com/api/search?q=" . urlencode($q);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36");
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json", "Referer: https://www.shwapno.com/"]);
            $res = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($code === 200 && $res) {
                $data = json_decode($res, true);
                if (!empty($data['products']) && is_array($data['products'])) {
                    foreach ($data['products'] as $p) {
                        $img = $p['product']['picture']['largeDeviceUrl']['imageUrl'] 
                            ?? ($p['product']['picture']['mediumDeviceUrl']['imageUrl'] 
                            ?? ($p['product']['picture']['smallDeviceUrl']['imageUrl'] ?? ''));
                        if ($img && !in_array($img, $candidates)) {
                            $candidates[] = $img;
                            if (count($candidates) >= 6) break;
                        }
                    }
                }
            }
        }

        return $candidates;
    }

    /**
     * Find best single image candidate
     */
    private function findCategoryImageCandidate($name, $slug = '') {
        $candidates = $this->searchImageCandidates($slug ?: $name);
        if (!empty($candidates[0])) {
            return $candidates[0];
        }

        // Secondary search with clean name
        if (!empty($name) && $name !== $slug) {
            $candidates2 = $this->searchImageCandidates($name);
            if (!empty($candidates2[0])) {
                return $candidates2[0];
            }
        }

        return null;
    }

    private function downloadAndSaveImage($imageUrl, $categoryName) {
        if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/categories/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ch = curl_init($imageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36");
        curl_setopt($ch, CURLOPT_REFERER, "https://www.shwapno.com/");
        $imageData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
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

        $ext = $allowedMimes[$mime] ?? 'jpg';
        $fileName = 'cat_' . time() . '_' . substr(md5(uniqid($categoryName)), 0, 6) . '.' . $ext;
        $targetFile = $uploadDir . $fileName;

        if (file_put_contents($targetFile, $imageData) !== false) {
            return '/uploads/categories/' . $fileName;
        }

        return null;
    }
}

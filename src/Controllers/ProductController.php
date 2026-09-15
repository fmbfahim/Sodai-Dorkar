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

        $products = $productModel->all();
        $vendors = $vendorModel->all();
        $categories = $categoryModel->all();
        $packagingUnits = (new \Models\PackagingUnit())->all();

        return $this->view('admin/products/index', [
            'title' => 'Products', 
            'products' => $products,
            'vendors' => $vendors,
            'categories' => $categories,
            'packagingUnits' => $packagingUnits
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
        
        $availability_status = $_POST['availability_status'] ?? 'pending';
        $is_verified = !empty($_POST['is_verified']) ? 1 : 0;

        // Multi-unit fields
        $unit_type = $_POST['unit_type'] ?? 'piece';
        $base_unit = trim($_POST['base_unit'] ?? 'pcs');
        $purchase_unit = trim($_POST['purchase_unit'] ?? '');
        $purchase_unit_qty = floatval($_POST['purchase_unit_qty'] ?? 1.000);
        $selling_unit = trim($_POST['selling_unit'] ?? $base_unit);
        $unit_variants_json = $this->processVariants($_POST['variants'] ?? ($_POST['unit_variants_json'] ?? null));

        $image_path = null;

        // Handle Image Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
            finfo_close($finfo);

            if (in_array($mime, $allowedMimeTypes)) {
                $uploadDir = __DIR__ . '/../../public/uploads/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $image_path = '/sodai-dorkar/public/uploads/products/' . $fileName;
                }
            }
        }

        if ($name && $sell_price >= 0) {
            $productModel = new Product();
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
                'purchase_unit' => $purchase_unit ?: null,
                'purchase_unit_qty' => $purchase_unit_qty > 0 ? $purchase_unit_qty : 1.000,
                'selling_unit' => $selling_unit ?: $base_unit,
                'unit_variants_json' => $unit_variants_json,
                'availability_status' => $availability_status,
                'is_verified' => $is_verified
            ]);
        }
        
        header('Location: /sodai-dorkar/public/admin/products');
        exit;
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /sodai-dorkar/public/admin/products');
            exit;
        }

        $productModel = new Product();
        $product = $productModel->find($id);

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
        $id = $_POST['id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $sku = trim($_POST['sku'] ?? '');
        $description = $_POST['description'] ?? '';
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

        // Handle Image Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
            finfo_close($finfo);

            if (in_array($mime, $allowedMimeTypes)) {
                $uploadDir = __DIR__ . '/../../public/uploads/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                     $data['image_path'] = '/sodai-dorkar/public/uploads/products/' . $fileName;
                }
            }
        }

        if ($id && $name) {
            $productModel = new Product();
            $productModel->update($id, $data);
        }
        
        header('Location: /sodai-dorkar/public/admin/products');
        exit;
    }

    public function destroy() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $productModel = new Product();
            $productModel->delete($id);
        }
        header('Location: /sodai-dorkar/public/admin/products');
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
}

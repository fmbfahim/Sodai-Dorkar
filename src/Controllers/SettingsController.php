<?php

namespace Controllers;

use Core\Controller;
use Core\Middleware;
use Models\Setting;
use Models\PackagingUnit;

class SettingsController extends Controller {

    public function __construct() {
        Middleware::auth(['admin']);
    }

    public function index() {
        $settingModel = new Setting();
        $settings = $settingModel->getAll();

        return $this->view('admin/settings/index', [
            'title' => 'Settings',
            'settings' => $settings,
            'activeTab' => 'general'
        ]);
    }

    public function update() {
        $settingModel = new Setting();
        
        foreach ($_POST as $key => $value) {
            $settingModel->update($key, $value);
        }

        header('Location: /sodai-dorkar/public/admin/settings?success=1');
        exit;
    }

    public function ecommerce() {
        $settingModel = new Setting();
        $settings = $settingModel->getAll();
        
        $validTabs = ['products', 'shipping', 'payments', 'privacy', 'visibility'];
        $tab = $_GET['tab'] ?? 'products';
        if (!in_array($tab, $validTabs)) {
            $tab = 'products';
        }

        $areas = [];
        $points = [];
        if ($tab === 'shipping') {
            $areaModel = new \Models\Area();
            $areas = $areaModel->all();

            $pointModel = new \Models\Point();
            $points = $pointModel->all();
        }

        return $this->view('admin/settings/ecommerce', [
            'title' => 'E-Commerce Management',
            'settings' => $settings,
            'activeTab' => $tab,
            'areas' => $areas,
            'points' => $points
        ]);
    }

    public function updateEcommerce() {
        $settingModel = new Setting();
        $activeTab = $_POST['active_tab'] ?? 'products';
        
        $excludeKeys = ['csrf_token', 'active_tab'];

        $checkboxMap = [
            'products' => [
                'manage_stock_enabled',
                'hide_out_of_stock_products',
                'show_discount_badge',
                'prices_include_tax',
                'enable_product_reviews',
                'reviews_verified_buyers_only'
            ],
            'shipping' => [
                'spend_more_offers_enabled',
                'free_shipping_enabled',
                'free_shipping_progress_bar_enabled',
                'free_shipping_exclude_heavy',
                'express_delivery_enabled',
                'delivery_weight_surcharge_enabled',
                'delivery_slots_enabled',
                'delivery_bad_weather_surcharge_enabled',
                'delivery_cod_fee_enabled',
                'allow_rider_transfer'
            ],
            'payments' => [
                'payment_cod_enabled',
                'payment_bkash_enabled',
                'payment_nagad_enabled',
                'payment_rocket_enabled',
                'payment_online_enabled'
            ],
            'privacy' => [
                'allow_guest_checkout',
                'enable_checkout_signup',
                'require_login_to_checkout',
                'auth_manual_pin_enabled',
                'auth_firebase_otp_enabled'
            ],
            'visibility' => [
                'top_announcement_bar_enabled',
                'seo_index_allow'
            ]
        ];

        // Process submitted form data
        foreach ($_POST as $key => $value) {
            if (!in_array($key, $excludeKeys)) {
                if ($key === 'spend_more_offers_tiers') {
                    if (is_array($value)) {
                        $settingModel->update($key, json_encode(array_values($value)));
                    } else {
                        $settingModel->update($key, trim($value));
                    }
                } elseif (is_array($value)) {
                    $cleanArr = [];
                    foreach ($value as $k => $v) {
                        if (is_array($v)) {
                            $cleanArr[$k] = $v;
                        } else {
                            $vTrim = trim($v);
                            if ($vTrim !== '') {
                                $cleanArr[$k] = is_numeric($vTrim) ? floatval($vTrim) : $vTrim;
                            }
                        }
                    }
                    $settingModel->update($key, json_encode($cleanArr));
                } else {
                    $settingModel->update($key, trim($value));
                }
            }
        }

        // For checkboxes belonging to the active tab, if not present in $_POST, set to '0'
        if (isset($checkboxMap[$activeTab])) {
            foreach ($checkboxMap[$activeTab] as $cbKey) {
                if (!isset($_POST[$cbKey])) {
                    $settingModel->update($cbKey, '0');
                }
            }
        }

        header('Location: /sodai-dorkar/public/admin/ecommerce-settings?tab=' . urlencode($activeTab) . '&success=1');
        exit;
    }

    public function units() {
        $unitModel = new PackagingUnit();
        $units = $unitModel->all();

        return $this->view('admin/settings/units', [
            'title' => 'Packaging & Unit Settings',
            'units' => $units,
            'activeTab' => 'units'
        ]);
    }

    public function storeUnit() {
        $name = trim($_POST['name'] ?? '');
        $baseUnit = trim($_POST['base_unit'] ?? 'kg');
        $defaultQty = floatval($_POST['default_qty'] ?? 1.000);

        if (!empty($name) && $defaultQty > 0) {
            $unitModel = new PackagingUnit();
            $unitModel->create([
                'name' => $name,
                'base_unit' => $baseUnit,
                'default_qty' => $defaultQty,
                'is_default' => !empty($_POST['is_default']) ? 1 : 0
            ]);
        }

        header('Location: /sodai-dorkar/public/admin/settings/units?success=1');
        exit;
    }

    public function deleteUnit() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $unitModel = new PackagingUnit();
            $unitModel->delete($id);
        }

        header('Location: /sodai-dorkar/public/admin/settings/units?deleted=1');
        exit;
    }
}

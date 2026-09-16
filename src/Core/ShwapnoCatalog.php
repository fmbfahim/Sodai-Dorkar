<?php

namespace Core;

class ShwapnoCatalog {

    public static function getTree() {
        return [
            [
                'slug' => 'cooking',
                'name_bn' => 'রান্নার উপাদান ও মুদি',
                'name_en' => 'Cooking & Grocery',
                'icon' => 'restaurant-outline',
                'search_query' => 'grocery',
                'children' => [
                    [
                        'slug' => 'rice',
                        'name_bn' => 'চাল ও শস্য',
                        'name_en' => 'Rice & Grains',
                        'icon' => 'leaf-outline',
                        'search_query' => 'miniket rice',
                        'children' => [
                            ['slug' => 'loose-rice', 'name_bn' => 'খোলা চাল', 'name_en' => 'Loose Rice', 'icon' => 'basket-outline', 'search_query' => 'rice loose kg'],
                            ['slug' => 'packed-rice', 'name_bn' => 'প্যাকেট চাল', 'name_en' => 'Packed Rice', 'icon' => 'cube-outline', 'search_query' => 'aroma rice pack']
                        ]
                    ],
                    [
                        'slug' => 'oil',
                        'name_bn' => 'ভোজ্য তেল ও ঘি',
                        'name_en' => 'Cooking Oil & Ghee',
                        'icon' => 'water-outline',
                        'search_query' => 'rupchanda soybean oil',
                        'children' => [
                            ['slug' => 'soybean-oil', 'name_bn' => 'সয়াবিন তেল', 'name_en' => 'Soybean Oil', 'icon' => 'water-outline', 'search_query' => 'rupchanda soybean oil 5ltr'],
                            ['slug' => 'mustard-oil', 'name_bn' => 'সরিষার তেল', 'name_en' => 'Mustard Oil', 'icon' => 'flask-outline', 'search_query' => 'radhuni mustard oil'],
                            ['slug' => 'sunflower-oil', 'name_bn' => 'সূর্যমুখী তেল', 'name_en' => 'Sunflower Oil', 'icon' => 'flower-outline', 'search_query' => 'sunflower oil'],
                            ['slug' => 'rice-bran-oil', 'name_bn' => 'রাইস ব্রান তেল', 'name_en' => 'Rice Bran Oil', 'icon' => 'color-fill-outline', 'search_query' => 'rice bran oil'],
                            ['slug' => 'olive-oil', 'name_bn' => 'অলিভ অয়েল', 'name_en' => 'Olive Oil', 'icon' => 'medkit-outline', 'search_query' => 'olive oil extra virgin']
                        ]
                    ],
                    [
                        'slug' => 'spices',
                        'name_bn' => 'মসলা ও রান্নার উপাদান',
                        'name_en' => 'Spices & Seasoning',
                        'icon' => 'flame-outline',
                        'search_query' => 'radhuni cumin',
                        'children' => [
                            ['slug' => 'Regular-Spice', 'name_bn' => 'সাধারণ গুঁড়া মসলা', 'name_en' => 'Regular Spice', 'icon' => 'flame-outline', 'search_query' => 'radhuni chilli powder'],
                            ['slug' => 'Mixed-Spice', 'name_bn' => 'মিক্সড মসলা', 'name_en' => 'Mixed Spice', 'icon' => 'grid-outline', 'search_query' => 'radhuni biryani masala'],
                            ['slug' => 'Wholespice', 'name_bn' => 'আস্ত মসলা', 'name_en' => 'Whole Spice', 'icon' => 'nutrition-outline', 'search_query' => 'shwapno darchini']
                        ]
                    ],
                    [
                        'slug' => 'daal-or-lentil',
                        'name_bn' => 'ডাল ও শিম বীজ',
                        'name_en' => 'Dal & Lentils',
                        'icon' => 'apps-outline',
                        'search_query' => 'masoor dal',
                        'children' => [
                            ['slug' => 'loose-daal', 'name_bn' => 'খোলা ডাল', 'name_en' => 'Loose Daal', 'icon' => 'basket-outline', 'search_query' => 'masoor daal loose kg'],
                            ['slug' => 'packed-daal', 'name_bn' => 'প্যাকেট ডাল', 'name_en' => 'Packed Daal', 'icon' => 'cube-outline', 'search_query' => 'pran dal 1kg']
                        ]
                    ],
                    [
                        'slug' => 'salt-and-sugar',
                        'name_bn' => 'লবণ ও চিনি',
                        'name_en' => 'Salt & Sugar',
                        'icon' => 'sparkles-outline',
                        'search_query' => 'salt iodized',
                        'children' => [
                            ['slug' => 'salt', 'name_bn' => 'লবণ', 'name_en' => 'Salt', 'icon' => 'sparkles-outline', 'search_query' => 'aci pure salt'],
                            ['slug' => 'sugar', 'name_bn' => 'চিনি ও গুড়', 'name_en' => 'Sugar', 'icon' => 'cube-outline', 'search_query' => 'fresh sugar packet']
                        ]
                    ]
                ]
            ],
            [
                'slug' => 'fruits-and-vegetables',
                'name_bn' => 'ফল ও শাকসবজি',
                'name_en' => 'Fruits & Vegetables',
                'icon' => 'nutrition-outline',
                'search_query' => 'apple fruit',
                'children' => [
                    ['slug' => 'fresh-fruits', 'name_bn' => 'তাজা ফলমূল', 'name_en' => 'Fresh Fruits', 'icon' => 'nutrition-outline', 'search_query' => 'apple fuji premium'],
                    ['slug' => 'fresh-vegetables', 'name_bn' => 'তাজা শাকসবজি', 'name_en' => 'Fresh Vegetables', 'icon' => 'flower-outline', 'search_query' => 'fresh tomato round'],
                    ['slug' => 'dry-fruits', 'name_bn' => 'শুকনা ফল ও বাদাম', 'name_en' => 'Dry Fruits', 'icon' => 'fitness-outline', 'search_query' => 'almond kaju dry fruit'],
                    ['slug' => 'dry-vegetables', 'name_bn' => 'শুকনা সবজি', 'name_en' => 'Dry Vegetables', 'icon' => 'leaf-outline', 'search_query' => 'dry mushroom vegetable']
                ]
            ],
            [
                'slug' => 'meat-and-fish',
                'name_bn' => 'মাছ ও মাংস',
                'name_en' => 'Meat & Fish',
                'icon' => 'fish-outline',
                'search_query' => 'fresh fish',
                'children' => [
                    [
                        'slug' => 'fish',
                        'name_bn' => 'মাছ ও সি-ফুড',
                        'name_en' => 'Fish & Seafood',
                        'icon' => 'fish-outline',
                        'search_query' => 'rui fish local',
                        'children' => [
                            ['slug' => 'fresh-water-fish', 'name_bn' => 'মিঠাপানির মাছ', 'name_en' => 'Fresh Water Fish', 'icon' => 'water-outline', 'search_query' => 'rui fresh cultured kg'],
                            ['slug' => 'sea-fish', 'name_bn' => 'সামুদ্রিক মাছ', 'name_en' => 'Sea Fish', 'icon' => 'boat-outline', 'search_query' => 'rupchanda sea fish']
                        ]
                    ],
                    [
                        'slug' => 'meat',
                        'name_bn' => 'মাংস ও ডিম',
                        'name_en' => 'Meat & Eggs',
                        'icon' => 'restaurant-outline',
                        'search_query' => 'beef premium cube',
                        'children' => [
                            ['slug' => 'beef', 'name_bn' => 'গরুর মাংস', 'name_en' => 'Beef', 'icon' => 'restaurant-outline', 'search_query' => 'beef premium bone kg'],
                            ['slug' => 'chicken', 'name_bn' => 'মুরগির মাংস', 'name_en' => 'Chicken', 'icon' => 'egg-outline', 'search_query' => 'broiler chicken kg'],
                            ['slug' => 'mutton', 'name_bn' => 'খাসির মাংস', 'name_en' => 'Mutton', 'icon' => 'restaurant-outline', 'search_query' => 'mutton kg'],
                            ['slug' => 'eggs', 'name_bn' => 'ডিম', 'name_en' => 'Eggs', 'icon' => 'egg-outline', 'search_query' => 'red layer chicken egg']
                        ]
                    ]
                ]
            ],
            [
                'slug' => 'beverages',
                'name_bn' => 'পানীয় ও চা-কফি',
                'name_en' => 'Drinks & Beverages',
                'icon' => 'wine-outline',
                'search_query' => 'juice drink',
                'children' => [
                    [
                        'slug' => 'tea',
                        'name_bn' => 'চা',
                        'name_en' => 'Tea',
                        'icon' => 'cafe-outline',
                        'search_query' => 'taaza tea',
                        'children' => [
                            ['slug' => 'Black-Tea-2', 'name_bn' => 'ব্ল্যাক টি (কালো চা)', 'name_en' => 'Black Tea', 'icon' => 'cafe-outline', 'search_query' => 'taaza danedar tea 200gm'],
                            ['slug' => 'Green-Tea-2', 'name_bn' => 'গ্রিন টি', 'name_en' => 'Green Tea', 'icon' => 'leaf-outline', 'search_query' => 'tetley green tea bag'],
                            ['slug' => 'Flavored-Tea', 'name_bn' => 'ফ্লেভার্ড টি', 'name_en' => 'Flavored Tea', 'icon' => 'heart-outline', 'search_query' => 'ispahani ginger tea']
                        ]
                    ],
                    [
                        'slug' => 'coffee',
                        'name_bn' => 'কফি',
                        'name_en' => 'Coffee',
                        'icon' => 'cafe-outline',
                        'search_query' => 'nescafe coffee',
                        'children' => [
                            ['slug' => 'Instant-Coffee', 'name_bn' => 'ইনস্ট্যান্ট কফি', 'name_en' => 'Instant Coffee', 'icon' => 'cafe-outline', 'search_query' => 'nescafe classic coffee 50g'],
                            ['slug' => 'Coffee-Mate', 'name_bn' => 'কফি মেট ও ক্রিমার', 'name_en' => 'Coffee Mate', 'icon' => 'flask-outline', 'search_query' => 'nestle coffee mate']
                        ]
                    ],
                    ['slug' => 'juice', 'name_bn' => 'জুস ও ফলের রস', 'name_en' => 'Juice', 'icon' => 'wine-outline', 'search_query' => 'frutika mango juice'],
                    ['slug' => 'soft-drinks', 'name_bn' => 'সফট ড্রিঙ্কস ও কোল্ড ড্রিঙ্কস', 'name_en' => 'Soft Drinks', 'icon' => 'beer-outline', 'search_query' => 'coca cola bottle'],
                    ['slug' => 'drinking-water', 'name_bn' => 'খাবার পানি', 'name_en' => 'Drinking Water', 'icon' => 'water-outline', 'search_query' => 'mum mineral water 500ml']
                ]
            ],
            [
                'slug' => 'dairy',
                'name_bn' => 'দুধ ও দুগ্ধজাত সামগ্রী',
                'name_en' => 'Dairy & Milk',
                'icon' => 'nutrition-outline',
                'search_query' => 'diploma milk powder',
                'children' => [
                    [
                        'slug' => 'liquid-and-uht-milk',
                        'name_bn' => 'তরল ও পাস্তুরিত দুধ',
                        'name_en' => 'Liquid & UHT Milk',
                        'icon' => 'water-outline',
                        'search_query' => 'aarong liquid milk 1ltr',
                        'children' => [
                            ['slug' => 'Low-Fat-Milk-2', 'name_bn' => 'লো ফ্যাট তরল দুধ', 'name_en' => 'Low Fat Milk', 'icon' => 'water-outline', 'search_query' => 'pran uht low fat milk']
                        ]
                    ],
                    [
                        'slug' => 'powder-milk',
                        'name_bn' => 'গুঁড়া দুধ',
                        'name_en' => 'Powder Milk',
                        'icon' => 'color-fill-outline',
                        'search_query' => 'diploma instant milk powder',
                        'children' => [
                            ['slug' => 'Full-Cream-Milk', 'name_bn' => 'ফুল ক্রিম গুঁড়া দুধ', 'name_en' => 'Full Cream Milk', 'icon' => 'cube-outline', 'search_query' => 'marks full cream milk powder'],
                            ['slug' => 'Diabetic-Milk', 'name_bn' => 'ডায়াবেটিক দুধ', 'name_en' => 'Diabetic Milk', 'icon' => 'medkit-outline', 'search_query' => 'aarong low fat powder milk']
                        ]
                    ],
                    ['slug' => 'ghee', 'name_bn' => 'ঘি', 'name_en' => 'Ghee', 'icon' => 'flame-outline', 'search_query' => 'aarong dairy pure ghee'],
                    ['slug' => 'butter', 'name_bn' => 'মাখন (বাটার)', 'name_en' => 'Butter', 'icon' => 'cube-outline', 'search_query' => 'aarong salted butter'],
                    ['slug' => 'Cheese', 'name_bn' => 'পনির (চীজ)', 'name_en' => 'Cheese', 'icon' => 'grid-outline', 'search_query' => 'aarong cheese slice'],
                    ['slug' => 'Yogurt', 'name_bn' => 'দই ও মিষ্টি দই', 'name_en' => 'Yogurt', 'icon' => 'nutrition-outline', 'search_query' => 'aarong sweet curd']
                ]
            ],
            [
                'slug' => 'snacks',
                'name_bn' => 'বিস্কুট ও স্ন্যাক্স',
                'name_en' => 'Snacks & Bakery',
                'icon' => 'pizza-outline',
                'search_query' => 'digestive biscuit',
                'children' => [
                    [
                        'slug' => 'biscuits',
                        'name_bn' => 'বিস্কুট ও কুকিজ',
                        'name_en' => 'Biscuits',
                        'icon' => 'disc-outline',
                        'search_query' => 'olympic digestive biscuit',
                        'children' => [
                            ['slug' => 'Energy-Biscuit', 'name_bn' => 'এনার্জি বিস্কুট', 'name_en' => 'Energy Biscuit', 'icon' => 'flash-outline', 'search_query' => 'olympic energy plus biscuit'],
                            ['slug' => 'Toast-Biscuit', 'name_bn' => 'টোস্ট বিস্কুট', 'name_en' => 'Toast Biscuit', 'icon' => 'square-outline', 'search_query' => 'pran premium toast biscuit'],
                            ['slug' => 'Cream-Sandwich-Biscuits', 'name_bn' => 'ক্রিম বিস্কুট', 'name_en' => 'Cream Biscuit', 'icon' => 'albums-outline', 'search_query' => 'oreo cream biscuit'],
                            ['slug' => 'Dry-Cake', 'name_bn' => 'ড্রাই কেক', 'name_en' => 'Dry Cake', 'icon' => 'cafe-outline', 'search_query' => 'all time dry cake']
                        ]
                    ],
                    [
                        'slug' => 'Noodles',
                        'name_bn' => 'নুডলস ও পাস্তা',
                        'name_en' => 'Noodles & Pasta',
                        'icon' => 'restaurant-outline',
                        'search_query' => 'maggi noodles 8 pack',
                        'children' => [
                            ['slug' => 'noodles-instant', 'name_bn' => 'ইনস্ট্যান্ট নুডলস', 'name_en' => 'Instant Noodles', 'icon' => 'restaurant-outline', 'search_query' => 'maggi 2 minute noodles'],
                            ['slug' => 'Pasta', 'name_bn' => 'পাস্তা ও ম্যাক্রোনি', 'name_en' => 'Pasta', 'icon' => 'grid-outline', 'search_query' => 'dano pasta packet']
                        ]
                    ],
                    ['slug' => 'chips-and-pretzels', 'name_bn' => 'চিপস ও পপকর্ন', 'name_en' => 'Chips & Snacks', 'icon' => 'triangle-outline', 'search_query' => 'lays potato chips'],
                    ['slug' => 'cakes', 'name_bn' => 'কেক ও পেস্ট্রি', 'name_en' => 'Cakes', 'icon' => 'heart-outline', 'search_query' => 'all time vanilla pound cake'],
                    ['slug' => 'soup', 'name_bn' => 'সুপ', 'name_en' => 'Soup', 'icon' => 'bowl-outline', 'search_query' => 'knorr corn chicken soup']
                ]
            ],
            [
                'slug' => 'baking-needs',
                'name_bn' => 'আটা, ময়দা ও বেকিং',
                'name_en' => 'Flour & Baking Needs',
                'icon' => 'color-fill-outline',
                'search_query' => 'pusti atta 2kg',
                'children' => [
                    ['slug' => 'flours', 'name_bn' => 'আটা, ময়দা ও সুজি', 'name_en' => 'Atta, Maida & Suji', 'icon' => 'color-fill-outline', 'search_query' => 'pusti atta 2kg packet'],
                    ['slug' => 'baking-ingredients', 'name_bn' => 'বেকিং উপাদান ও ঈস্ট', 'name_en' => 'Baking Ingredients', 'icon' => 'flask-outline', 'search_query' => 'baking powder foster clark']
                ]
            ],
            [
                'slug' => 'cleaning',
                'name_bn' => 'পরিষ্কার পরিচ্ছন্নতা',
                'name_en' => 'Home Cleaning',
                'icon' => 'sparkles-outline',
                'search_query' => 'wheel detergent powder',
                'children' => [
                    ['slug' => 'laundry-cleaning', 'name_bn' => 'লন্ড্রি ও ডিটারজেন্ট', 'name_en' => 'Laundry & Detergent', 'icon' => 'shirt-outline', 'search_query' => 'wheel 2in1 detergent 1kg'],
                    ['slug' => 'dish-wash', 'name_bn' => 'বাসন পরিষ্কার (ডিশ ওয়াশ)', 'name_en' => 'Dish Wash', 'icon' => 'water-outline', 'search_query' => 'vim dishwash liquid 500ml'],
                    ['slug' => 'toilet-cleaners', 'name_bn' => 'টয়লেট ও ফ্লোর ক্লিনার', 'name_en' => 'Toilet Cleaner', 'icon' => 'sparkles-outline', 'search_query' => 'harpic toilet cleaner 750ml'],
                    ['slug' => 'air-fresheners', 'name_bn' => 'এয়ার ফ্রেশনার ও কীটনাশক', 'name_en' => 'Air Freshener', 'icon' => 'flame-outline', 'search_query' => 'hit mosquito spray aerosol']
                ]
            ],
            [
                'slug' => 'baby-food-care',
                'name_bn' => 'শিশু খাদ্য ও যত্ন',
                'name_en' => 'Baby Food & Care',
                'icon' => 'happy-outline',
                'search_query' => 'cerelac baby food',
                'children' => [
                    ['slug' => 'baby-food', 'name_bn' => 'শিশু খাদ্য (সেরেল্যাক ও ফর্মুলা)', 'name_en' => 'Baby Food', 'icon' => 'heart-outline', 'search_query' => 'nestle cerelac wheat apple'],
                    ['slug' => 'diapers', 'name_bn' => 'ডায়াপার ও বেবি ওয়াইপস', 'name_en' => 'Diapers & Wipes', 'icon' => 'shield-checkmark-outline', 'search_query' => 'pampers baby diaper'],
                    ['slug' => 'baby-skin-care', 'name_bn' => 'বেবি স্কিন কেয়ার ও সাবান', 'name_en' => 'Baby Skin Care', 'icon' => 'body-outline', 'search_query' => 'johnson baby lotion']
                ]
            ],
            [
                'slug' => 'personal-care',
                'name_bn' => 'পার্সোনাল কেয়ার ও প্রসাধন',
                'name_en' => 'Personal Care',
                'icon' => 'body-outline',
                'search_query' => 'lux soap rose',
                'children' => [
                    ['slug' => 'soaps-body-wash', 'name_bn' => 'সাবান ও বডি ওয়াশ', 'name_en' => 'Soaps & Body Wash', 'icon' => 'water-outline', 'search_query' => 'lux beauty soap 100g'],
                    ['slug' => 'hair-care', 'name_bn' => 'শ্যাম্পু ও হেয়ার কেয়ার', 'name_en' => 'Hair Care', 'icon' => 'sparkles-outline', 'search_query' => 'sunsilk black shine shampoo'],
                    ['slug' => 'skin-care-lotion', 'name_bn' => 'স্কিন কেয়ার ও লোশন', 'name_en' => 'Skin Care', 'icon' => 'heart-outline', 'search_query' => 'vaseline intensive care lotion'],
                    ['slug' => 'oral-care', 'name_bn' => 'ওরাল কেয়ার ও টুথপেস্ট', 'name_en' => 'Oral Care', 'icon' => 'happy-outline', 'search_query' => 'colgate total toothpaste']
                ]
            ],
            [
                'slug' => 'Frozen',
                'name_bn' => 'ফ্রোজেন খাবার',
                'name_en' => 'Frozen Food',
                'icon' => 'snow-outline',
                'search_query' => 'frozen paratha',
                'children' => [
                    ['slug' => 'Paratha', 'name_bn' => 'পরোটা ও রুটি', 'name_en' => 'Paratha & Roti', 'icon' => 'disc-outline', 'search_query' => 'kazi farms paratha packet'],
                    ['slug' => 'Nuggets', 'name_bn' => 'নাগেটস ও ফ্রাইস', 'name_en' => 'Nuggets', 'icon' => 'fast-food-outline', 'search_query' => 'chicken nuggets kazi farms'],
                    ['slug' => 'Singara', 'name_bn' => 'সিঙ্গাড়া ও সমুচা', 'name_en' => 'Singara & Samosa', 'icon' => 'triangle-outline', 'search_query' => 'golden harvest singara']
                ]
            ],
            [
                'slug' => 'sauces-and-pickles',
                'name_bn' => 'সস ও আচার',
                'name_en' => 'Sauces & Pickles',
                'icon' => 'flask-outline',
                'search_query' => 'tomato sauce maggi',
                'children' => [
                    ['slug' => 'pickle-and-condiments', 'name_bn' => 'আচার ও চাটনি', 'name_en' => 'Pickles', 'icon' => 'nutrition-outline', 'search_query' => 'pran mango pickle'],
                    ['slug' => 'dipping-sauce', 'name_bn' => 'টমেটো সস ও কেচাপ', 'name_en' => 'Sauces & Ketchup', 'icon' => 'flask-outline', 'search_query' => 'maggi rich tomato ketchup']
                ]
            ],
            [
                'slug' => 'breakfast',
                'name_bn' => 'সকালের নাস্তা',
                'name_en' => 'Breakfast Items',
                'icon' => 'sunny-outline',
                'search_query' => 'bread jam breakfast',
                'children' => [
                    ['slug' => 'breads', 'name_bn' => 'পাউরুটি ও বান', 'name_en' => 'Breads', 'icon' => 'square-outline', 'search_query' => 'all time milk bread'],
                    ['slug' => 'jam-and-jelly', 'name_bn' => 'জ্যাম ও জেলি', 'name_en' => 'Jam & Jelly', 'icon' => 'heart-outline', 'search_query' => 'ahmed mixed fruit jam'],
                    ['slug' => 'honey', 'name_bn' => 'মধু', 'name_en' => 'Honey', 'icon' => 'color-fill-outline', 'search_query' => 'dabur honey bottle'],
                    ['slug' => 'cereals', 'name_bn' => 'সিরিয়ালস ও ওটস', 'name_en' => 'Cereals & Oats', 'icon' => 'bowl-outline', 'search_query' => 'kelloggs corn flakes']
                ]
            ]
        ];
    }

    /**
     * Return flattened list of categories sorted depth-first
     * with parent_slug, level (1, 2, 3), and combined display name.
     */
    public static function getFlattenedList() {
        $tree = self::getTree();
        $flattened = [];

        foreach ($tree as $main) {
            $mainFullName = $main['name_bn'] . ' (' . $main['name_en'] . ')';
            $flattened[] = [
                'slug' => $main['slug'],
                'name' => $mainFullName,
                'name_bn' => $main['name_bn'],
                'name_en' => $main['name_en'],
                'level' => 1,
                'parent_slug' => null,
                'parent_name' => null,
                'icon' => $main['icon'],
                'search_query' => $main['search_query'],
                'children_count' => count($main['children'] ?? [])
            ];

            if (!empty($main['children'])) {
                foreach ($main['children'] as $sub) {
                    $subFullName = $sub['name_bn'] . ' (' . $sub['name_en'] . ')';
                    $flattened[] = [
                        'slug' => $sub['slug'],
                        'name' => $subFullName,
                        'name_bn' => $sub['name_bn'],
                        'name_en' => $sub['name_en'],
                        'level' => 2,
                        'parent_slug' => $main['slug'],
                        'parent_name' => $mainFullName,
                        'icon' => $sub['icon'] ?? $main['icon'],
                        'search_query' => $sub['search_query'] ?? $sub['slug'],
                        'children_count' => count($sub['children'] ?? [])
                    ];

                    if (!empty($sub['children'])) {
                        foreach ($sub['children'] as $leaf) {
                            $leafFullName = $leaf['name_bn'] . ' (' . $leaf['name_en'] . ')';
                            $flattened[] = [
                                'slug' => $leaf['slug'],
                                'name' => $leafFullName,
                                'name_bn' => $leaf['name_bn'],
                                'name_en' => $leaf['name_en'],
                                'level' => 3,
                                'parent_slug' => $sub['slug'],
                                'parent_name' => $subFullName,
                                'icon' => $leaf['icon'] ?? $sub['icon'] ?? $main['icon'],
                                'search_query' => $leaf['search_query'] ?? $leaf['slug'],
                                'children_count' => 0
                            ];
                        }
                    }
                }
            }
        }

        return $flattened;
    }

    /**
     * Find item details by slug or name
     */
    public static function findItem($slugOrName) {
        $flat = self::getFlattenedList();
        $target = strtolower(trim($slugOrName));
        foreach ($flat as $item) {
            if (strtolower($item['slug']) === $target || 
                strtolower($item['name_bn']) === $target || 
                strtolower($item['name_en']) === $target ||
                strtolower($item['name']) === $target) {
                return $item;
            }
        }
        return null;
    }
}

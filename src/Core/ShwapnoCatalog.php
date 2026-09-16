<?php

namespace Core;

class ShwapnoCatalog {

    /**
     * Complete Reference 11 Root Categories with Multi-Level Subcategories
     * 1. Food (খাদ্য ও মুদি)
     * 2. Baby Food & Care (শিশু খাদ্য ও যত্ন)
     * 3. Diapers (ডায়াপার)
     * 4. Home Cleaning (বাসাবাড়ি পরিষ্কার)
     * 5. Pet Care (পেট কেয়ার)
     * 6. Beauty & Health (সৌন্দর্য ও স্বাস্থ্য)
     * 7. Fashion & Lifestyle (ফ্যাশন ও লাইফস্টাইল)
     * 8. Home & Kitchen (হোম ও কিচেন)
     * 9. Stationeries (স্টেশনারি)
     * 10. Toys & Sports (খেলনা ও খেলাধুলা)
     * 11. Gadget (গ্যাজেট)
     */
    public static function getTree() {
        return [
            // 1. Food (খাদ্য ও মুদি)
            [
                'slug' => 'food',
                'name_bn' => 'খাদ্য ও মুদি',
                'name_en' => 'Food & Grocery',
                'icon' => 'basket-outline',
                'search_query' => 'grocery',
                'children' => [
                    [
                        'slug' => 'cooking',
                        'name_bn' => 'রান্নার উপাদান ও মুদি',
                        'name_en' => 'Cooking & Grocery',
                        'icon' => 'restaurant-outline',
                        'search_query' => 'grocery food',
                        'children' => [
                            [
                                'slug' => 'rice',
                                'name_bn' => 'চাল ও শস্য',
                                'name_en' => 'Rice & Grains',
                                'icon' => 'leaf-outline',
                                'search_query' => 'miniket rice',
                                'children' => [
                                    ['slug' => 'packed-rice', 'name_bn' => 'প্যাকেট চাল', 'name_en' => 'Packed Rice', 'icon' => 'cube-outline', 'search_query' => 'aroma rice pack'],
                                    ['slug' => 'loose-rice', 'name_bn' => 'খোলা চাল', 'name_en' => 'Loose Rice', 'icon' => 'basket-outline', 'search_query' => 'miniket loose rice'],
                                    ['slug' => 'polao-rice', 'name_bn' => 'পোলাও ও বাসমতি চাল', 'name_en' => 'Polao & Basmati Rice', 'icon' => 'sparkles-outline', 'search_query' => 'chinigura polao rice']
                                ]
                            ],
                            [
                                'slug' => 'oil',
                                'name_bn' => 'ভোজ্য তেল ও ঘি',
                                'name_en' => 'Cooking Oil & Ghee',
                                'icon' => 'water-outline',
                                'search_query' => 'soybean oil',
                                'children' => [
                                    ['slug' => 'soybean-oil', 'name_bn' => 'সয়াবিন তেল', 'name_en' => 'Soybean Oil', 'icon' => 'water-outline', 'search_query' => 'rupchanda soybean oil 5ltr'],
                                    ['slug' => 'mustard-oil', 'name_bn' => 'সরিষার তেল', 'name_en' => 'Mustard Oil', 'icon' => 'flask-outline', 'search_query' => 'radhuni mustard oil'],
                                    ['slug' => 'sunflower-oil', 'name_bn' => 'সূর্যমুখী তেল', 'name_en' => 'Sunflower Oil', 'icon' => 'flower-outline', 'search_query' => 'sunflower oil'],
                                    ['slug' => 'ghee', 'name_bn' => 'ঘি ও বাটার অয়েল', 'name_en' => 'Ghee & Butter Oil', 'icon' => 'color-fill-outline', 'search_query' => 'aarong dairy ghee'],
                                    ['slug' => 'olive-oil', 'name_bn' => 'অলিভ অয়েল', 'name_en' => 'Olive Oil', 'icon' => 'medkit-outline', 'search_query' => 'olive oil extra virgin']
                                ]
                            ],
                            [
                                'slug' => 'spices',
                                'name_bn' => 'মসলা ও রান্নার উপাদান',
                                'name_en' => 'Spices & Seasoning',
                                'icon' => 'flame-outline',
                                'search_query' => 'radhuni spices',
                                'children' => [
                                    ['slug' => 'powder-spice', 'name_bn' => 'গুঁড়া মসলা', 'name_en' => 'Powder Spice', 'icon' => 'flame-outline', 'search_query' => 'radhuni turmeric powder'],
                                    ['slug' => 'whole-spice', 'name_bn' => 'আস্ত মসলা', 'name_en' => 'Whole Spice', 'icon' => 'nutrition-outline', 'search_query' => 'cardamom elaichi whole'],
                                    ['slug' => 'mixed-spice', 'name_bn' => 'মিক্সড মসলা', 'name_en' => 'Mixed Spice', 'icon' => 'grid-outline', 'search_query' => 'radhuni biryani masala']
                                ]
                            ],
                            [
                                'slug' => 'daal-or-lentil',
                                'name_bn' => 'ডাল ও শিম বীজ',
                                'name_en' => 'Dal & Lentils',
                                'icon' => 'apps-outline',
                                'search_query' => 'masoor dal',
                                'children' => [
                                    ['slug' => 'masoor-dal', 'name_bn' => 'মসুর ডাল', 'name_en' => 'Masoor Dal', 'icon' => 'basket-outline', 'search_query' => 'masoor daal premium'],
                                    ['slug' => 'moong-dal', 'name_bn' => 'মুগ ডাল', 'name_en' => 'Moong Dal', 'icon' => 'leaf-outline', 'search_query' => 'moong daal yellow'],
                                    ['slug' => 'chola-boot', 'name_bn' => 'ছোলা ও বুটের ডাল', 'name_en' => 'Chola & Booter Dal', 'icon' => 'cube-outline', 'search_query' => 'chola boot gram']
                                ]
                            ],
                            [
                                'slug' => 'flours',
                                'name_bn' => 'আটা, ময়দা ও সুজি',
                                'name_en' => 'Atta, Flour & Suji',
                                'icon' => 'layers-outline',
                                'search_query' => 'teer atta',
                                'children' => [
                                    ['slug' => 'atta', 'name_bn' => 'আটা', 'name_en' => 'Atta', 'icon' => 'cube-outline', 'search_query' => 'teer atta 2kg'],
                                    ['slug' => 'maida', 'name_bn' => 'ময়দা', 'name_en' => 'Maida', 'icon' => 'layers-outline', 'search_query' => 'teer maida 2kg'],
                                    ['slug' => 'suji', 'name_bn' => 'সুজি', 'name_en' => 'Suji', 'icon' => 'apps-outline', 'search_query' => 'pusti suji pack']
                                ]
                            ],
                            [
                                'slug' => 'salt-and-sugar',
                                'name_bn' => 'লবণ ও চিনি',
                                'name_en' => 'Salt & Sugar',
                                'icon' => 'sparkles-outline',
                                'search_query' => 'salt iodized',
                                'children' => [
                                    ['slug' => 'salt', 'name_bn' => 'লবণ', 'name_en' => 'Salt', 'icon' => 'sparkles-outline', 'search_query' => 'aci pure salt 1kg'],
                                    ['slug' => 'sugar', 'name_bn' => 'চিনি ও গুড়', 'name_en' => 'Sugar', 'icon' => 'cube-outline', 'search_query' => 'fresh white sugar packet']
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
                            ['slug' => 'fresh-fruits', 'name_bn' => 'তাজা ফলমূল', 'name_en' => 'Fresh Fruits', 'icon' => 'nutrition-outline', 'search_query' => 'apple fuji fresh'],
                            ['slug' => 'fresh-vegetables', 'name_bn' => 'তাজা শাকসবজি', 'name_en' => 'Fresh Vegetables', 'icon' => 'flower-outline', 'search_query' => 'fresh potato tomato'],
                            ['slug' => 'dry-fruits', 'name_bn' => 'শুকনা ফল ও বাদাম', 'name_en' => 'Dry Fruits & Nuts', 'icon' => 'fitness-outline', 'search_query' => 'kaju badam almond']
                        ]
                    ],
                    [
                        'slug' => 'meat-and-fish',
                        'name_bn' => 'মাছ ও মাংস',
                        'name_en' => 'Meat & Fish',
                        'icon' => 'fish-outline',
                        'search_query' => 'fresh fish rui',
                        'children' => [
                            [
                                'slug' => 'fish',
                                'name_bn' => 'মাছ ও সি-ফুড',
                                'name_en' => 'Fish & Seafood',
                                'icon' => 'fish-outline',
                                'search_query' => 'rui fish fresh',
                                'children' => [
                                    ['slug' => 'fresh-water-fish', 'name_bn' => 'মিঠাপানির মাছ', 'name_en' => 'Fresh Water Fish', 'icon' => 'water-outline', 'search_query' => 'rui fish freshwater cut'],
                                    ['slug' => 'sea-fish', 'name_bn' => 'সামুদ্রিক মাছ', 'name_en' => 'Sea Fish', 'icon' => 'boat-outline', 'search_query' => 'ilish hilsa fish']
                                ]
                            ],
                            [
                                'slug' => 'meat-and-eggs',
                                'name_bn' => 'মাংস ও ডিম',
                                'name_en' => 'Meat & Eggs',
                                'icon' => 'restaurant-outline',
                                'search_query' => 'broiler chicken fresh',
                                'children' => [
                                    ['slug' => 'chicken', 'name_bn' => 'মুরগির মাংস', 'name_en' => 'Chicken', 'icon' => 'egg-outline', 'search_query' => 'broiler chicken skin off'],
                                    ['slug' => 'beef', 'name_bn' => 'গরুর মাংস', 'name_en' => 'Beef', 'icon' => 'restaurant-outline', 'search_query' => 'beef meat premium bone in'],
                                    ['slug' => 'mutton', 'name_bn' => 'খাসির মাংস', 'name_en' => 'Mutton', 'icon' => 'pizza-outline', 'search_query' => 'mutton fresh meat'],
                                    ['slug' => 'eggs', 'name_bn' => 'ডিম', 'name_en' => 'Eggs', 'icon' => 'egg-outline', 'search_query' => 'farm fresh chicken egg 12 pcs']
                                ]
                            ]
                        ]
                    ],
                    [
                        'slug' => 'beverages',
                        'name_bn' => 'পানীয় ও চা-কফি',
                        'name_en' => 'Beverages, Tea & Coffee',
                        'icon' => 'cafe-outline',
                        'search_query' => 'taaza tea packet',
                        'children' => [
                            [
                                'slug' => 'tea',
                                'name_bn' => 'চা',
                                'name_en' => 'Tea',
                                'icon' => 'cafe-outline',
                                'search_query' => 'ispahani mirzapore tea',
                                'children' => [
                                    ['slug' => 'black-tea', 'name_bn' => 'কালো চা', 'name_en' => 'Black Tea', 'icon' => 'cafe-outline', 'search_query' => 'taaza black tea 400g'],
                                    ['slug' => 'green-tea', 'name_bn' => 'গ্রিন টি', 'name_en' => 'Green Tea', 'icon' => 'leaf-outline', 'search_query' => 'tetley green tea bag']
                                ]
                            ],
                            [
                                'slug' => 'coffee',
                                'name_bn' => 'কফি',
                                'name_en' => 'Coffee',
                                'icon' => 'beer-outline',
                                'search_query' => 'nescafe classic coffee',
                                'children' => [
                                    ['slug' => 'instant-coffee', 'name_bn' => 'ইনস্ট্যান্ট কফি', 'name_en' => 'Instant Coffee', 'icon' => 'cafe-outline', 'search_query' => 'nescafe instant jar']
                                ]
                            ],
                            [
                                'slug' => 'juice-drinks',
                                'name_bn' => 'জুস ও পানীয়',
                                'name_en' => 'Juice & Drinks',
                                'icon' => 'wine-outline',
                                'search_query' => 'mango juice bottle',
                                'children' => [
                                    ['slug' => 'fruit-juice', 'name_bn' => 'ফলের জুস', 'name_en' => 'Fruit Juice', 'icon' => 'nutrition-outline', 'search_query' => 'pran mango juice'],
                                    ['slug' => 'soft-drinks', 'name_bn' => 'কোমল পানীয়', 'name_en' => 'Soft Drinks', 'icon' => 'flask-outline', 'search_query' => 'coca cola 1 ltr'],
                                    ['slug' => 'mineral-water', 'name_bn' => 'মিনারেল ওয়াটার', 'name_en' => 'Mineral Water', 'icon' => 'water-outline', 'search_query' => 'mum drinking water 500ml']
                                ]
                            ]
                        ]
                    ],
                    [
                        'slug' => 'dairy',
                        'name_bn' => 'দুধ ও দুগ্ধজাত সামগ্রী',
                        'name_en' => 'Dairy & Milk',
                        'icon' => 'color-fill-outline',
                        'search_query' => 'milk liquid uht',
                        'children' => [
                            ['slug' => 'liquid-milk', 'name_bn' => 'তরল ও পাস্তুরিত দুধ', 'name_en' => 'Liquid & Pasteurized Milk', 'icon' => 'water-outline', 'search_query' => 'aarong liquid milk 1ltr'],
                            ['slug' => 'powder-milk', 'name_bn' => 'গুঁড়া দুধ', 'name_en' => 'Powder Milk', 'icon' => 'cube-outline', 'search_query' => 'dano power milk powder 1kg'],
                            ['slug' => 'butter-cheese', 'name_bn' => 'মাখন ও পনির', 'name_en' => 'Butter & Cheese', 'icon' => 'grid-outline', 'search_query' => 'aarong salted butter 200g'],
                            ['slug' => 'yogurt-sweets', 'name_bn' => 'দই ও মিষ্টি', 'name_en' => 'Yogurt & Curd', 'icon' => 'egg-outline', 'search_query' => 'aarong sweet curd doi']
                        ]
                    ],
                    [
                        'slug' => 'snacks',
                        'name_bn' => 'বিস্কুট ও স্ন্যাক্স',
                        'name_en' => 'Snacks & Bakery',
                        'icon' => 'pizza-outline',
                        'search_query' => 'digestive biscuit',
                        'children' => [
                            ['slug' => 'biscuits', 'name_bn' => 'বিস্কুট ও কুকিজ', 'name_en' => 'Biscuits & Cookies', 'icon' => 'disc-outline', 'search_query' => 'lexus biscuit olimpik'],
                            ['slug' => 'chips-chanachur', 'name_bn' => 'চিপস ও চানাচুর', 'name_en' => 'Chips & Chanachur', 'icon' => 'flame-outline', 'search_query' => 'lays potato chips'],
                            ['slug' => 'chocolates', 'name_bn' => 'চকলেট ও ক্যান্ডি', 'name_en' => 'Chocolates & Candies', 'icon' => 'heart-outline', 'search_query' => 'cadbury dairy milk'],
                            ['slug' => 'noodles-pasta', 'name_bn' => 'নুডলস ও পাস্তা', 'name_en' => 'Noodles & Pasta', 'icon' => 'restaurant-outline', 'search_query' => 'maggi noodles masala 8 pack']
                        ]
                    ],
                    [
                        'slug' => 'breakfast-bakery',
                        'name_bn' => 'সকালের নাস্তা ও বেকারি',
                        'name_en' => 'Breakfast & Bakery',
                        'icon' => 'sunny-outline',
                        'search_query' => 'bread jam breakfast',
                        'children' => [
                            ['slug' => 'breads', 'name_bn' => 'পাউরুটি ও বান', 'name_en' => 'Breads', 'icon' => 'square-outline', 'search_query' => 'all time milk bread 400g'],
                            ['slug' => 'cereals', 'name_bn' => 'সিরিয়ালস ও ওটস', 'name_en' => 'Cereals & Oats', 'icon' => 'bowl-outline', 'search_query' => 'quaker oats pouch'],
                            ['slug' => 'jam-honey', 'name_bn' => 'জ্যাম, জেলি ও মধু', 'name_en' => 'Jam, Jelly & Honey', 'icon' => 'heart-outline', 'search_query' => 'ahmed mixed fruit jam']
                        ]
                    ]
                ]
            ],

            // 2. Baby Food & Care (শিশু খাদ্য ও যত্ন)
            [
                'slug' => 'baby-food-care',
                'name_bn' => 'শিশু খাদ্য ও যত্ন',
                'name_en' => 'Baby Food & Care',
                'icon' => 'medical-outline',
                'search_query' => 'cerelac baby food',
                'children' => [
                    [
                        'slug' => 'baby-food',
                        'name_bn' => 'শিশু খাদ্য',
                        'name_en' => 'Baby Food',
                        'icon' => 'nutrition-outline',
                        'search_query' => 'nestle cerelac wheat apple',
                        'children' => [
                            ['slug' => 'baby-formula', 'name_bn' => 'শিশুর দুধ ও ফর্মুলা', 'name_en' => 'Baby Formula', 'icon' => 'color-fill-outline', 'search_query' => 'lactogen 1 baby formula'],
                            ['slug' => 'baby-cereal', 'name_bn' => 'সিরিয়াল ও পিউরি', 'name_en' => 'Baby Cereal & Puree', 'icon' => 'nutrition-outline', 'search_query' => 'cerelac rice baby cereal']
                        ]
                    ],
                    [
                        'slug' => 'baby-skin-care',
                        'name_bn' => 'শিশুর ত্বক ও প্রসাধন',
                        'name_en' => 'Baby Skin Care',
                        'icon' => 'heart-outline',
                        'search_query' => 'johnsons baby lotion',
                        'children' => [
                            ['slug' => 'baby-lotion', 'name_bn' => 'বেবি লোশন ও তেল', 'name_en' => 'Baby Lotion & Oil', 'icon' => 'water-outline', 'search_query' => 'johnsons baby oil 200ml'],
                            ['slug' => 'baby-shampoo', 'name_bn' => 'বেবি শ্যাম্পু ও সাবান', 'name_en' => 'Baby Shampoo & Soap', 'icon' => 'sparkles-outline', 'search_query' => 'johnsons baby shampoo 200ml'],
                            ['slug' => 'baby-powder', 'name_bn' => 'বেবি পাউডার', 'name_en' => 'Baby Powder', 'icon' => 'cloud-outline', 'search_query' => 'kodomo baby powder']
                        ]
                    ],
                    [
                        'slug' => 'feeding-accessories',
                        'name_bn' => 'খাওয়ানোর সরঞ্জাম',
                        'name_en' => 'Feeding Accessories',
                        'icon' => 'wine-outline',
                        'search_query' => 'baby feeder bottle',
                        'children' => [
                            ['slug' => 'feeding-bottles', 'name_bn' => 'ফিডার ও বোতল', 'name_en' => 'Feeding Bottles', 'icon' => 'flask-outline', 'search_query' => 'pigeon baby feeding bottle'],
                            ['slug' => 'teethers', 'name_bn' => 'টিথার ও প্যাসিফায়ার', 'name_en' => 'Teethers & Soothers', 'icon' => 'shield-outline', 'search_query' => 'baby silicone teether']
                        ]
                    ]
                ]
            ],

            // 3. Diapers (ডায়াপার)
            [
                'slug' => 'diapers',
                'name_bn' => 'ডায়াপার',
                'name_en' => 'Diapers & Wipes',
                'icon' => 'shield-checkmark-outline',
                'search_query' => 'pampers baby diaper',
                'children' => [
                    [
                        'slug' => 'baby-diapers',
                        'name_bn' => 'শিশুর ডায়াপার',
                        'name_en' => 'Baby Diapers',
                        'icon' => 'shield-checkmark-outline',
                        'search_query' => 'huggies pant diaper',
                        'children' => [
                            ['slug' => 'pant-diapers', 'name_bn' => 'প্যান্ট ডায়াপার', 'name_en' => 'Pant Diapers', 'icon' => 'shirt-outline', 'search_query' => 'neocare pant diaper large'],
                            ['slug' => 'tape-diapers', 'name_bn' => 'টেপ ডায়াপার', 'name_en' => 'Tape Diapers', 'icon' => 'bookmark-outline', 'search_query' => 'mamy poko pants tape diaper']
                        ]
                    ],
                    ['slug' => 'adult-diapers', 'name_bn' => 'প্রাপ্তবয়স্কদের ডায়াপার', 'name_en' => 'Adult Diapers', 'icon' => 'body-outline', 'search_query' => 'friends adult diaper large'],
                    ['slug' => 'baby-wipes', 'name_bn' => 'বেবি ওয়াইপস', 'name_en' => 'Baby Wipes', 'icon' => 'newspaper-outline', 'search_query' => 'neocare baby wipes 80 pcs']
                ]
            ],

            // 4. Home Cleaning (বাসাবাড়ি পরিষ্কার)
            [
                'slug' => 'home-cleaning',
                'name_bn' => 'বাসাবাড়ি পরিষ্কার',
                'name_en' => 'Home Cleaning',
                'icon' => 'sparkles-outline',
                'search_query' => 'wheel detergent bar harpic',
                'children' => [
                    [
                        'slug' => 'laundry-detergent',
                        'name_bn' => 'ডিটারজেন্ট ও লন্ড্রি',
                        'name_en' => 'Laundry & Detergents',
                        'icon' => 'shirt-outline',
                        'search_query' => 'wheel 2in1 washing powder',
                        'children' => [
                            ['slug' => 'detergent-powder', 'name_bn' => 'ডিটারজেন্ট পাউডার', 'name_en' => 'Detergent Powder', 'icon' => 'cube-outline', 'search_query' => 'surf excel detergent 1kg'],
                            ['slug' => 'liquid-detergent', 'name_bn' => 'লিকুইড ডিটারজেন্ট', 'name_en' => 'Liquid Detergent', 'icon' => 'water-outline', 'search_query' => 'vanish stain remover liquid'],
                            ['slug' => 'laundry-soap', 'name_bn' => 'কাপড় কাচার সাবান', 'name_en' => 'Laundry Soap', 'icon' => 'square-outline', 'search_query' => 'chaka laundry soap bar']
                        ]
                    ],
                    [
                        'slug' => 'dishwashing',
                        'name_bn' => 'থালাবাসন পরিষ্কার',
                        'name_en' => 'Dishwashing',
                        'icon' => 'restaurant-outline',
                        'search_query' => 'vim dishwash liquid 500ml',
                        'children' => [
                            ['slug' => 'dishwash-liquid-bar', 'name_bn' => 'ডিশওয়াশ বার ও লিকুইড', 'name_en' => 'Dishwash Bar & Liquid', 'icon' => 'water-outline', 'search_query' => 'vim bar dishwash']
                        ]
                    ],
                    [
                        'slug' => 'floor-toilet-cleaners',
                        'name_bn' => 'মেঝে ও বাথরুম ক্লিনার',
                        'name_en' => 'Floor & Toilet Cleaners',
                        'icon' => 'trash-outline',
                        'search_query' => 'harpic toilet cleaner 750ml',
                        'children' => [
                            ['slug' => 'toilet-cleaner', 'name_bn' => 'টয়লেট ক্লিনার', 'name_en' => 'Toilet Cleaner', 'icon' => 'trash-outline', 'search_query' => 'harpic power plus 750ml'],
                            ['slug' => 'floor-cleaner', 'name_bn' => 'ফ্লোর ক্লিনার', 'name_en' => 'Floor Cleaner', 'icon' => 'sparkles-outline', 'search_query' => 'lizol floor cleaner lavender'],
                            ['slug' => 'glass-cleaner', 'name_bn' => 'গ্লাস ক্লিনার', 'name_en' => 'Glass Cleaner', 'icon' => 'tablet-portrait-outline', 'search_query' => 'mr brasso glass cleaner']
                        ]
                    ],
                    ['slug' => 'air-fresheners', 'name_bn' => 'এয়ার ফ্রেশনার', 'name_en' => 'Air Fresheners', 'icon' => 'flower-outline', 'search_query' => 'odonil air freshener block'],
                    ['slug' => 'pest-control', 'name_bn' => 'কীটপতঙ্গ নিয়ন্ত্রণ', 'name_en' => 'Pest Control', 'icon' => 'bug-outline', 'search_query' => 'hit mosquito spray aerosol']
                ]
            ],

            // 5. Pet Care (পেট কেয়ার)
            [
                'slug' => 'pet-care',
                'name_bn' => 'পেট কেয়ার',
                'name_en' => 'Pet Care',
                'icon' => 'paw-outline',
                'search_query' => 'cat food whiskas meo',
                'children' => [
                    [
                        'slug' => 'cat-food-care',
                        'name_bn' => 'বিড়ালের খাবার ও যত্ন',
                        'name_en' => 'Cat Food & Care',
                        'icon' => 'paw-outline',
                        'search_query' => 'whiskas ocean fish cat food',
                        'children' => [
                            ['slug' => 'cat-food', 'name_bn' => 'ক্যাট ফুড', 'name_en' => 'Cat Food', 'icon' => 'nutrition-outline', 'search_query' => 'drools cat food dry'],
                            ['slug' => 'cat-litter', 'name_bn' => 'ক্যাট লিটার', 'name_en' => 'Cat Litter', 'icon' => 'trash-outline', 'search_query' => 'bentonite cat litter sand']
                        ]
                    ],
                    ['slug' => 'dog-food', 'name_bn' => 'কুকুরের খাবার', 'name_en' => 'Dog Food', 'icon' => 'nutrition-outline', 'search_query' => 'pedigree adult dog food meat'],
                    ['slug' => 'bird-fish-food', 'name_bn' => 'পাখি ও মাছের খাবার', 'name_en' => 'Bird & Fish Food', 'icon' => 'fish-outline', 'search_query' => 'tokyu fish food aquarium']
                ]
            ],

            // 6. Beauty & Health (সৌন্দর্য ও স্বাস্থ্য)
            [
                'slug' => 'beauty-health',
                'name_bn' => 'সৌন্দর্য ও স্বাস্থ্য',
                'name_en' => 'Beauty & Health',
                'icon' => 'heart-outline',
                'search_query' => 'lux soap dove shampoo',
                'children' => [
                    [
                        'slug' => 'skin-care',
                        'name_bn' => 'ত্বকের যত্ন',
                        'name_en' => 'Skin Care',
                        'icon' => 'sparkles-outline',
                        'search_query' => 'ponds face wash',
                        'children' => [
                            ['slug' => 'face-wash', 'name_bn' => 'ফেসওয়াশ', 'name_en' => 'Face Wash', 'icon' => 'water-outline', 'search_query' => 'garnier men face wash 100g'],
                            ['slug' => 'creams-moisturizers', 'name_bn' => 'ক্রিম ও লোশন', 'name_en' => 'Creams & Moisturizers', 'icon' => 'color-fill-outline', 'search_query' => 'nivea soft cream 100ml'],
                            ['slug' => 'sunscreen-serums', 'name_bn' => 'সানস্ক্রিন ও সিরাম', 'name_en' => 'Sunscreen & Serums', 'icon' => 'sunny-outline', 'search_query' => 'sunscreen spf 50 lotion']
                        ]
                    ],
                    [
                        'slug' => 'hair-care',
                        'name_bn' => 'চুলের যত্ন',
                        'name_en' => 'Hair Care',
                        'icon' => 'cut-outline',
                        'search_query' => 'sunsilk black shine shampoo',
                        'children' => [
                            ['slug' => 'shampoo', 'name_bn' => 'শ্যাম্পু', 'name_en' => 'Shampoo', 'icon' => 'water-outline', 'search_query' => 'clear men shampoo cool sport'],
                            ['slug' => 'conditioner', 'name_bn' => 'কন্ডিশনার', 'name_en' => 'Conditioner', 'icon' => 'sparkles-outline', 'search_query' => 'dove hair fall conditioner'],
                            ['slug' => 'hair-oil', 'name_bn' => 'হেয়ার অয়েল', 'name_en' => 'Hair Oil', 'icon' => 'flask-outline', 'search_query' => 'parachute coconut oil 200ml']
                        ]
                    ],
                    [
                        'slug' => 'oral-care',
                        'name_bn' => 'মুখ ও দাঁতের যত্ন',
                        'name_en' => 'Oral Care',
                        'icon' => 'medkit-outline',
                        'search_query' => 'colgate total toothpaste',
                        'children' => [
                            ['slug' => 'toothpaste', 'name_bn' => 'টুথপেস্ট', 'name_en' => 'Toothpaste', 'icon' => 'shield-checkmark-outline', 'search_query' => 'close up red hot toothpaste'],
                            ['slug' => 'toothbrush', 'name_bn' => 'টুথব্রাশ ও মাউথওয়াশ', 'name_en' => 'Toothbrush & Mouthwash', 'icon' => 'color-wand-outline', 'search_query' => 'oral b toothbrush medium']
                        ]
                    ],
                    [
                        'slug' => 'bath-body',
                        'name_bn' => 'গোসল ও সাবান',
                        'name_en' => 'Bath & Body',
                        'icon' => 'water-outline',
                        'search_query' => 'dettol soap original',
                        'children' => [
                            ['slug' => 'bath-soap', 'name_bn' => 'সাবান', 'name_en' => 'Bath Soap', 'icon' => 'cube-outline', 'search_query' => 'lux velvet touch soap 150g'],
                            ['slug' => 'body-wash', 'name_bn' => 'বডি ওয়াশ ও শাওয়ার জেল', 'name_en' => 'Body Wash', 'icon' => 'water-outline', 'search_query' => 'lifebuoy total 10 body wash']
                        ]
                    ],
                    [
                        'slug' => 'personal-hygiene',
                        'name_bn' => 'ব্যক্তিগত সুরক্ষা',
                        'name_en' => 'Personal Hygiene',
                        'icon' => 'shield-outline',
                        'search_query' => 'savlon handwash refill',
                        'children' => [
                            ['slug' => 'handwash', 'name_bn' => 'হ্যান্ডওয়াশ', 'name_en' => 'Handwash', 'icon' => 'water-outline', 'search_query' => 'savlon ocean blue handwash'],
                            ['slug' => 'sanitary-pads', 'name_bn' => 'স্যানিটারি ন্যাপকিন', 'name_en' => 'Sanitary Pads', 'icon' => 'shield-checkmark-outline', 'search_query' => 'senora confidence sanitary napkin']
                        ]
                    ]
                ]
            ],

            // 7. Fashion & Lifestyle (ফ্যাশন ও লাইফস্টাইল)
            [
                'slug' => 'fashion-lifestyle',
                'name_bn' => 'ফ্যাশন ও লাইফস্টাইল',
                'name_en' => 'Fashion & Lifestyle',
                'icon' => 'shirt-outline',
                'search_query' => 'cotton t shirt polo',
                'children' => [
                    ['slug' => 'mens-fashion', 'name_bn' => 'পুরুষদের পোশাক', 'name_en' => 'Men\'s Fashion', 'icon' => 'shirt-outline', 'search_query' => 'men cotton polo shirt'],
                    ['slug' => 'womens-fashion', 'name_bn' => 'নারীদের পোশাক', 'name_en' => 'Women\'s Fashion', 'icon' => 'woman-outline', 'search_query' => 'women kurti cotton dress'],
                    ['slug' => 'bags-luggage', 'name_bn' => 'ব্যাগ ও লাগেজ', 'name_en' => 'Bags & Luggage', 'icon' => 'briefcase-outline', 'search_query' => 'travel backpack bag'],
                    ['slug' => 'shoes-sandals', 'name_bn' => 'জুতা ও স্যান্ডেল', 'name_en' => 'Shoes & Sandals', 'icon' => 'walk-outline', 'search_query' => 'men leather sandal apex']
                ]
            ],

            // 8. Home & Kitchen (হোম ও কিচেন)
            [
                'slug' => 'home-kitchen',
                'name_bn' => 'হোম ও কিচেন',
                'name_en' => 'Home & Kitchen',
                'icon' => 'home-outline',
                'search_query' => 'non stick frying pan cookware',
                'children' => [
                    [
                        'slug' => 'cookware-kitchenware',
                        'name_bn' => 'রান্নাঘরের তৈজসপত্র',
                        'name_en' => 'Cookware & Utensils',
                        'icon' => 'restaurant-outline',
                        'search_query' => 'kiam pressure cooker pan',
                        'children' => [
                            ['slug' => 'pans-pots', 'name_bn' => 'ফ্রাইপ্যান ও কড়াই', 'name_en' => 'Pans & Pots', 'icon' => 'disc-outline', 'search_query' => 'non stick fry pan 24cm'],
                            ['slug' => 'cutlery', 'name_bn' => 'ছুরি ও চামচ', 'name_en' => 'Cutlery & Knives', 'icon' => 'cut-outline', 'search_query' => 'stainless steel spoon set']
                        ]
                    ],
                    [
                        'slug' => 'storage-containers',
                        'name_bn' => 'সংরক্ষণ ও কন্টেইনার',
                        'name_en' => 'Storage & Containers',
                        'icon' => 'cube-outline',
                        'search_query' => 'rfl plastic container box',
                        'children' => [
                            ['slug' => 'jars-containers', 'name_bn' => 'প্লাস্টিক ও কাঁচের জার', 'name_en' => 'Jars & Containers', 'icon' => 'cube-outline', 'search_query' => 'airtight storage container set'],
                            ['slug' => 'water-bottles', 'name_bn' => 'পানির বোতল ও ফ্লাস্ক', 'name_en' => 'Water Bottles & Flask', 'icon' => 'flask-outline', 'search_query' => 'thermos vacuum flask hot cold']
                        ]
                    ],
                    ['slug' => 'home-decor-linen', 'name_bn' => 'ঘর সাজানো ও বিছানার চাদর', 'name_en' => 'Home Decor & Linen', 'icon' => 'bed-outline', 'search_query' => 'cotton bed sheet double king']
                ]
            ],

            // 9. Stationeries (স্টেশনারি)
            [
                'slug' => 'stationeries',
                'name_bn' => 'স্টেশনারি',
                'name_en' => 'Stationeries & Office',
                'icon' => 'pencil-outline',
                'search_query' => 'matador ball pen paper',
                'children' => [
                    ['slug' => 'pens-pencils', 'name_bn' => 'কলম, পেন্সিল ও মার্কার', 'name_en' => 'Pens & Pencils', 'icon' => 'pencil-outline', 'search_query' => 'matador pin point ball pen pack'],
                    ['slug' => 'notebooks-paper', 'name_bn' => 'খাতা ও নোটবুক', 'name_en' => 'Notebooks & Paper', 'icon' => 'book-outline', 'search_query' => 'bashundhara a4 paper ream'],
                    ['slug' => 'office-school-supplies', 'name_bn' => 'অফিস ও স্কুলের সরবরাহ', 'name_en' => 'Office & School Supplies', 'icon' => 'folder-outline', 'search_query' => 'stapler pin punch machine'],
                    ['slug' => 'stationery-tools', 'name_bn' => 'ক্যালকুলেটর ও স্কেল', 'name_en' => 'Tools & Calculators', 'icon' => 'calculator-outline', 'search_query' => 'casio scientific calculator']
                ]
            ],

            // 10. Toys & Sports (খেলনা ও খেলাধুলা)
            [
                'slug' => 'toys-sports',
                'name_bn' => 'খেলনা ও খেলাধুলা',
                'name_en' => 'Toys & Sports',
                'icon' => 'football-outline',
                'search_query' => 'kids toys football cricket',
                'children' => [
                    ['slug' => 'baby-kids-toys', 'name_bn' => 'শিশুদের খেলনা ও পুতুল', 'name_en' => 'Baby & Kids Toys', 'icon' => 'happy-outline', 'search_query' => 'remote control car toy'],
                    ['slug' => 'learning-puzzle-toys', 'name_bn' => 'লার্নিং ও পাজল টয়', 'name_en' => 'Learning & Puzzle Toys', 'icon' => 'extension-puzzle-outline', 'search_query' => 'building blocks puzzle kids'],
                    ['slug' => 'sports-fitness-goods', 'name_bn' => 'আউটডোর ও স্পোর্টস সামগ্রী', 'name_en' => 'Sports & Fitness Goods', 'icon' => 'football-outline', 'search_query' => 'cricket bat tennis ball badminton']
                ]
            ],

            // 11. Gadget (গ্যাজেট)
            [
                'slug' => 'gadget',
                'name_bn' => 'গ্যাজেট',
                'name_en' => 'Gadgets & Tech',
                'icon' => 'phone-portrait-outline',
                'search_query' => 'earphone charging cable powerbank',
                'children' => [
                    ['slug' => 'mobile-accessories', 'name_bn' => 'মোবাইল এক্সেসরিজ', 'name_en' => 'Mobile Accessories', 'icon' => 'phone-portrait-outline', 'search_query' => 'mobile stand holder car'],
                    ['slug' => 'cables-chargers', 'name_bn' => 'ক্যাবল ও চার্জার', 'name_en' => 'Cables & Fast Chargers', 'icon' => 'flash-outline', 'search_query' => 'fast charger type c cable 65w'],
                    ['slug' => 'headphones-audio', 'name_bn' => 'হেডফোন ও অডিও', 'name_en' => 'Headphones & Audio', 'icon' => 'headset-outline', 'search_query' => 'bluetooth wireless earphone tws'],
                    ['slug' => 'powerbanks-smart-devices', 'name_bn' => 'পাওয়ার ব্যাংক ও স্মার্ট ডিভাইস', 'name_en' => 'Power Banks & Smart Devices', 'icon' => 'battery-charging-outline', 'search_query' => '10000mah fast power bank xiaomi']
                ]
            ]
        ];
    }

    /**
     * Recursive flattener that extracts all levels (1, 2, 3, 4)
     * GUARANTEES:
     * - 'name' is purely Bengali (name_bn)
     * - 'slug' is purely English (slug)
     * - 'parent_name' is purely Bengali (parent_name_bn)
     * - 'parent_slug' is purely English (parent_slug)
     * - Sorted depth-first so parent always comes before its children
     */
    public static function getFlattenedList() {
        $tree = self::getTree();
        $flattened = [];
        self::flattenSubtree($tree, $flattened, null, null, 1);
        return $flattened;
    }

    private static function flattenSubtree(array $nodes, array &$flattened, $parentSlug = null, $parentNameBn = null, $level = 1) {
        foreach ($nodes as $node) {
            $slug = $node['slug'];
            $nameBn = $node['name_bn'];
            $nameEn = $node['name_en'] ?? $slug;
            $hasChildren = !empty($node['children']);
            $childrenCount = $hasChildren ? count($node['children']) : 0;

            $flattened[] = [
                'slug' => $slug,
                'name' => $nameBn, // Pure Bengali Name
                'name_bn' => $nameBn,
                'name_en' => $nameEn,
                'level' => $level,
                'parent_slug' => $parentSlug,
                'parent_name' => $parentNameBn, // Pure Bengali Parent Name
                'icon' => $node['icon'] ?? 'folder-outline',
                'search_query' => $node['search_query'] ?? ($node['name_en'] ?? $slug),
                'children_count' => $childrenCount
            ];

            if ($hasChildren) {
                self::flattenSubtree($node['children'], $flattened, $slug, $nameBn, $level + 1);
            }
        }
    }

    /**
     * Find item details by slug or name (Bengali / English / Slug)
     */
    public static function findItem($slugOrName) {
        if (empty($slugOrName)) return null;
        $flat = self::getFlattenedList();
        $target = strtolower(trim($slugOrName));

        // Exact slug or name match
        foreach ($flat as $item) {
            if (strtolower($item['slug']) === $target || 
                strtolower($item['name_bn']) === $target || 
                strtolower($item['name']) === $target || 
                strtolower($item['name_en']) === $target) {
                return $item;
            }
        }

        // Partial match for convenience
        foreach ($flat as $item) {
            if (stripos($target, strtolower($item['slug'])) !== false ||
                stripos(strtolower($item['name_bn']), $target) !== false ||
                stripos(strtolower($item['name_en']), $target) !== false) {
                return $item;
            }
        }

        return null;
    }
}

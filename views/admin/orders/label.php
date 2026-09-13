<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Label #<?php echo $order['id']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39+Text&display=swap" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; margin: 0; }
            @page {
                size: 4in 6in; /* Standard shipping label size */
                margin: 0;
            }
        }
        .barcode {
            font-family: 'Libre Barcode 39 Text', cursive;
            font-size: 48px;
        }
    </style>
</head>
<body class="bg-gray-200 p-8 print:p-0 print:bg-white flex justify-center">

    <div class="w-[384px] h-[576px] bg-white border border-gray-300 p-6 flex flex-col justify-between print:border-none relative">
        <!-- Top Section -->
        <div>
            <div class="flex justify-between items-start border-b-2 border-black pb-4 mb-4">
                <div>
                   <h1 class="font-bold text-xl uppercase"><?php echo htmlspecialchars($settings['site_title'] ?? 'Sodai Dorkar'); ?></h1>
                   <p class="text-xs"><?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?></p>
                </div>
                <div class="text-right">
                    <div class="bg-black text-white px-2 py-1 font-bold text-sm">COD: ৳ <?php echo number_format($order['total_amount'], 0); ?></div>
                </div>
            </div>

            <!-- Customer -->
            <div class="mb-6">
                <span class="text-xs font-bold uppercase text-gray-500">Deliver To:</span>
                <h2 class="text-2xl font-bold leading-tight mt-1"><?php echo htmlspecialchars($order['customer_name']); ?></h2>
                <p class="text-lg font-mono font-bold mt-1"><?php echo htmlspecialchars($order['customer_phone']); ?></p>
                <p class="text-sm mt-2 border-l-4 border-gray-800 pl-2 leading-snug">
                    <?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?>
                </p>
                
                <?php if(!empty($order['area_name'])): ?>
                <div class="mt-3 inline-block border border-gray-400 px-2 py-1 bg-gray-50">
                    <span class="font-bold text-sm"><?php echo htmlspecialchars($order['area_name']); ?></span>
                    <?php if(!empty($order['zone_name'])): ?>
                        <span class="text-gray-500 mx-1">/</span>
                        <span class="text-sm"><?php echo htmlspecialchars($order['zone_name']); ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Middle: Barcode -->
        <div class="text-center py-4 border-t-2 border-dashed border-gray-300">
             <div class="barcode">*<?php echo $order['id']; ?>*</div>
             <p class="font-mono text-xs tracking-widest mt-1">ORD-<?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?></p>
        </div>

        <!-- Bottom: Order Details -->
        <div class="border-t-2 border-black pt-4">
            <div class="flex justify-between text-xs mb-2">
                <span class="font-bold">Order ID: #<?php echo $order['id']; ?></span>
                <span><?php echo date('d/m/Y h:i A', strtotime($order['created_at'])); ?></span>
            </div>
            <div class="text-xs text-gray-500 line-clamp-3">
                Items: 
                <?php 
                $items = $order['items'] ?? [];
                $names = array_map(function($i) { return $i['product_name'] . ' (' . $i['quantity'] . ')'; }, $items);
                echo implode(', ', $names);
                ?>
            </div>
        </div>
    </div>

    <!-- Print Button -->
    <div class="fixed bottom-8 right-8 no-print">
        <button onclick="window.print()" class="bg-black hover:bg-gray-800 text-white font-bold py-3 px-6 rounded-lg shadow-lg flex items-center">
             <ion-icon name="print-outline" class="mr-2 text-xl"></ion-icon>
             Print Label
        </button>
    </div>
    
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>

</body>
</html>

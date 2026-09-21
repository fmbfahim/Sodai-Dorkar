<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $order['id']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8 print:p-0 print:bg-white text-sm">

    <div class="max-w-2xl mx-auto bg-white p-8 shadow-sm print:shadow-none print:w-full">
        <!-- Header -->
        <div class="flex justify-between items-start mb-8 border-b pb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($settings['site_title'] ?? 'Fresh E mart'); ?></h1>
                <p class="text-gray-500 mt-1"><?php echo htmlspecialchars($settings['contact_address'] ?? 'Dhaka, Bangladesh'); ?></p>
                <p class="text-gray-500"><?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?></p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold text-gray-700">INVOICE</h2>
                <p class="text-gray-600 mt-1">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></p>
                <p class="text-gray-500 text-xs mt-1">Date: <?php echo date('d M, Y', strtotime($order['created_at'])); ?></p>
            </div>
        </div>

        <!-- Addresses -->
        <div class="flex justify-between mb-8">
            <div>
                <h3 class="font-bold text-gray-600 mb-2 text-xs uppercase tracking-wider">Bill To:</h3>
                <p class="font-bold text-gray-800"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                <p class="text-gray-600"><?php echo htmlspecialchars($order['customer_phone']); ?></p>
                <p class="text-gray-600 w-64 text-xs mt-1"><?php echo htmlspecialchars($order['delivery_address']); ?></p>
            </div>
            <div class="text-right">
                <div class="bg-gray-50 p-3 rounded">
                    <p class="text-xs text-gray-500">Total Amount</p>
                    <p class="text-xl font-bold text-primary-600">৳ <?php echo number_format($order['total_amount'], 2); ?></p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="w-full mb-8">
            <thead>
                <tr class="border-b-2 border-gray-200">
                    <th class="text-left py-2 text-gray-600 font-bold w-12">#</th>
                    <th class="text-left py-2 text-gray-600 font-bold">Item</th>
                    <th class="text-center py-2 text-gray-600 font-bold w-20">Qty</th>
                    <th class="text-right py-2 text-gray-600 font-bold w-24">Price</th>
                    <th class="text-right py-2 text-gray-600 font-bold w-24">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $items = $order['items'] ?? [];
                $subtotal = 0;
                foreach($items as $index => $item): 
                    $lineTotal = $item['price'] * $item['quantity'];
                    $subtotal += $lineTotal;
                ?>
                <tr class="border-b border-gray-100">
                    <td class="py-2 text-gray-500"><?php echo $index + 1; ?></td>
                    <td class="py-2 text-gray-800"><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td class="py-2 text-center text-gray-600"><?php echo $item['quantity']; ?></td>
                    <td class="py-2 text-right text-gray-600"><?php echo number_format($item['price'], 2); ?></td>
                    <td class="py-2 text-right text-gray-800 font-medium"><?php echo number_format($lineTotal, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="flex justify-end">
            <div class="w-48">
                <div class="flex justify-between py-1 text-gray-600">
                    <span>Subtotal:</span>
                    <span>৳ <?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="flex justify-between py-1 text-gray-600">
                    <span>Delivery Charge:</span>
                    <span>৳ <?php echo number_format($order['delivery_charge'] ?? 0, 2); ?></span>
                </div>
                <div class="flex justify-between py-2 border-t border-gray-200 mt-2 font-bold text-lg text-gray-800">
                    <span>Total:</span>
                    <span>৳ <?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-12 text-center text-xs text-gray-400">
            <p>Thank you for shopping with <?php echo htmlspecialchars($settings['site_title'] ?? 'Fresh E mart'); ?>!</p>
            <p class="mt-1">This is a system generated invoice.</p>
        </div>
    </div>

    <!-- Print Button -->
    <div class="fixed bottom-8 right-8 no-print">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-full shadow-lg flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print Invoice
        </button>
    </div>

</body>
</html>

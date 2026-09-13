<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Receipt #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace, system-ui, sans-serif;
            font-size: 12px;
            line-height: 1.35;
            color: #000;
            background: #f3f4f6;
            padding: 20px 0;
            -webkit-print-color-adjust: exact;
        }
        .receipt-container {
            width: 80mm;
            max-width: 80mm;
            margin: 0 auto;
            background: #fff;
            padding: 12px 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .double-line {
            border-top: 2px solid #000;
            margin: 6px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        th {
            border-bottom: 1px dashed #000;
            padding: 4px 0;
        }
        td {
            padding: 3px 0;
            vertical-align: top;
        }
        .item-row {
            margin-bottom: 2px;
        }
        .variant-pill {
            display: inline-block;
            font-size: 10px;
            border: 1px solid #666;
            padding: 0 3px;
            border-radius: 2px;
            font-weight: bold;
            margin-left: 2px;
        }
        .no-print-bar {
            width: 80mm;
            margin: 0 auto 12px auto;
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        .btn {
            padding: 6px 14px;
            font-family: sans-serif;
            font-size: 12px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .btn-print {
            background: #059669;
            color: #fff;
        }
        .btn-print:hover {
            background: #047857;
        }
        .btn-close {
            background: #6b7280;
            color: #fff;
        }
        .btn-close:hover {
            background: #4b5563;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
                width: 80mm;
            }
            .receipt-container {
                box-shadow: none;
                width: 80mm;
                max-width: 80mm;
                padding: 4mm 3mm;
            }
            .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- On-screen Action Toolbar -->
    <div class="no-print-bar">
        <button onclick="window.print()" class="btn btn-print">🖨️ Print Receipt</button>
        <button onclick="window.close()" class="btn btn-close">✕ Close</button>
    </div>

    <!-- 80mm POS Thermal Receipt -->
    <div class="receipt-container">
        <!-- Store Header -->
        <div class="text-center">
            <h1 style="font-size: 18px; font-weight: 900; letter-spacing: -0.5px;"><?= htmlspecialchars($settings['site_title'] ?? 'Sodai Dorkar') ?></h1>
            <p style="font-size: 11px; margin-top: 2px;"><?= htmlspecialchars($settings['contact_address'] ?? 'Chandpur, Bangladesh') ?></p>
            <p style="font-size: 11px;">Hotline: <?= htmlspecialchars($settings['contact_phone'] ?? '01700-000000') ?></p>
            <div class="dashed-line"></div>
            <p class="font-bold" style="font-size: 13px; letter-spacing: 1px;">*** POS RECEIPT ***</p>
            <div class="dashed-line"></div>
        </div>

        <!-- Order & Cashier Info -->
        <div style="font-size: 11px;">
            <div style="display: flex; justify-content: space-between;">
                <span>Order No:</span>
                <span class="font-bold">#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Date:</span>
                <span><?= date('d-M-Y h:i A', strtotime($order['created_at'])) ?></span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Payment:</span>
                <span class="font-bold"><?= strtoupper($order['payment_method'] ?? 'CASH') ?></span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Status:</span>
                <span class="font-bold"><?= strtoupper($order['status'] ?? 'DELIVERED') ?></span>
            </div>
        </div>

        <div class="dashed-line"></div>

        <!-- Customer Info -->
        <div style="font-size: 11px;">
            <div><span style="color: #444;">Customer:</span> <span class="font-bold"><?= htmlspecialchars($order['customer_name']) ?></span></div>
            <div><span style="color: #444;">Phone:</span> <span class="font-bold"><?= htmlspecialchars($order['customer_phone']) ?></span></div>
            <?php if (!empty($order['delivery_address'])): ?>
                <div><span style="color: #444;">Address:</span> <?= htmlspecialchars($order['delivery_address']) ?></div>
            <?php endif; ?>
        </div>

        <div class="dashed-line"></div>

        <!-- Line Items Table -->
        <table>
            <thead>
                <tr>
                    <th class="text-left" style="width: 50%;">Item</th>
                    <th class="text-center" style="width: 14%;">Qty</th>
                    <th class="text-right" style="width: 18%;">Price</th>
                    <th class="text-right" style="width: 18%;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $items = $order['items'] ?? [];
                $subtotal = 0;
                foreach ($items as $item): 
                    $lineTotal = $item['price'] * $item['quantity'];
                    $subtotal += $lineTotal;
                ?>
                <tr>
                    <td class="text-left">
                        <div class="font-bold"><?= htmlspecialchars($item['product_name']) ?></div>
                        <?php if (!empty($item['unit_title'])): ?>
                            <span class="variant-pill"><?= htmlspecialchars($item['unit_title']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center font-bold"><?= $item['quantity'] ?></td>
                    <td class="text-right"><?= number_format($item['price'], 0) ?></td>
                    <td class="text-right font-bold"><?= number_format($lineTotal, 0) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="dashed-line"></div>

        <!-- Financial Summary -->
        <div style="font-size: 11px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                <span>Subtotal:</span>
                <span>৳ <?= number_format($subtotal, 2) ?></span>
            </div>

            <?php 
                $deliveryFee = floatval($order['delivery_charge'] ?? 0);
                $grandTotal = floatval($order['total_amount'] ?? 0);
                $discount = floatval($order['delivery_discount'] ?? 0);
                if ($discount <= 0) {
                    $discount = max(0, ($subtotal + $deliveryFee) - $grandTotal);
                }
            ?>

            <?php if ($discount > 0): ?>
            <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                <span>Discount:</span>
                <span>- ৳ <?= number_format($discount, 2) ?></span>
            </div>
            <?php endif; ?>

            <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                <span>Delivery Fee:</span>
                <span>৳ <?= number_format($deliveryFee, 2) ?></span>
            </div>

            <div class="double-line"></div>

            <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: 900; margin: 4px 0;">
                <span>NET TOTAL:</span>
                <span>৳ <?= number_format($grandTotal, 2) ?></span>
            </div>

            <div class="double-line"></div>

            <div style="display: flex; justify-content: space-between;">
                <span>Paid (<?= strtoupper($order['payment_method'] ?? 'CASH') ?>):</span>
                <span class="font-bold">৳ <?= number_format($grandTotal, 2) ?></span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Change / Due:</span>
                <span>৳ 0.00</span>
            </div>
        </div>

        <?php if (!empty($order['rider_note']) || !empty($order['admin_note'])): ?>
        <div style="font-size: 9px; margin-top: 4px; padding: 4px; background: #f3f4f6; border-radius: 4px;">
            <?php if (!empty($order['admin_note'])): ?>
                <div><strong>Note:</strong> <?= htmlspecialchars($order['admin_note']) ?></div>
            <?php endif; ?>
            <?php if (!empty($order['rider_note'])): ?>
                <div><strong>Rider Note:</strong> <?= htmlspecialchars($order['rider_note']) ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="dashed-line"></div>

        <!-- Receipt Footer & Barcode -->
        <div class="text-center" style="margin-top: 8px; font-size: 10px;">
            <p class="font-bold" style="font-size: 14px; letter-spacing: 2px; font-family: monospace;">*ORD-<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>*</p>
            <p style="margin-top: 4px;">Thank you for shopping with us!</p>
            <p style="color: #666; font-size: 9px; margin-top: 2px;">Items once sold can be returned within 24h with receipt.</p>
            <p style="color: #888; font-size: 8px; margin-top: 4px;">Powered by Sodai Dorkar Smart POS</p>
        </div>
    </div>

    <script>
        // Auto trigger print when opened
        window.addEventListener('load', () => {
            setTimeout(() => {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>

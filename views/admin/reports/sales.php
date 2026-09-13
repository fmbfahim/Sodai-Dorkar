<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="text-2xl font-bold text-secondary-800">Sales Report</h2>
            <p class="text-sm text-secondary-500">Overview of sales performance.</p>
        </div>
        
        <form class="flex gap-2 items-end bg-white p-2 rounded-lg border border-secondary-200 shadow-sm">
            <div>
                <label class="block text-xs text-secondary-500 mb-1">From</label>
                <input type="date" name="start" value="<?php echo $start; ?>" class="text-sm border border-secondary-300 rounded px-2 py-1">
            </div>
            <div>
                <label class="block text-xs text-secondary-500 mb-1">To</label>
                <input type="date" name="end" value="<?php echo $end; ?>" class="text-sm border border-secondary-300 rounded px-2 py-1">
            </div>
            <button type="submit" class="bg-primary-600 text-white px-4 py-1.5 rounded text-sm font-bold hover:bg-primary-700">Filter</button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <?php 
            $totalSales = array_sum(array_column($sales, 'total')); 
            $totalOrders = array_sum(array_column($sales, 'count'));
            $avgOrder = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        ?>
        <div class="bg-white p-6 rounded-xl border border-secondary-100 shadow-sm">
            <p class="text-secondary-500 text-sm">Total Revenue</p>
            <h3 class="text-3xl font-bold text-primary-600 mt-1">৳ <?php echo number_format($totalSales); ?></h3>
        </div>
        <div class="bg-white p-6 rounded-xl border border-secondary-100 shadow-sm">
            <p class="text-secondary-500 text-sm">Total Orders</p>
            <h3 class="text-3xl font-bold text-secondary-800 mt-1"><?php echo number_format($totalOrders); ?></h3>
        </div>
        <div class="bg-white p-6 rounded-xl border border-secondary-100 shadow-sm">
            <p class="text-secondary-500 text-sm">Average Order Value</p>
            <h3 class="text-3xl font-bold text-blue-600 mt-1">৳ <?php echo number_format($avgOrder, 0); ?></h3>
        </div>
    </div>

    <!-- Chart (Placeholder as we need Chart.js but simple table first) -->
    
    <div class="bg-white rounded-xl shadow-sm border border-secondary-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-secondary-200 bg-secondary-50">
            <h3 class="font-bold text-secondary-700">Daily Breakdown</h3>
        </div>
        <table class="w-full text-left border-collapse">
            <thead class="bg-secondary-50 text-secondary-600 text-xs uppercase font-semibold">
                <tr>
                    <th class="p-4">Date</th>
                    <th class="p-4 text-center">Orders</th>
                    <th class="p-4 text-right">Revenue</th>
                    <th class="p-4 text-right">Avg. Value</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-secondary-100 text-sm">
                <?php if(empty($sales)): ?>
                <tr>
                    <td colspan="4" class="p-8 text-center text-secondary-400 italic">No sales found in this range.</td>
                </tr>
                <?php else: ?>
                    <?php foreach($sales as $day): ?>
                    <tr class="hover:bg-secondary-50 transition-colors">
                        <td class="p-4 font-medium text-secondary-800"><?php echo date('d M, Y', strtotime($day['date'])); ?></td>
                        <td class="p-4 text-center">
                            <span class="inline-block bg-blue-50 text-blue-700 px-2 py-1 rounded font-bold text-xs">
                                <?php echo $day['count']; ?>
                            </span>
                        </td>
                        <td class="p-4 text-right font-bold text-secondary-800">৳ <?php echo number_format($day['total']); ?></td>
                        <td class="p-4 text-right font-mono text-secondary-500">৳ <?php echo number_format($day['total'] / $day['count'], 0); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

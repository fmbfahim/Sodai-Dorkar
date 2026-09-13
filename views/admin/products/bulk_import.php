<div class="max-w-xl mx-auto mt-10">
    <div class="bg-white rounded-xl shadow-sm border border-secondary-100 p-8">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <ion-icon name="cloud-upload-outline" class="text-3xl text-primary-600"></ion-icon>
            </div>
            <h2 class="text-2xl font-bold text-secondary-900">Bulk Product Import</h2>
            <p class="text-secondary-500 mt-2">Upload a CSV file to import products in bulk.</p>
        </div>

        <form action="/sodai-dorkar/public/admin/products/bulk-preview" method="POST" enctype="multipart/form-data" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <div class="relative border-2 border-dashed border-secondary-300 rounded-xl p-8 text-center hover:border-primary-500 hover:bg-primary-50 transition-colors cursor-pointer group">
                <input type="file" name="csv_file" accept=".csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required>
                <div class="space-y-2">
                    <ion-icon name="document-text-outline" class="text-4xl text-secondary-400 group-hover:text-primary-500 transition-colors"></ion-icon>
                    <p class="text-sm font-medium text-secondary-700 group-hover:text-primary-700">Click to upload or drag and drop</p>
                    <p class="text-xs text-secondary-400">CSV files only (Max 5MB)</p>
                </div>
            </div>

            <div class="bg-blue-50 text-blue-800 text-sm p-4 rounded-lg flex items-start">
                <ion-icon name="information-circle-outline" class="text-lg mr-2 flex-shrink-0 mt-0.5"></ion-icon>
                <div>
                    <strong>CSV ফরম্যাট (কলামের ক্রম):</strong><br>
                    1. Name, 2. SKU, 3. Description, 4. Buy Price, 5. Regular Price, 6. Sell Price, 7. Stock Qty, <br>
                    <strong>8. Category Path</strong> (যেমন: "Food > Rice > Miniket"), <br>
                    <strong>9. Brand Name</strong> (যেমন: "Teer"), <br>
                    <strong>10. Vendor Name</strong> (যেমন: "Rahim Traders"), <br>
                    <strong>11. Unit Type</strong> (sack_kg, drum_liter, box_piece, weight, piece), <br>
                    <strong>12. Base Unit</strong> (kg, liter, pcs, gm, ml), <br>
                    <strong>13. Purchase Unit</strong> (বস্তা, ড্রাম, বক্স), <br>
                    <strong>14. Purchase Qty</strong> (৫০, ১৯০, ২৪), <br>
                    <strong>15. Selling Unit</strong> (kg, pcs)
                </div>
            </div>

            <div class="flex justify-between items-center">
                <a href="/sodai-dorkar/public/admin/products/bulk-demo" class="px-4 py-2 text-sm bg-green-50 text-green-700 border border-green-200 rounded-lg hover:bg-green-100 font-semibold transition-colors flex items-center">
                    <ion-icon name="download-outline" class="mr-2 text-lg"></ion-icon>
                    Download Demo CSV
                </a>
                <div class="flex justify-end gap-3">
                    <a href="/sodai-dorkar/public/admin/products" class="px-6 py-2.5 border border-secondary-300 rounded-lg text-secondary-600 font-bold hover:bg-secondary-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg transition-colors shadow-sm w-full sm:w-auto">
                        Preview & Edit
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

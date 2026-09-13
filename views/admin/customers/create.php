<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-secondary-100 p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-secondary-800">Registration Form</h3>
            <a href="/sodai-dorkar/public/admin/customers" class="text-secondary-500 hover:text-secondary-700 text-sm flex items-center">
                <ion-icon name="arrow-back-outline" class="mr-1"></ion-icon> Back to List
            </a>
        </div>

        <form action="/sodai-dorkar/public/admin/customers/store" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <!-- Include Leaflet CSS for Map -->
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

            <!-- Basic Info -->
            <div class="mb-8">
                <h4 class="text-sm uppercase tracking-wide text-secondary-500 font-bold mb-4 border-b border-secondary-100 pb-2">Basic Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-secondary-600 text-sm font-medium mb-2" for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required placeholder="Customer Name">
                    </div>
                    <div>
                        <label class="block text-secondary-600 text-sm font-medium mb-2" for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required placeholder="01XXX-XXXXXX">
                    </div>
                    <div>
                        <label class="block text-secondary-600 text-sm font-medium mb-2" for="area_id">Area / Location</label>
                        <div class="relative">
                            <select id="area_id" name="area_id" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 appearance-none bg-white" required>
                                <option value="" disabled selected>Select Area</option>
                                <?php foreach ($areas as $area): ?>
                                    <option value="<?php echo $area['id']; ?>"><?php echo htmlspecialchars($area['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-secondary-700">
                                <ion-icon name="chevron-down-outline"></ion-icon>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-secondary-600 text-sm font-medium mb-2" for="address_details">House/Street Address</label>
                        <input type="text" id="address_details" name="address_details" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required placeholder="House #, Road #">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-secondary-600 text-sm font-medium mb-2">Pin Location on Map (Optional)</label>
                        <div id="customerMap" class="w-full h-64 rounded-lg border border-secondary-300 relative z-0"></div>
                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">
                        <p class="text-xs text-secondary-500 mt-1">Click on the map or drag the marker to pinpoint the exact location.</p>
                    </div>
                </div>
            </div>

            <!-- Demographics -->
            <div class="mb-8">
                <h4 class="text-sm uppercase tracking-wide text-secondary-500 font-bold mb-4 border-b border-secondary-100 pb-2">Household Profile</h4>
                
                <!-- Members Breakdown -->
                <div class="mb-4">
                    <label class="block text-xs font-bold text-secondary-400 uppercase tracking-wider mb-2">Members Breakdown</label>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                         <div>
                            <label class="block text-secondary-600 text-xs font-medium mb-1">Total Adults</label>
                            <input type="number" name="adult_count" class="w-full px-2 py-2 border border-secondary-300 rounded text-center focus:ring-1 focus:ring-primary-500" value="0" min="0">
                        </div>
                        <div>
                            <label class="block text-secondary-600 text-xs font-medium mb-1">Adult Males</label>
                            <input type="number" name="member_male" class="w-full px-2 py-2 border border-secondary-300 rounded text-center focus:ring-1 focus:ring-primary-500" value="0" min="0">
                        </div>
                         <div>
                            <label class="block text-secondary-600 text-xs font-medium mb-1">Adult Females</label>
                            <input type="number" name="member_female" class="w-full px-2 py-2 border border-secondary-300 rounded text-center focus:ring-1 focus:ring-primary-500" value="0" min="0">
                        </div>
                        <div>
                            <label class="block text-secondary-600 text-xs font-medium mb-1">Children</label>
                            <input type="number" name="kids_count" class="w-full px-2 py-2 border border-secondary-300 rounded text-center focus:ring-1 focus:ring-primary-500" value="0" min="0">
                        </div>
                        <div>
                            <label class="block text-secondary-600 text-xs font-medium mb-1">Elderly</label>
                            <input type="number" name="elderly_count" class="w-full px-2 py-2 border border-secondary-300 rounded text-center focus:ring-1 focus:ring-primary-500" value="0" min="0">
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                 <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                     <div>
                        <label class="block text-secondary-600 text-sm font-medium mb-2">Occupation</label>
                        <select name="occupation" class="w-full px-3 py-2 border border-secondary-300 rounded-lg bg-white">
                            <option value="">Select Occupation</option>
                            <option value="business">Business</option>
                            <option value="service" selected>Service/Job</option>
                            <option value="govt_service">Govt. Service</option>
                            <option value="farmer">Farmer/Agriculture</option>
                            <option value="housewife">Homemaker</option>
                            <option value="student">Student</option>
                            <option value="expat_family">Expat Family</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-secondary-600 text-sm font-medium mb-2">Housing Type</label>
                        <select name="house_type" class="w-full px-3 py-2 border border-secondary-300 rounded-lg bg-white">
                            <option value="">Select Housing</option>
                            <option value="building">Building (Pucca)</option>
                            <option value="semi_pucca">Semi-Pucca</option>
                            <option value="tin_shed">Tin Shed</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="flex gap-4">
                         <div class="flex-1">
                            <label class="block text-secondary-600 text-xs font-medium mb-2">Expatriates</label>
                            <input type="number" name="exhpat_count" class="w-full px-3 py-2 border border-secondary-300 rounded-lg" value="0" min="0">
                        </div>
                        <div class="flex-1">
                            <label class="block text-secondary-600 text-xs font-medium mb-2">Pets</label>
                            <input type="number" name="pet_count" class="w-full px-3 py-2 border border-secondary-300 rounded-lg" value="0" min="0">
                        </div>
                    </div>
                </div>
            </div>

             <!-- Notes -->
             <div class="mb-8">
                <label class="block text-secondary-600 text-sm font-medium mb-2" for="notes">Additional Notes</label>
                <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Any special requirements or dietary habits..."></textarea>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-8 rounded-lg transition-colors shadow-lg shadow-primary-200">
                    Register Customer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Include Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Default location (e.g., Dhaka)
        var defaultLat = 23.8103;
        var defaultLng = 90.4125;
        
        var map = L.map('customerMap').setView([defaultLat, defaultLng], 12);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);

        function updateLocation(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        }

        marker.on('dragend', function(event) {
            var position = marker.getLatLng();
            updateLocation(position.lat, position.lng);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateLocation(e.latlng.lat, e.latlng.lng);
        });
    });
</script>

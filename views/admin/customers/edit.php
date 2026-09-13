<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-secondary-800">Edit Customer</h2>
        <a href="/sodai-dorkar/public/admin/customers" class="text-secondary-600 hover:text-primary-600 flex items-center transition-colors">
            <ion-icon name="arrow-back-outline" class="mr-2 text-xl"></ion-icon>
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-secondary-100 p-8">
        <form action="/sodai-dorkar/public/admin/customers/update" method="POST">
    <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::token() ?>">
            <!-- Include Leaflet CSS for Map -->
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

            <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">

            <!-- Identity & Location -->
            <div class="mb-8 border-b border-secondary-100 pb-6">
                <h3 class="text-lg font-bold text-secondary-800 mb-4 flex items-center">
                    <ion-icon name="person-outline" class="mr-2 text-primary-600"></ion-icon>
                    Identity & Location
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-secondary-600 text-sm font-medium mb-2" for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                    </div>
                    <div>
                        <label class="block text-secondary-600 text-sm font-medium mb-2" for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                    </div>
                    <div>
                        <label class="block text-secondary-600 text-sm font-medium mb-2" for="area">Union (Area)</label>
                        <div class="relative">
                            <select id="area" name="area_id" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 appearance-none bg-white" required onchange="filterZones()">
                                <option value="">Select Union</option>
                                <?php foreach ($areas as $area): 
                                    $selected = ($area['id'] == $customer['area_id']) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $area['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($area['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-secondary-500">
                                 <ion-icon name="chevron-down-outline"></ion-icon>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-secondary-600 text-sm font-medium mb-2" for="zone">Zone (Ward)</label>
                        <div class="relative">
                            <select id="zone" name="zone_id" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 appearance-none bg-white" onchange="filterPoints()">
                                <option value="">Select Zone</option>
                                <?php foreach ($zones as $zone): 
                                    $selected = ($zone['id'] == $customer['zone_id']) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $zone['id']; ?>" data-area="<?php echo $zone['area_id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($zone['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-secondary-500">
                                 <ion-icon name="chevron-down-outline"></ion-icon>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-secondary-600 text-sm font-medium mb-2" for="point">Point (Village/Road)</label>
                        <div class="relative">
                            <select id="point" name="point_id" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 appearance-none bg-white">
                                <option value="">Select Point</option>
                                <?php foreach ($points as $point): 
                                    $selected = ($point['id'] == $customer['point_id']) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $point['id']; ?>" data-zone="<?php echo $point['zone_id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($point['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-secondary-500">
                                 <ion-icon name="chevron-down-outline"></ion-icon>
                            </div>
                        </div>
                    </div>

<script>
    function filterZones() {
        const areaId = document.getElementById('area').value;
        const zoneSelect = document.getElementById('zone');
        const pointSelect = document.getElementById('point');
        
        // Hide/Show zones based on area
        let hasVisible = false;
        Array.from(zoneSelect.options).forEach(option => {
            if (option.value === "") return;
            const parent = option.getAttribute('data-area');
            if (parent === areaId) {
                option.style.display = "";
                hasVisible = true;
            } else {
                option.style.display = "none";
                if(option.selected) {
                    zoneSelect.value = ""; // Deselect hidden
                }
            }
        });
        
        filterPoints(); // Chain update
    }

    function filterPoints() {
        const zoneId = document.getElementById('zone').value;
        const pointSelect = document.getElementById('point');
        
        Array.from(pointSelect.options).forEach(option => {
            if (option.value === "") return;
            const parent = option.getAttribute('data-zone');
            if (parent === zoneId) {
                option.style.display = "";
            } else {
                option.style.display = "none";
                 if(option.selected) {
                    pointSelect.value = ""; // Deselect hidden
                }
            }
        });
    }
    
    // Initial Filter
    window.addEventListener('DOMContentLoaded', () => {
         // Don't clear values on load, just hide invalid ones (though logic mainly handles changes)
         // Actually, on load, we just want to hide unrelated ones but KEEP selected ones visible.
         // Current logic hides unrelated ones.
         filterZones();
    });
</script>
                     <div class="md:col-span-2">
                        <label class="block text-secondary-600 text-sm font-medium mb-2" for="address">Address Details</label>
                        <textarea id="address" name="address_details" rows="2" class="w-full px-4 py-3 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Village, House No, Landmark..."><?php echo htmlspecialchars($customer['address_details']); ?></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-secondary-600 text-sm font-medium mb-2">Pin Location on Map (Optional)</label>
                        <div id="customerMap" class="w-full h-64 rounded-lg border border-secondary-300 relative z-0"></div>
                        <input type="hidden" id="latitude" name="latitude" value="<?php echo htmlspecialchars($customer['latitude'] ?? ''); ?>">
                        <input type="hidden" id="longitude" name="longitude" value="<?php echo htmlspecialchars($customer['longitude'] ?? ''); ?>">
                        <p class="text-xs text-secondary-500 mt-1">Click on the map or drag the marker to pinpoint the exact location.</p>
                    </div>
                </div>
            </div>

            <!-- Demographics (JSON) -->
            <?php $demos = json_decode($customer['demographics_json'], true) ?? []; ?>
            <div class="mb-8">
                <h3 class="text-lg font-bold text-secondary-800 mb-4 flex items-center">
                    <ion-icon name="people-outline" class="mr-2 text-primary-600"></ion-icon>
                    Family Profile
                </h3>

                 <!-- Members Breakdown -->
                <div class="bg-secondary-50 p-4 rounded-lg mb-4">
                    <label class="block text-xs font-bold text-secondary-500 uppercase tracking-wider mb-3">Members Breakdown</label>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                         <div>
                            <label class="block text-secondary-600 text-xs font-medium mb-1">Total Adults</label>
                            <input type="number" name="adult_count" value="<?php echo $demos['adult_count'] ?? 0; ?>" class="w-full px-2 py-2 border border-secondary-300 rounded text-center" min="0">
                        </div>
                        <div>
                            <label class="block text-secondary-600 text-xs font-medium mb-1">Adult Males</label>
                            <input type="number" name="member_male" value="<?php echo $demos['member_male'] ?? 0; ?>" class="w-full px-2 py-2 border border-secondary-300 rounded text-center" min="0">
                        </div>
                         <div>
                            <label class="block text-secondary-600 text-xs font-medium mb-1">Adult Females</label>
                            <input type="number" name="member_female" value="<?php echo $demos['member_female'] ?? 0; ?>" class="w-full px-2 py-2 border border-secondary-300 rounded text-center" min="0">
                        </div>
                        <div>
                            <label class="block text-secondary-600 text-xs font-medium mb-1">Children</label>
                            <input type="number" name="kids_count" value="<?php echo $demos['kids_count'] ?? 0; ?>" class="w-full px-2 py-2 border border-secondary-300 rounded text-center" min="0">
                        </div>
                        <div>
                            <label class="block text-secondary-600 text-xs font-medium mb-1">Elderly</label>
                            <input type="number" name="elderly_count" value="<?php echo $demos['elderly_count'] ?? 0; ?>" class="w-full px-2 py-2 border border-secondary-300 rounded text-center" min="0">
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                 <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                     <div>
                        <label class="block text-secondary-600 text-sm font-medium mb-2">Occupation</label>
                        <select name="occupation" class="w-full px-3 py-2 border border-secondary-300 rounded-lg bg-white">
                            <?php 
                                $opts = [
                                    '' => 'Select Occupation', 'business' => 'Business', 'service' => 'Service/Job', 
                                    'govt_service' => 'Govt. Service', 'farmer' => 'Farmer/Agriculture', 
                                    'housewife' => 'Homemaker', 'student' => 'Student', 
                                    'expat_family' => 'Expat Family', 'other' => 'Other'
                                ];
                                $currentOcc = $demos['occupation'] ?? '';
                                foreach($opts as $val => $label) {
                                    $sel = ($currentOcc == $val) ? 'selected' : '';
                                    echo "<option value='$val' $sel>$label</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-secondary-600 text-sm font-medium mb-2">Housing Type</label>
                        <select name="house_type" class="w-full px-3 py-2 border border-secondary-300 rounded-lg bg-white">
                            <?php 
                                $opts = [
                                    '' => 'Select Housing', 'building' => 'Building (Pucca)', 
                                    'semi_pucca' => 'Semi-Pucca', 'tin_shed' => 'Tin Shed', 'other' => 'Other'
                                ];
                                $currentHome = $demos['house_type'] ?? '';
                                foreach($opts as $val => $label) {
                                    $sel = ($currentHome == $val) ? 'selected' : '';
                                    echo "<option value='$val' $sel>$label</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="flex gap-4">
                         <div class="flex-1">
                            <label class="block text-secondary-600 text-xs font-medium mb-2">Expatriates</label>
                            <input type="number" name="exhpat_count" value="<?php echo $demos['exhpat_count'] ?? 0; ?>" class="w-full px-3 py-2 border border-secondary-300 rounded-lg" min="0">
                        </div>
                        <div class="flex-1">
                            <label class="block text-secondary-600 text-xs font-medium mb-2">Pets</label>
                            <input type="number" name="pet_count" value="<?php echo $demos['pet_count'] ?? 0; ?>" class="w-full px-3 py-2 border border-secondary-300 rounded-lg" min="0">
                        </div>
                    </div>


                    <div class="col-span-1 md:col-span-3 mt-2">
                        <label class="block text-secondary-600 text-sm font-medium mb-1">Additional Notes</label>
                        <input type="text" name="notes" value="<?php echo htmlspecialchars($demos['notes'] ?? ''); ?>" class="w-full px-4 py-2 border border-secondary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Any special requirements...">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4">
                <a href="/sodai-dorkar/public/admin/customers" class="px-6 py-2.5 rounded-lg border border-secondary-300 text-secondary-600 hover:bg-secondary-50 font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-6 rounded-lg transition-colors shadow-lg shadow-primary-200">
                    Update Customer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Include Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var existingLat = <?php echo $customer['latitude'] ? $customer['latitude'] : 'null'; ?>;
        var existingLng = <?php echo $customer['longitude'] ? $customer['longitude'] : 'null'; ?>;
        
        var defaultLat = existingLat || 23.8103;
        var defaultLng = existingLng || 90.4125;
        
        var map = L.map('customerMap').setView([defaultLat, defaultLng], existingLat ? 16 : 12);
        
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

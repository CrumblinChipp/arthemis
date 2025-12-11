    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Campus Map Viewer</h1>
        
        {{-- The map container --}}
        <div id="viewMap" style="height: 600px; width: 100%;"></div>

    </div>


    {{-- Make sure you have the Leaflet CSS and JS links in your layout or here --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/SVSLqlAqHDgQO_C68="
        crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20n636lF2gJg+pL894O7k2xQ5K5G6/pXF5V/jH+fK5+"
        crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Initialize the map
            // Use your campus center coordinates here. Example:
            var initial map_x_percent = 14.75; 
            var initial map_y_percent = 121.01;
            var mapZoom = 15;

            var map = L.map('viewMap').setView([initial map_x_percent, initial map_y_percent], mapZoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // 2. Get the building data passed from the controller
            // The JSON encode ensures the data is safely passed to JavaScript
            var buildings = @json($buildings);

            // 3. Loop through the buildings and add markers
            buildings.forEach(function(building) {
                if (building. map_x_percent && building. map_y_percent) {
                    var marker = L.marker([building. map_x_percent, building. map_y_percent]).addTo(map);

                    // Optional: Make the marker title the building name for screen readers
                    marker.options.title = building.name;

                    // Implement the HOVER effect (Tooltip)
                    // Tooltips open on mouseover and close on mouseout by default
                    marker.bindTooltip(building.name, {
                        permanent: false, // Don't show permanently
                        direction: 'top', // Position the tooltip above the marker
                        offset: [0, -10] // Adjust position if needed
                    });
                    
                    // Optional: Fly to the first marker on load
                    // map.flyTo([building. map_x_percent, building. map_y_percent], mapZoom); 
                }
            });
            
            // Optional: If you want to fit the map view to show ALL markers
            if (buildings.length > 0) {
                 var group = new L.featureGroup(
                    buildings.map(b => L.marker([b. map_x_percent, b. map_y_percent]))
                 );
                 map.fitBounds(group.getBounds().pad(0.1)); // Pad adds a little margin
            }


        });
    </script>

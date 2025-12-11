<div class="p-4 max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold my-6 text-gray-800">Campus Map: {{ $campus->name ?? 'Select Campus' }}</h2>

    <div id="map-container" class="relative bg-gray-100 border border-gray-300 shadow-xl rounded-lg overflow-hidden">
        
        @if($campus && $campus->map_image_path)
            <img 
                id="campus-map" 
                src="{{ asset($campus->map_image_path) }}" 
                alt="{{ $campus->name }} Map" 
                class="w-full h-auto cursor-crosshair"
            >
        @else
            <div class="h-96 flex items-center justify-center text-gray-500">
                No map image uploaded for this campus.
            </div>
        @endif


        <div id="marker-layer" class="absolute inset-0 pointer-events-none">
            
            @foreach($buildings as $building)
                @if(isset($building->map_x_percent) && isset($building->map_y_percent))
                <div 
                    class="
                        absolute w-4 h-4 rounded-full shadow-lg border-2 border-white bg-red-600
                        transition-all duration-200 cursor-pointer group pointer-events-auto
                    "
                    style="
                        left: {{ $building->map_x_percent }}%;
                        top: {{ $building->map_y_percent }}%;
                        transform: translate(-50%, -50%); /* Centering the marker */
                    "
                    data-building-id="{{ $building->id }}"
                    data-building-name="{{ $building->name }}"
                >
                    <span 
                        class="
                            absolute whitespace-nowrap -bottom-6 left-1/2 transform -translate-x-1/2 
                            text-xs font-semibold bg-gray-800 text-white p-1 rounded-md opacity-0 
                            group-hover:opacity-100 transition-opacity duration-300
                        "
                    >
                        {{ $building->name }}
                    </span>
                </div>
                @endif
            @endforeach
        </div>

    </div>
    
    </div>  

<script>
  document.addEventListener('DOMContentLoaded', () => {
      const campusMap = document.getElementById('campus-map');
      const markerLayer = document.getElementById('marker-layer');
      const markerForm = document.getElementById('marker-form');
      const buildingNameInput = document.getElementById('building-name');
      const markerXInput = document.getElementById('marker-x');
      const markerYInput = document.getElementById('marker-y');
      const saveButton = markerForm.querySelector('button[type="submit"]');

      let currentMarkerElement = null; // Element for the currently placing marker

      // 1. Handle Map Click for Marker Placement
      campusMap.addEventListener('click', (event) => {
          // Clear any previous temporary marker
          if (currentMarkerElement) {
              currentMarkerElement.remove();
          }

          // Get coordinates relative to the *image* (important!)
          // event.offsetX and event.offsetY give the coordinates relative to the image element.
          const x = event.offsetX;
          const y = event.offsetY;
          
          // Calculate percentage for responsiveness (100% is the image width/height)
          const imageWidth = campusMap.offsetWidth;
          const imageHeight = campusMap.offsetHeight;
          
          const xPercentage = (x / imageWidth) * 100;
          const yPercentage = (y / imageHeight) * 100;

          // Store the percentage coordinates in hidden fields
          markerXInput.value = xPercentage.toFixed(2);
          markerYInput.value = yPercentage.toFixed(2);
          
          // Enable and focus the form
          saveButton.disabled = false;
          buildingNameInput.focus();

          // 2. Display a temporary marker on the map
          currentMarkerElement = createMarkerElement(xPercentage, yPercentage, 'Placing Marker...', true);
          markerLayer.appendChild(currentMarkerElement);
      });

      // Helper function to create the marker HTML element
      function createMarkerElement(xPercent, yPercent, name, isTemporary = false) {
          const marker = document.createElement('div');
          
          // Use Tailwind for styling: a red dot with a shadow.
          // The transform is key to center the marker *on* the coordinate.
          marker.className = `
              absolute w-4 h-4 rounded-full shadow-lg border-2 
              transition-all duration-200 cursor-pointer 
              ${isTemporary ? 'bg-yellow-400 border-yellow-700 animate-pulse' : 'bg-red-600 border-white hover:bg-red-800'}
          `;
          
          marker.style.left = `${xPercent}%`;
          marker.style.top = `${yPercent}%`;
          // Center the marker dot on the precise coordinates
          marker.style.transform = 'translate(-50%, -50%)';
          
          // Add a visible label for the admin
          if (!isTemporary) {
              const label = document.createElement('span');
              label.textContent = name;
              // Tailwind classes for the label
              label.className = `
                  absolute whitespace-nowrap -bottom-6 left-1/2 transform -translate-x-1/2 
                  text-xs font-semibold bg-gray-800 text-white p-1 rounded-md opacity-0 
                  group-hover:opacity-100 transition-opacity duration-300
              `;
              marker.appendChild(label);
              marker.classList.add('group'); // Enable hover effects
          }

          return marker;
      }

      // 3. Handle Form Submission (Save to Backend)
      markerForm.addEventListener('submit', async (event) => {
          event.preventDefault();

          const buildingName = buildingNameInput.value.trim();
          const x_coord = markerXInput.value;
          const y_coord = markerYInput.value;

          if (!buildingName || !x_coord || !y_coord) {
              alert('Please click on the map and provide a building name.');
              return;
          }

          const markerData = {
              name: buildingName,
              x_percent: parseFloat(x_coord), // Store as percentage
              y_percent: parseFloat(y_coord), // Store as percentage
              campus_id: 'your_current_campus_id', // Replace with dynamic ID
          };

          // --- Step 4: Send Data to Your Backend API ---
          try {
              // This part is conceptual. Replace with your actual AJAX/fetch call.
              console.log('Sending data to backend:', markerData);
              
              // Example of a successful save:
              // const response = await fetch('/api/markers', {
              //     method: 'POST',
              //     headers: { 'Content-Type': 'application/json' },
              //     body: JSON.stringify(markerData),
              // });
              // const savedMarker = await response.json();

              // Simulate successful saving
              const savedMarker = {...markerData, id: Date.now()}; 

              // Clear the temporary marker and render the permanent one
              if (currentMarkerElement) {
                  currentMarkerElement.remove();
                  currentMarkerElement = null;
              }
              const permanentMarker = createMarkerElement(savedMarker.x_percent, savedMarker.y_percent, savedMarker.name, false);
              markerLayer.appendChild(permanentMarker);

              // Reset the form
              markerForm.reset();
              saveButton.disabled = true;
              alert(`Marker "${buildingName}" saved successfully!`);

          } catch (error) {
              console.error('Error saving marker:', error);
              alert('Failed to save marker. Check console for details.');
          }
      });

      // 5. Load Existing Markers (on page load)
      async function loadExistingMarkers() {
          // --- Step 5: Fetch existing markers from your Backend API ---
          try {
              // This is conceptual. Replace with your actual AJAX/fetch call.
              // const response = await fetch('/api/markers?campus_id=...');
              // const existingMarkers = await response.json();

              // Simulate loading existing data (you'd get this from your database)
              const existingMarkers = [
                  { id: 101, name: 'Admissions Building', x_percent: 25.5, y_percent: 15.0 },
                  { id: 102, name: 'Science Lab', x_percent: 70.2, y_percent: 45.8 },
                  // ... more markers
              ];

              existingMarkers.forEach(marker => {
                  const markerElement = createMarkerElement(marker.x_percent, marker.y_percent, marker.name, false);
                  markerLayer.appendChild(markerElement);
              });
              
          } catch (error) {
              console.error('Error loading existing markers:', error);
          }
      }

      loadExistingMarkers();
  });
</script>
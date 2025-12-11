<div class="p-4 max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold my-6 text-gray-800">Campus Map: {{ $campus->name ?? 'Select Campus' }}</h2>

    <div class="flex flex-col lg:flex-row gap-8">
        
        <div id="map-container" class="relative bg-gray-100 border border-gray-300 shadow-xl rounded-lg overflow-hidden lg:w-3/4">
            
            @if($campus && $campus->map)
                <img 
                    id="campus-map" 
                    src="{{ asset('storage/' . $campus->map) }}" 
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
                        class="absolute w-4 h-4 rounded-full shadow-lg border-2 border-white bg-red-600 transition-all duration-200 cursor-pointer group permanent-marker pointer-events-auto"
                        style="
                            left: {{ $building->map_x_percent }}%;
                            top: {{ $building->map_y_percent }}%;
                            transform: translate(-50%, -50%);
                        "
                        data-building-id="{{ $building->id }}"
                        data-building-name="{{ $building->name }}"
                        data-x-percent="{{ $building->map_x_percent }}"
                        data-y-percent="{{ $building->map_y_percent }}"
                    >
                        <span 
                            class="absolute whitespace-nowrap -bottom-6 left-1/2 transform -translate-x-1/2 text-xs font-semibold bg-gray-800 text-white p-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        >
                            {{ $building->name }}
                        </span>
                    </div>
                    @endif
                @endforeach
            </div>
            
        </div> <div id="config-panel" class="lg:w-1/4 p-6 bg-white shadow-xl rounded-lg border border-gray-200">
            <h3 id="form-title" class="text-xl font-semibold mb-4 text-gray-700">Place New Marker</h3>
            
            <form id="marker-form" class="space-y-4">
                <input type="hidden" id="building-id" value=""> 
                <input type="hidden" id="marker-x">
                <input type="hidden" id="marker-y">
                
                <div>
                    <label for="building-name" class="block text-sm font-medium text-gray-700">Building Name</label>
                    <input type="text" id="building-name" placeholder="e.g., Library Hall" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex gap-4">
                    <button type="submit" id="save-button" class="flex-1 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" disabled>
                        Save Marker
                    </button>
                    <button type="button" id="delete-button" class="w-24 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 hidden">
                        Delete
                    </button>
                </div>
                <button type="button" id="cancel-button" class="w-full py-1 text-sm text-gray-500 hover:text-gray-700 hidden">
                    Cancel Editing
                </button>
                
                <p id="instruction" class="text-sm text-gray-500 italic mt-4">Click on the map to place a new marker.</p>
            </form>
        </div> </div> </div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const campusMap = document.getElementById('campus-map');
        const markerLayer = document.getElementById('marker-layer');
        const markerForm = document.getElementById('marker-form');
        const formTitle = document.getElementById('form-title');
        
        // Form Inputs
        const buildingIdInput = document.getElementById('building-id');
        const buildingNameInput = document.getElementById('building-name');
        const markerXInput = document.getElementById('marker-x');
        const markerYInput = document.getElementById('marker-y');

        // Buttons
        const saveButton = document.getElementById('save-button');
        const deleteButton = document.getElementById('delete-button');
        const cancelButton = document.getElementById('cancel-button');
        const instruction = document.getElementById('instruction');

        // State Variables
        let currentMarkerElement = null; // Temporary marker being placed
        let selectedMarkerElement = null; // Permanent marker being edited


        // --- UTILITY & STATE MANAGEMENT ---

        function resetFormState() {
            markerForm.reset();
            buildingIdInput.value = '';
            saveButton.disabled = true;
            
            // UI Reset
            formTitle.textContent = 'Place New Marker';
            saveButton.textContent = 'Save Marker';
            deleteButton.classList.add('hidden');
            cancelButton.classList.add('hidden');
            instruction.textContent = 'Click on the map to place a new marker.';

            // Clear active selection highlight
            if (selectedMarkerElement) {
                selectedMarkerElement.classList.remove('ring-4', 'ring-blue-500', 'ring-opacity-50');
                selectedMarkerElement = null;
            }
            
            // Remove temporary placement marker
            if (currentMarkerElement) {
                currentMarkerElement.remove();
                currentMarkerElement = null;
            }
        }

        function prepareFormForEdit(buildingId, name, xPercent, yPercent, markerEl) {
            resetFormState(); // Clear any existing state first
            
            // Set State
            buildingIdInput.value = buildingId;
            markerXInput.value = xPercent;
            markerYInput.value = yPercent;
            buildingNameInput.value = name;
            selectedMarkerElement = markerEl; // Store the element being edited

            // UI Update
            formTitle.textContent = 'Edit Building Marker';
            saveButton.textContent = 'Update Marker';
            saveButton.disabled = false;
            deleteButton.classList.remove('hidden');
            cancelButton.classList.remove('hidden');
            instruction.textContent = `Editing: ${name}`;
            
            // Highlight the selected marker
            markerEl.classList.add('ring-4', 'ring-blue-500', 'ring-opacity-50');
            buildingNameInput.focus();
        }
        
        // --- MARKER CREATION ---
        
        function createMarkerElement(id, xPercent, yPercent, name, isTemporary = false) {
            const marker = document.createElement('div');
            
            marker.className = `
                absolute w-4 h-4 rounded-full shadow-lg border-2 
                transition-all duration-200 cursor-pointer 
                ${isTemporary ? 'bg-yellow-400 border-yellow-700 animate-pulse' : 'bg-red-600 border-white hover:bg-red-800'}
            `;
            
            marker.style.left = `${xPercent}%`;
            marker.style.top = `${yPercent}%`;
            marker.style.transform = 'translate(-50%, -50%)';
            
            if (!isTemporary) {
                // Permanent markers need the ID and name for editing
                marker.dataset.buildingId = id;
                marker.dataset.name = name;
                marker.dataset.xPercent = xPercent;
                marker.dataset.yPercent = yPercent;
                marker.classList.add('permanent-marker');
                marker.classList.add('group'); // For hover label
                
                const label = document.createElement('span');
                label.textContent = name;
                label.className = `
                    absolute whitespace-nowrap -bottom-6 left-1/2 transform -translate-x-1/2 
                    text-xs font-semibold bg-gray-800 text-white p-1 rounded-md opacity-0 
                    group-hover:opacity-100 transition-opacity duration-300
                `;
                marker.appendChild(label);
            }

            return marker;
        }


        // --- EVENT LISTENERS ---

        // A. Handle Map Click for NEW Marker Placement
        campusMap.addEventListener('click', (event) => {
            // If we are currently editing an existing marker, ignore the map click
            if (buildingIdInput.value) {
                return;
            }
            
            // Clear any previous temporary marker
            if (currentMarkerElement) {
                currentMarkerElement.remove();
            }

            const x = event.offsetX;
            const y = event.offsetY;
            
            const imageWidth = campusMap.offsetWidth;
            const imageHeight = campusMap.offsetHeight;
            
            const xPercentage = (x / imageWidth) * 100;
            const yPercentage = (y / imageHeight) * 100;

            // Store the percentage coordinates in hidden fields
            markerXInput.value = xPercentage.toFixed(2);
            markerYInput.value = yPercentage.toFixed(2);
            
            saveButton.disabled = false;
            buildingNameInput.focus();

            // Display a temporary marker
            currentMarkerElement = createMarkerElement(null, xPercentage, yPercentage, 'Placing Marker...', true);
            markerLayer.appendChild(currentMarkerElement);
        });

        // B. Handle Permanent Marker Click for EDITING
        markerLayer.addEventListener('click', (event) => {
            const markerEl = event.target.closest('.permanent-marker');
            if (markerEl) {
                // Prevent the map click listener from firing immediately after selecting
                event.stopPropagation(); 
                
                // Get data from the marker's custom attributes
                const id = markerEl.dataset.buildingId;
                const name = markerEl.dataset.name;
                const x = markerEl.dataset.xPercent;
                const y = markerEl.dataset.yPercent;
                
                prepareFormForEdit(id, name, x, y, markerEl);
            }
        });

        // C. Handle Cancel Button
        cancelButton.addEventListener('click', resetFormState);

        // D. Handle Delete Button
        deleteButton.addEventListener('click', async () => {
            const id = buildingIdInput.value;
            const name = buildingNameInput.value;

            if (!confirm(`Are you sure you want to delete the marker for "${name}"?`)) {
                return;
            }

            // --- API CALL: DELETE ---
            try {
                console.log(`Sending DELETE request for Building ID: ${id}`);
                
                // 🛑 Replace with your actual Laravel API route
                // const response = await fetch(`/api/buildings/${id}`, { method: 'DELETE' });
                
                // if (!response.ok) throw new Error('Delete failed.');

                // Successful Deletion
                if (selectedMarkerElement) {
                    selectedMarkerElement.remove(); // Remove from the DOM
                }
                alert(`Marker "${name}" deleted successfully.`);
                resetFormState();

            } catch (error) {
                console.error('Error deleting marker:', error);
                alert('Failed to delete marker. Check console for details.');
            }
        });


        // E. Handle Form Submission (Create or Update)
        markerForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const buildingId = buildingIdInput.value; // Will be empty for New, filled for Edit
            const isUpdating = !!buildingId;
            
            const markerData = {
                building_id: buildingId, // Only used for the API route/payload
                name: buildingNameInput.value.trim(),
                map_x_percent: parseFloat(markerXInput.value),
                map_y_percent: parseFloat(markerYInput.value),
                // You might need to send the campus_id for new creations
                campus_id: '{{ $campus->id ?? "1" }}', 
                // Note: In Laravel, you typically use `_method: 'PUT'` in the form for updates
            };

            let method = isUpdating ? 'PUT' : 'POST';
            let url = isUpdating ? `/api/buildings/${buildingId}` : `/api/buildings`; 

            // --- API CALL: POST (Create) or PUT (Update) ---
            try {
                console.log(`${method} request to ${url} with data:`, markerData);
                
                // 🛑 Replace with your actual Laravel API route
                // const response = await fetch(url, {
                //     method: method,
                //     headers: { 
                //         'Content-Type': 'application/json',
                //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content // Ensure you have this in your head tag
                //     },
                //     body: JSON.stringify(markerData),
                // });
                // const savedMarker = await response.json(); // The backend should return the saved model

                // --- SIMULATED SUCCESS ---
                const savedMarker = {
                    id: buildingId || Date.now(), 
                    name: markerData.name, 
                    map_x_percent: markerData.map_x_percent, 
                    map_y_percent: markerData.map_y_percent 
                };
                // --- END SIMULATED SUCCESS ---


                if (isUpdating) {
                    // 1. Update the existing marker's DOM element
                    if (selectedMarkerElement) {
                        selectedMarkerElement.remove();
                    }
                    alert(`Marker "${savedMarker.name}" updated successfully!`);
                } else {
                    // 1. Remove temporary placement marker
                    if (currentMarkerElement) {
                        currentMarkerElement.remove();
                    }
                    alert(`New marker "${savedMarker.name}" created successfully!`);
                }
                
                // 2. Render the new/updated permanent marker
                const permanentMarker = createMarkerElement(
                    savedMarker.id, 
                    savedMarker.map_x_percent, 
                    savedMarker.map_y_percent, 
                    savedMarker.name, 
                    false
                );
                markerLayer.appendChild(permanentMarker);

                // 3. Reset the UI
                resetFormState(); 

            } catch (error) {
                console.error('Error saving/updating marker:', error);
                alert('Failed to save marker. Check console for details.');
            }
        });

        // Handle initial loading of existing markers (from the Blade loop)
        function setupPermanentMarkerListeners() {
            // This is necessary because the markers are rendered by Blade, not JS
            document.querySelectorAll('.permanent-marker').forEach(markerEl => {
                // Listener is added via delegation in event B, so no action needed here other than data validation.
                // We ensure the data attributes are correct for the delegated listener to work.
            });
        }

        // Initialize on page load
        resetFormState(); // Start in a clean state
        setupPermanentMarkerListeners(); // Ensure existing markers are selectable
    });
</script>
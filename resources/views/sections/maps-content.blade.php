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
            
        </div>
    </div>
</div>
@extends('layouts.app') {{-- Adjust this to your main layout --}}

@section('edit-campus')
<div id="edit-campus" class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">📝 Edit Campus: {{ $campus->name }}</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Whoops!</strong> There were some problems with your input.
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Edit Campus Form --}}
    <form action="{{ route('admin.campus.update', $campus->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        @method('PUT') {{-- Required for PUT route method --}}

        {{-- Campus Name --}}
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-bold mb-2">Campus Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $campus->name) }}" required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        {{-- Campus Map Upload --}}
        <div class="mb-4">
            <label for="map" class="block text-gray-700 font-bold mb-2">Campus Map (Optional)</label>
            @if ($campus->map)
                <p class="text-sm text-gray-600 mb-2">Current map uploaded: <a href="{{ Storage::url($campus->map) }}" target="_blank" class="text-indigo-500 hover:underline">View Map</a></p>
            @endif
            <input type="file" id="map" name="map"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            <p class="text-xs text-gray-500 mt-1">Max 2MB. Only update to change the current map.</p>
        </div>

        {{-- Buildings List --}}
        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">Buildings</label>
            <div id="buildings-container" class="space-y-2">
                {{-- Existing Buildings --}}
                @foreach ($campus->buildings as $building)
                    <div class="flex items-center building-input">
                        <input type="text" name="buildings[{{ $building->id }}]" value="{{ old('buildings.' . $building->id, $building->name) }}" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        {{-- Note: For simplicity, this doesn't include deletion. Deletion requires an AJAX call or dedicated controller action. --}}
                        <button type="button" class="remove-building-btn text-red-500 hover:text-red-700 ml-2 text-xl hidden">&times;</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-building-btn" class="mt-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-full text-sm">
                + Add New Building
            </button>
        </div>

        {{-- Submit Button --}}
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Campus
            </button>
        </div>
    </form>
</div>

<script>
    let newBuildingCounter = 0; // Use a counter for new buildings that don't have an ID yet

    document.getElementById('add-building-btn').addEventListener('click', function() {
        const container = document.getElementById('buildings-container');
        const newBuildingDiv = document.createElement('div');
        newBuildingDiv.className = 'flex items-center building-input';
        
        // Use a temporary key (e.g., new_0, new_1) for buildings without an ID
        newBuildingDiv.innerHTML = `
            <input type="text" name="buildings[new_${newBuildingCounter++}]" placeholder="New Building Name" required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <button type="button" class="remove-building-btn text-red-500 hover:text-red-700 ml-2 text-xl">&times;</button>
        `;
        container.appendChild(newBuildingDiv);
        updateRemoveButtons();
    });

    document.getElementById('buildings-container').addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-building-btn')) {
            e.target.closest('.building-input').remove();
            updateRemoveButtons();
        }
    });

    function updateRemoveButtons() {
        const inputs = document.querySelectorAll('.building-input');
        inputs.forEach((inputDiv, index) => {
            const removeBtn = inputDiv.querySelector('.remove-building-btn');
            // Show remove button only if there is more than one building input
            if (inputs.length > 1) {
                removeBtn.classList.remove('hidden');
            } else {
                removeBtn.classList.add('hidden');
            }
        });
    }

    // Initialize state
    updateRemoveButtons();
</script>
@endsection
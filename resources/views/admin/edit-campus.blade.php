<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">📝 Edit Campus: {{ $campus->campus_name }}</h1>

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
    <form action="{{ route('admin.campus.update', $campus->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md mb-6">
        @csrf
        @method('PUT') {{-- Required for PUT route method --}}

        {{-- Campus Name --}}
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-bold mb-2">Campus Name</label>
            <input type="text" id="name" name="campus_name" value="{{ old('campus_name', $campus->name) }}" required
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
            <div class="buildings-container space-y-2">
                {{-- Existing Buildings --}}
                @foreach ($campus->buildings as $building)
                    <div class="flex items-center building-input">
                        <input type="text" name="buildings[{{ $building->id }}]" value="{{ old('buildings.' . $building->id, $building->name) }}" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <button type="button" class="remove-building-btn text-red-500 hover:text-red-700 ml-2 text-xl hidden">&times;</button>
                    </div>
                @endforeach
            </div>
            <button type="button" class="add-building-btn mt-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-full text-sm">
                + Add New Building
            </button>
        </div>

        {{-- Submit Button --}}
        <div class="flex items-center justify-start">
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Campus
            </button>
        </div>
    </form>
    
    <hr class="my-6">

    {{-- 🛑 Delete Campus Button and Form --}}
    <div class="mt-8 p-4 bg-red-50 rounded-lg border border-red-200">
        <h2 class="text-xl font-bold text-red-700 mb-3">Danger Zone</h2>
        <p class="text-red-600 mb-4">Permanently delete this campus and all associated data (buildings, waste entries, etc.). This action cannot be undone.</p>
        
        <form id="delete-campus-form" action="{{ route('admin.campus.destroy', $campus->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="button" id="delete-campus-btn" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Delete Campus Permanently
            </button>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.add-building-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const form = btn.closest('form');
        const container = form.querySelector('.buildings-container');
        const newBuildingDiv = document.createElement('div');
        newBuildingDiv.className = 'flex items-center building-input';
        newBuildingDiv.innerHTML = `
            <input type="text" name="buildings[new_${Date.now()}]" placeholder="New Building Name" required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <button type="button" class="remove-building-btn text-red-500 hover:text-red-700 ml-2 text-xl">&times;</button>
        `;
        container.appendChild(newBuildingDiv);
        updateRemoveButtons(form);
    });
});

document.querySelectorAll('form').forEach(form => {
    form.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-building-btn')) {
            e.target.closest('.building-input').remove();
            updateRemoveButtons(form);
        }
    });
});

// Update buttons visibility scoped to a form
function updateRemoveButtons(form) {
    const inputs = form.querySelectorAll('.building-input');
    inputs.forEach(inputDiv => {
        const removeBtn = inputDiv.querySelector('.remove-building-btn');
        if (!removeBtn) return;
        if (inputs.length > 1) {
            removeBtn.classList.remove('hidden');
        } else {
            removeBtn.classList.add('hidden');
        }
    });
}

// Initialize all forms on page load
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach(form => updateRemoveButtons(form));
    const deleteButton = document.getElementById("delete-campus-btn");
    const deleteForm = document.getElementById("delete-campus-form");

    if (deleteButton && deleteForm) {
        deleteButton.addEventListener("click", function() {
            const campusName = "{{ $campus->name ?? 'this campus' }}"; // Assuming you have $campus available in the blade view
            
            const confirmed = confirm(`🛑 WARNING: Are you sure you want to permanently delete the campus "${campusName}"? This action cannot be undone.`);

            if (confirmed) {
                // Perform a second, more serious confirmation
                const confirmedAgain = confirm("LAST CHANCE: Deleting will remove all buildings and waste data linked to this campus. Click OK to proceed.");
                
                if (confirmedAgain) {
                    // If the user confirms twice, submit the form
                    deleteForm.submit();
                }
            }
        });
    }
});

</script>
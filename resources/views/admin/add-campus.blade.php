<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">➕ Add New Campus</h1>

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

    <form action="{{ route('admin.campus.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md add-campus-form">
        @csrf

        {{-- Campus Name --}}
        <div class="mb-4">
            <label for="campus_name" class="block text-gray-700 font-bold mb-2">Campus Name</label>
            <input type="text" id="campus_name" name="campus_name" value="{{ old('campus_name') }}" required
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        {{-- Campus Map Upload --}}
        <div class="mb-4">
            <label for="campus_map" class="block text-gray-700 font-bold mb-2">Campus Map (Optional)</label>
            <input type="file" id="campus_map" name="campus_map"
                  class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            <p class="text-xs text-gray-500 mt-1">Max 2MB. Image format.</p>
        </div>

        {{-- Buildings List --}}
        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">Buildings</label>
            <div class="buildings-container space-y-2">
                {{-- Initial Building Input --}}
                <div class="flex items-center building-input">
                    <input type="text" name="buildings[]" placeholder="Building Name (e.g., Admin Block)" required
                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <button type="button" class="remove-building-btn text-red-500 hover:text-red-700 ml-2 text-xl hidden">&times;</button>
                </div>
            </div>
            <button type="button" class="add-building-btn mt-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-full text-sm">
                + Add Another Building
            </button>
        </div>

        {{-- Submit Button --}}
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Save Campus
            </button>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('.add-campus-form').forEach(form => {
    // Add Building Button
    form.querySelector('.add-building-btn').addEventListener('click', () => {
        const container = form.querySelector('.buildings-container');
        const newBuildingDiv = document.createElement('div');
        newBuildingDiv.className = 'flex items-center building-input';
        newBuildingDiv.innerHTML = `
            <input type="text" name="buildings[new_${Date.now()}]" placeholder="Building Name" required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <button type="button" class="remove-building-btn text-red-500 hover:text-red-700 ml-2 text-xl">&times;</button>
        `;
        container.appendChild(newBuildingDiv);
        updateRemoveButtons(form);
    });

    // Remove Building Button (event delegation)
    form.addEventListener('click', e => {
        if (e.target && e.target.classList.contains('remove-building-btn')) {
            e.target.closest('.building-input').remove();
            updateRemoveButtons(form);
        }
    });

    // Initialize remove button visibility
    updateRemoveButtons(form);
});

// Function to show/hide remove buttons based on count
function updateRemoveButtons(form) {
    const inputs = form.querySelectorAll('.building-input');
    inputs.forEach(inputDiv => {
        const removeBtn = inputDiv.querySelector('.remove-building-btn');
        if (!removeBtn) return;
        if (inputs.length > 1) removeBtn.classList.remove('hidden');
        else removeBtn.classList.add('hidden');
    });
}
</script>

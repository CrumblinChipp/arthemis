<div id="adminModal" class="fixed inset-0 hidden flex items-center justify-center bg-black bg-opacity-50">
  <div class="bg-white p-6 rounded-md shadow-lg w-80">
      <h2 class="text-xl font-bold mb-4">Admin Verification</h2>

      <input type="password" id="adminPassword"
          class="border w-full px-3 py-2 mb-3 rounded-md"
          placeholder="Enter admin password">

      <button onclick="verifyAdmin()"
          class="bg-blue-600 w-full text-white py-2 rounded-md">
          Verify
      </button>

      <button onclick="closeAdminModal()"
          class="mt-2 w-full py-2 border rounded-md">
          Cancel
      </button>

      <p id="adminError" class="text-red-600 mt-2 hidden">
          Incorrect password.
      </p>
  </div>
</div>

<button id="openWasteModal"
  class="fixed bottom-6 right-6 bg-green-600 text-white px-5 py-3 rounded-full shadow-lg 
      hover:bg-green-700 transition z-50">
  Add Waste Entry
</button>

<div id="wasteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50 items-center justify-center">
  <div class="bg-white rounded-xl p-6 w-full max-w-lg shadow-xl">

    <h2 class="text-2xl font-bold mb-4">Submit Waste Entry</h2>

    {{-- Name --}}
    <label class="block font-medium">Your Name</label>
    <input id="entryName" type="text" class="w-full border rounded p-2 mb-3">

    {{-- Campus Dropdown (Dynamic) --}}
    <label class="block font-medium">Campus</label>
    <select id="entryCampus" class="w-full border rounded p-2 mb-3">
      <option value="" disabled selected>Select Campus</option>
      @foreach ($campuses as $campus)
        <option value="{{ $campus->id }}">{{ $campus->name }}</option>
      @endforeach
    </select>

    {{-- Building Dropdown (Dynamic Based on Campus) --}}
    <label class="block font-medium">Building</label>
    <select id="entryBuilding" class="w-full border rounded p-2 mb-4">
      <option value="">Select a Campus First</option>
    </select>

    {{-- Waste Categories --}}
    <label class="block font-medium mb-1">Biodegradable (kg)</label>
    <input id="bio" type="number" class="w-full border rounded p-2 mb-3">

    <label class="block font-medium mb-1">Recyclable (kg)</label>
    <input id="recyclable" type="number" class="w-full border rounded p-2 mb-3">

    <label class="block font-medium mb-1">Residual (kg)</label>
    <input id="residual" type="number" class="w-full border rounded p-2 mb-3">

    <label class="block font-medium mb-1">Infectious (kg)</label>
    <input id="infectious" type="number" class="w-full border rounded p-2 mb-4">

    <div class="flex justify-end gap-3 mt-5">
      <button id="cancelMain"
        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
        Cancel
      </button>

      <button id="submitMain"
        class="px-5 py-2 bg-green-600 text-white rounded hover:bg-green-700">
        Submit
      </button>
    </div>

  </div>
</div>


<div id="confirmModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50 items-center justify-center">
  <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl">

    <h2 class="text-xl font-bold mb-4">Confirm Submission</h2>
    <p class="mb-6">Are you sure you want to submit this waste entry?</p>

    <div class="flex justify-end gap-3">
      <button id="cancelConfirm" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
        Cancel
      </button>

      <button id="confirmSubmit" class="px-5 py-2 bg-green-600 text-white rounded hover:bg-green-700">
        Yes, Submit
      </button>
    </div>
  </div>
</div>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.20/dist/full.css" rel="stylesheet" />
  <title>Waste Records Table</title>
</head>

<body class="bg-gray-50 p-6">

  <!-- TOP BAR -->
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">

      <!-- Calendar Button -->
      <button class="btn btn-primary btn-md">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar">
          <rect width="18" height="18" x="3" y="4" rx="2" />
          <line x1="16" x2="16" y1="2" y2="6" />
          <line x1="8" x2="8" y1="2" y2="6" />
          <line x1="3" x2="21" y1="10" y2="10" />
        </svg>
      </button>

      <!-- Toggle Columns -->
      <div class="dropdown">
        <label tabindex="0" class="btn">Toggle Columns</label>
        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
          <li><a>Date</a></li>
          <li><a>Building</a></li>
          <li><a>Waste Type</a></li>
          <li><a>Weight</a></li>
        </ul>
      </div>

      <!-- Search -->
      <input type="text" placeholder="Search" class="input input-bordered w-80" />

    </div>

    <!-- Add Waste Button -->
    <button class="btn btn-success">Add Waste</button>
  </div>

  <!-- TABLE -->
  <div class="overflow-x-auto bg-white shadow-md rounded-lg">
    <table class="table table-zebra w-full">
      <thead>
        <tr>
          <th><input type="checkbox" class="checkbox" /></th>
          <th>Date</th>
          <th>Building</th>
          <th>Waste Type</th>
          <th>Weight (kg)</th>
          <th class="text-right">Actions</th>
        </tr>
      </thead>
      <tbody>

        <!-- Sample repeated rows (replace with backend data) -->
        <tr>
          <td><input type="checkbox" class="checkbox" /></td>
          <td>03-15-24</td>
          <td>SSC</td>
          <td>total</td>
          <td>0</td>
          <td class="text-right">
            <div class="dropdown dropdown-end">
              <label tabindex="0" class="btn btn-sm">⋮</label>
              <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-32">
                <li><a>Edit</a></li>
                <li><a>Delete</a></li>
              </ul>
            </div>
          </td>
        </tr>

        <tr>
          <td><input type="checkbox" class="checkbox" /></td>
          <td>03-15-24</td>
          <td>CICS</td>
          <td>biodegradable</td>
          <td>0</td>
          <td class="text-right">
            <div class="dropdown dropdown-end">
              <label tabindex="0" class="btn btn-sm">⋮</label>
              <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-32">
                <li><a>Edit</a></li>
                <li><a>Delete</a></li>
              </ul>
            </div>
          </td>
        </tr>

        <!-- You can add more rows as needed -->

      </tbody>
    </table>
  </div>

  <!-- PAGINATION -->
  <div class="flex justify-between items-center mt-4 text-sm text-gray-600">
    <div>
      Rows per page:
      <select class="select select-bordered select-sm w-20">
        <option>20</option>
        <option>50</option>
        <option>100</option>
      </select>
    </div>

    <div>
      1–20 of 4135
      <button class="btn btn-sm mx-1">⟨⟨</button>
      <button class="btn btn-sm mx-1">⟨</button>
      <button class="btn btn-sm mx-1">⟩</button>
      <button class="btn btn-sm mx-1">⟩⟩</button>
    </div>
  </div>

</body>
</html>

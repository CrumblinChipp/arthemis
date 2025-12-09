{{-- Admin Settings Wrapper --}}
<div id="admin-wrapper" class="flex w-full min-h-screen">
    {{-- Middle Sidebar: Admin Navigation --}}
    <div id="admin-nav"
        class="w-full md:w-64 bg-white min-h-screen border-r shrink-0 flex flex-col
                        transition-transform duration-300 md:translate-x-0
                        fixed md:static inset-0 z-40">

        <h2 class="text-xl font-bold p-4 border-b">Admin Setting</h2>

        <button data-admin-page="add-campus"
                class="admin-nav-item p-4 text-left hover:bg-green-900/20 border-b">
            + Add Campus
        </button>

        <button data-admin-page="edit-campus"
                class="admin-nav-item p-4 text-left hover:bg-green-900/20 border-b">
            Edit Campus
        </button>

        <button data-admin-page="edit-building"
                class="admin-nav-item p-4 text-left hover:bg-green-900/20 border-b">
            Edit Building
        </button>
    </div>

    {{-- Right Panel: Admin Page Content --}}
    <div id="admin-content" class="hidden md:block flex-1 p-6">
        <button id="admin-back"
                class="md:hidden mb-4 px-4 py-2 bg-green-800 text-white rounded">
            ← Back
        </button>

        <div id="admin-content-inner">
            {{-- Include the individual admin pages here --}}
            @include('admin.add-campus')
            @include('admin.edit-campus')
            <div id="edit-building-page" class="hidden">
                <h2 class="text-2xl font-bold">Edit Building</h2>
            </div>
        </div>
    </div>
</div>
<aside class="
        bg-gradient-to-r from-emerald-500 to-emerald-700 text-white flex items-center
        fixed top-0 left-0 z-50
        w-full h-14
        md:flex-col md:items-center md:justify-start
        md:w-72 md:h-screen md:fixed
        md:pt-6
        ">

    {{-- Logo --}}
    <div class="flex items-center gap-3 md:mb-8">
        <div id="openAdminModal" class="h-8 w-8 rounded-full bg-green-700 flex items-center justify-center text-white font-bold">A</div>

        {{-- Hide text on mobile --}}
        <div class="hidden md:block">
            <div class="text-white font-semibold">ARTHEMIS</div>
        </div>
    </div>
    {{-- Navigation --}}
    <a href="#"
        data-nav="dashboard"
        class="nav-item w-full flex flex-col
        md:flex-row items-center gap-1
        px-3 py-2 rounded-md text-gray-200
        hover:bg-green-900/30 transition">
        <span>🏠︎</span>
        <span class="hidden md:inline text-2xl">Dashboard</span>
    </a>

    {{-- Maps --}}
    <a href="#"
        data-nav="maps"
        class="nav-item w-full flex flex-col
        md:flex-row items-center gap-1
        px-3 py-2 rounded-md text-gray-200
        hover:bg-green-900/30 transition">
        <span>⚲</span>
        <span class="hidden md:inline text-2xl">Map</span>
    </a>

    {{-- Data --}}
    <a href="#"
        data-nav="data"
        class="nav-item w-full flex flex-col
        md:flex-row items-center gap-1
        px-3 py-2 rounded-md text-gray-200
        hover:bg-green-900/30 transition">
        <span>𝄜</span>
        <span class="hidden md:inline text-2xl">Data</span>
    </a>

    {{-- Admin Setting --}}
    @if(session()->has('admin_verified') && session('admin_verified') === true)
        <a href="#"
        data-nav="admin"
        class="nav-item w-full flex flex-col
        md:flex-row items-center gap-1
        px-3 py-2 rounded-md text-gray-200
        hover:bg-green-900/30 transition">
        <span>⚙</span>
        <span class="hidden md:inline text-2xl">Admin Settings</span>
        </a>
    @endif
    <form action="{{ route('logout') }}" method="POST" class="w-full md:w-auto">
        @csrf
        <button type="submit" class="flex items-center justify-center gap-2 w-full px-7 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold text-sm rounded-lg transition-all duration-300 hover:-translate-y-0.5 shadow-md">
            <span>⏻</span>
            <span class="hidden md:inline">Logout</span>
        </button>
    </form>

</aside>
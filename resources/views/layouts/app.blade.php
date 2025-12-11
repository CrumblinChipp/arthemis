<!doctype html>
<html lang="en">
<head>
    @include('partials.head') {{-- Partial for head content --}}
</head>
<body class="bg-gray-100 text-gray-800">

<div class="flex w-full">
    {{-- Sidebar --}}
    @include('partials.sidebar') {{-- Partial for sidebar/navigation --}}

    {{-- Main Content Area --}}
    <main id="mainContent" class="pt-16 md:pt-[15px] ml-0 md:ml-[300px] w-full">
        @yield('content')
    </main>
</div>

    @include('partials.modals') {{-- Partial for modals --}}

<script>
    const addCampusRoute = "{{ route('admin.campus.create') }}"; 
</script>
 
<script src="{{ asset('js/dashboardHandler.js') }}"></script>
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col space-y-3">
    </div>
</body>
</html>
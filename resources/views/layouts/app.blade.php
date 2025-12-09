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
    // Ensure this route is defined in your routes/web.php
    const addCampusRoute = "{{ route('admin.campus.create') }}"; 
</script>

<script src="{{ asset('js/dashboardHandler.js') }}"></script>

</body>
</html>
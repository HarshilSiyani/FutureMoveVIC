<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sustainability Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('styles')
</head>
<body class="bg-gray-100">
    <nav class="bg-green-600 text-white p-4">
        <div class="container mx-auto">
            <h1 class="text-2xl font-bold">Sustainability Dashboard</h1>
        </div>
    </nav>

    <main class="container mx-auto mt-8">
        @yield('content')
    </main>

    <footer class="bg-green-600 text-white p-4 mt-8">
        <div class="container mx-auto text-center">
            <p>&copy; 2023 Sustainability Dashboard. All rights reserved.</p>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
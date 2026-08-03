<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', config('app.name'))</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50">
        <header class="bg-white border-b border-gray-100">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="{{ route('news.index') }}" class="font-semibold text-lg text-gray-800">
                    {{ config('app.name') }}
                </a>
                <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    {{ __('Staff Login') }}
                </a>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            @yield('content')
        </main>

        <footer class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </footer>
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Toast Container -->
        <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

        <!-- Toast Notification System -->
        <script>
            // Toast notification function
            window.showToast = function(message, type = 'success') {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');

                // Set colors based on type
                let bgColor, textColor, icon;
                switch(type) {
                    case 'success':
                        bgColor = 'bg-green-500';
                        textColor = 'text-white';
                        icon = '✓';
                        break;
                    case 'error':
                        bgColor = 'bg-red-500';
                        textColor = 'text-white';
                        icon = '✗';
                        break;
                    case 'warning':
                        bgColor = 'bg-yellow-500';
                        textColor = 'text-white';
                        icon = '⚠';
                        break;
                    case 'info':
                        bgColor = 'bg-blue-500';
                        textColor = 'text-white';
                        icon = 'ℹ';
                        break;
                    default:
                        bgColor = 'bg-gray-700';
                        textColor = 'text-white';
                        icon = '•';
                }

                toast.className = `${bgColor} ${textColor} px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 min-w-[300px] transform transition-all duration-300 translate-x-0 opacity-100`;
                toast.innerHTML = `
                    <span class="text-2xl">${icon}</span>
                    <span class="flex-1 font-medium">${message}</span>
                    <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200 text-xl font-bold">&times;</button>
                `;

                container.appendChild(toast);

                // Auto remove after 4 seconds
                setTimeout(() => {
                    toast.style.transform = 'translateX(400px)';
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 4000);
            };
        </script>
    </body>
</html>

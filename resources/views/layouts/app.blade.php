<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel System</title>

    {{-- TailwindCSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-800">

    {{-- NAVBAR --}}
    <nav class="bg-white shadow px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">

            <a href="/" class="text-xl font-bold text-blue-600">
                Travel System
            </a>

            <div class="flex items-center space-x-6">

                <a href="/trips" class="hover:text-blue-600">
                    Trips
                </a>

                @auth
                    <span class="text-gray-600">Hi, {{ auth()->user()->name }}</span>

                    <form action="/logout" method="POST" class="inline-block">
                        @csrf
                        <button 
                            type="submit" 
                            class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="/login" class="hover:text-blue-600">Login</a>
                    <a href="/register" class="hover:text-blue-600">Register</a>
                @endauth

            </div>  

        </div>
    </nav>

    {{-- PAGE CONTENT --}}
    <main class="max-w-7xl mx-auto mt-6">
        
        {{-- GLOBAL FLASH MESSAGE --}}
        @if(session('api_message'))
            <div class="bg-blue-100 text-blue-800 p-4 rounded mb-4">
                <strong>{{ session('api_message') }}</strong>
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>

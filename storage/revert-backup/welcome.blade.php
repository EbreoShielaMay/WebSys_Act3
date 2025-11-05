<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Portfolio') }}</title>
    
    <!-- Vite assets -->
    @vite(['resources/css/app.css', 'resources/css/styles.css', 'resources/js/app.js', 'resources/js/script.js'])
</head>
<body class="bg-purple-900 text-white">
    <!-- Navigation -->
    <nav class="fixed w-full bg-purple-800 shadow z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="logo-text">Portfolio</span>
                    </div>
                </div>
                <div class="flex items-center">
                    @auth
                        <a href="{{ route('portfolio.edit') }}" class="btn-primary mr-4">
                            Edit Portfolio
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn-primary">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="hero" class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-5xl font-bold mb-4">{{ $content['hero']['name'] ?? 'Your Name' }}</h1>
            <p class="text-2xl mb-8">{{ $content['hero']['title'] ?? 'Web Developer' }}</p>
            <p class="max-w-2xl mx-auto">{{ $content['hero']['description'] ?? 'I create stunning web applications...' }}</p>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-purple-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold mb-12 text-center">About Me</h2>
            <div class="grid md:grid-cols-2 gap-12">
                <div>
                    <p class="text-lg">{{ $content['about']['text'] ?? "I'm a passionate web developer..." }}</p>
                </div>
                <div>
                    <div class="bg-purple-700 p-6 rounded-lg">
                        <h3 class="text-xl font-bold mb-4">Experience</h3>
                        <p class="text-4xl font-bold">{{ $content['about']['years'] ?? '5' }}+ Years</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Skills Section -->
    <section id="skills" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold mb-12 text-center">Skills</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-purple-800 p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-4">Frontend Development</h3>
                    <div class="relative pt-1">
                        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-purple-700">
                            <div style="width: {{ $content['skills']['frontend'] ?? '95' }}%" 
                                 class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500">
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-semibold inline-block">
                                {{ $content['skills']['frontend'] ?? '95' }}%
                            </span>
                        </div>
                    </div>
                </div>
                <div class="bg-purple-800 p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-4">Backend Development</h3>
                    <div class="relative pt-1">
                        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-purple-700">
                            <div style="width: {{ $content['skills']['backend'] ?? '90' }}%" 
                                 class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500">
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-semibold inline-block">
                                {{ $content['skills']['backend'] ?? '90' }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-purple-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold mb-12 text-center">Contact Me</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <h3 class="text-xl font-bold mb-4">Email</h3>
                    <p>{{ $content['contact']['email'] ?? 'contact@example.com' }}</p>
                </div>
                <div class="text-center">
                    <h3 class="text-xl font-bold mb-4">Phone</h3>
                    <p>{{ $content['contact']['phone'] ?? '+1 (234) 567-890' }}</p>
                </div>
                <div class="text-center">
                    <h3 class="text-xl font-bold mb-4">Location</h3>
                    <p>{{ $content['contact']['location'] ?? 'New York, United States' }}</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-purple-800 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; {{ date('Y') }} All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

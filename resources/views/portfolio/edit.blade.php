<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Portfolio</title>
    @vite(['resources/css/styles.css','resources/js/script.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* small helper overrides for editor layout */
        .editor-toolbar { display:flex; gap:0.5rem; margin-bottom:1rem; }
        .editor-section { background:rgba(10,0,21,0.6); border-radius:12px; padding:1rem; margin-bottom:1rem; }
        .editor-row { display:flex; gap:0.75rem; align-items:center; margin-bottom:0.5rem; }
        .editor-row input, .editor-row textarea { flex:1; }
        .remove-field { background:#7c3aed; border:none; color:#fff; padding:0.4rem 0.6rem; border-radius:6px; cursor:pointer; }
        .add-field { background:linear-gradient(90deg,#8b5cf6,#ec4899); border:none; color:#fff; padding:0.6rem 0.8rem; border-radius:8px; cursor:pointer; }
    </style>
</head>
<body>
    <main style="padding:2rem;">
        <div style="max-width:1100px;margin:0 auto;color:#fff;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                <h1>Edit Portfolio</h1>
                <div class="editor-toolbar">
                    <a href="{{ route('home') }}" class="btn-outline">View public</a>
                </div>
            </div>

            @if(session('success'))
                <div style="padding:0.75rem 1rem;background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.2);border-radius:8px;margin-bottom:1rem;">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('portfolio.update') }}">
                @csrf
                @method('PUT')

                <div id="editor-sections">
                    @php $i = 0; @endphp
                    @foreach($content ?? [] as $section => $items)
                        <div class="editor-section" data-section="{{ $section }}">
                            <h3 style="margin-top:0; text-transform:capitalize">{{ $section }}</h3>
                            @foreach($items as $key => $value)
                                <div class="editor-row">
                                    <input type="hidden" name="content[{{ $i }}][section]" value="{{ $section }}">
                                    <input type="text" name="content[{{ $i }}][key]" value="{{ $key }}" style="max-width:220px;" placeholder="key">
                                    @if(is_array($value))
                                        <textarea name="content[{{ $i }}][value]">{{ json_encode($value) }}</textarea>
                                    @else
                                        <input type="text" name="content[{{ $i }}][value]" value="{{ $value }}" placeholder="value">
                                    @endif
                                    <button type="button" class="remove-field" onclick="removeField(this)">Remove</button>
                                </div>
                                @php $i++; @endphp
                            @endforeach

                            <div style="margin-top:0.5rem;">
                                <button type="button" class="add-field" onclick="addField('{{ $section }}')">Add field to {{ $section }}</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="display:flex;gap:0.75rem; margin-top:1rem;">
                    <button type="submit" class="form-submit-btn">Save changes</button>
                    <button type="button" class="btn-outline" onclick="location.reload()">Cancel</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // simple client-side helpers to add/remove fields
        function removeField(btn) {
            const row = btn.closest('.editor-row');
            if (row) row.remove();
        }

        function addField(section) {
            const container = document.querySelector('.editor-section[data-section="'+section+'"]');
            if (!container) return;
            // find highest index used to append new inputs with next index
            const existing = document.querySelectorAll('[name^="content["]');
            let maxIndex = -1;
            existing.forEach(el => {
                const m = el.name.match(/^content\[(\d+)\]/);
                if (m) maxIndex = Math.max(maxIndex, parseInt(m[1],10));
            });
            const next = maxIndex + 1;
            const row = document.createElement('div');
            row.className = 'editor-row';
            row.innerHTML = `\n                <input type="hidden" name="content[${next}][section]" value="${section}">\n                <input type="text" name="content[${next}][key]" value="" style="max-width:220px;" placeholder="key">\n                <input type="text" name="content[${next}][value]" value="" placeholder="value">\n                <button type="button" class="remove-field" onclick="removeField(this)">Remove</button>\n            `;
            container.insertBefore(row, container.querySelector('div'));
        }
    </script>

    <script> if (typeof lucide !== 'undefined') try { lucide.createIcons(); } catch (e) {}</script>
</body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Portfolio Content - {{ config('app.name', 'Portfolio') }}</title>
    
    <!-- Vite assets -->
    @vite(['resources/css/app.css', 'resources/css/styles.css', 'resources/js/app.js', 'resources/js/script.js'])
</head>
<body class="bg-purple-900 text-white">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-purple-800 shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ url('/') }}" class="nav-logo">
                                <span class="logo-text">Portfolio</span>
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn-primary">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if(session('success'))
                <div class="mb-4 p-4 bg-green-500 text-white rounded">
                    {{ session('success') }}
                </div>
                @endif

                <form action="{{ route('portfolio.update') }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- Hero Section -->
                    <div class="bg-purple-800 p-6 rounded-lg shadow">
                        <h2 class="text-2xl font-bold mb-4">Hero Section</h2>
                        <div class="grid gap-4">
                            <div>
                                <label class="block mb-2">Name</label>
                                <input type="text" name="content[hero_name][section]" value="hero" hidden>
                                <input type="text" name="content[hero_name][key]" value="name" hidden>
                                <input type="text" name="content[hero_name][value]" 
                                       value="{{ $content['hero']['name'] ?? 'Your Name' }}"
                                       class="w-full p-2 rounded bg-purple-700 text-white">
                            </div>
                            <div>
                                <label class="block mb-2">Title</label>
                                <input type="text" name="content[hero_title][section]" value="hero" hidden>
                                <input type="text" name="content[hero_title][key]" value="title" hidden>
                                <input type="text" name="content[hero_title][value]"
                                       value="{{ $content['hero']['title'] ?? 'Web Developer' }}"
                                       class="w-full p-2 rounded bg-purple-700 text-white">
                            </div>
                            <div>
                                <label class="block mb-2">Description</label>
                                <textarea name="content[hero_description][section]" hidden>hero</textarea>
                                <textarea name="content[hero_description][key]" hidden>description</textarea>
                                <textarea name="content[hero_description][value]" rows="3"
                                          class="w-full p-2 rounded bg-purple-700 text-white">{{ $content['hero']['description'] ?? 'I create stunning web applications...' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- About Section -->
                    <div class="bg-purple-800 p-6 rounded-lg shadow">
                        <h2 class="text-2xl font-bold mb-4">About Section</h2>
                        <div class="grid gap-4">
                            <div>
                                <label class="block mb-2">About Text</label>
                                <textarea name="content[about_text][section]" hidden>about</textarea>
                                <textarea name="content[about_text][key]" hidden>text</textarea>
                                <textarea name="content[about_text][value]" rows="6"
                                          class="w-full p-2 rounded bg-purple-700 text-white">{{ $content['about']['text'] ?? "I'm a passionate web developer..." }}</textarea>
                            </div>
                            <div>
                                <label class="block mb-2">Years Experience</label>
                                <input type="number" name="content[about_years][section]" value="about" hidden>
                                <input type="text" name="content[about_years][key]" value="years" hidden>
                                <input type="number" name="content[about_years][value]"
                                       value="{{ $content['about']['years'] ?? '5' }}"
                                       class="w-full p-2 rounded bg-purple-700 text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Skills Section -->
                    <div class="bg-purple-800 p-6 rounded-lg shadow">
                        <h2 class="text-2xl font-bold mb-4">Skills</h2>
                        <div class="grid gap-4">
                            <div>
                                <label class="block mb-2">Frontend Skills</label>
                                <input type="text" name="content[skills_frontend][section]" value="skills" hidden>
                                <input type="text" name="content[skills_frontend][key]" value="frontend" hidden>
                                <input type="text" name="content[skills_frontend][value]"
                                       value="{{ $content['skills']['frontend'] ?? '95' }}"
                                       class="w-full p-2 rounded bg-purple-700 text-white">
                            </div>
                            <div>
                                <label class="block mb-2">Backend Skills</label>
                                <input type="text" name="content[skills_backend][section]" value="skills" hidden>
                                <input type="text" name="content[skills_backend][key]" value="backend" hidden>
                                <input type="text" name="content[skills_backend][value]"
                                       value="{{ $content['skills']['backend'] ?? '90' }}"
                                       class="w-full p-2 rounded bg-purple-700 text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Projects Section -->
                    <div class="bg-purple-800 p-6 rounded-lg shadow">
                        <h2 class="text-2xl font-bold mb-4">Projects</h2>
                        <!-- Add project editing fields here -->
                    </div>

                    <!-- Contact Section -->
                    <div class="bg-purple-800 p-6 rounded-lg shadow">
                        <h2 class="text-2xl font-bold mb-4">Contact Information</h2>
                        <div class="grid gap-4">
                            <div>
                                <label class="block mb-2">Email</label>
                                <input type="email" name="content[contact_email][section]" value="contact" hidden>
                                <input type="text" name="content[contact_email][key]" value="email" hidden>
                                <input type="email" name="content[contact_email][value]"
                                       value="{{ $content['contact']['email'] ?? 'contact@example.com' }}"
                                       class="w-full p-2 rounded bg-purple-700 text-white">
                            </div>
                            <div>
                                <label class="block mb-2">Phone</label>
                                <input type="text" name="content[contact_phone][section]" value="contact" hidden>
                                <input type="text" name="content[contact_phone][key]" value="phone" hidden>
                                <input type="text" name="content[contact_phone][value]"
                                       value="{{ $content['contact']['phone'] ?? '+1 (234) 567-890' }}"
                                       class="w-full p-2 rounded bg-purple-700 text-white">
                            </div>
                            <div>
                                <label class="block mb-2">Location</label>
                                <input type="text" name="content[contact_location][section]" value="contact" hidden>
                                <input type="text" name="content[contact_location][key]" value="location" hidden>
                                <input type="text" name="content[contact_location][value]"
                                       value="{{ $content['contact']['location'] ?? 'New York, United States' }}"
                                       class="w-full p-2 rounded bg-purple-700 text-white">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary px-8 py-3">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
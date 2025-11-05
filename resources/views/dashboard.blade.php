<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Dashboard') }}</title>

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Small override for the admin form to keep it compact inside the dashboard */
        .admin-form { max-width:1100px; margin:2.5rem auto; }
        .admin-form .form-row { display:flex; gap:1rem; flex-wrap:wrap; }
        .admin-form .form-group { flex:1 1 300px; }
        .admin-save { position:fixed; right:20px; bottom:20px; z-index:1000; }
        /* Modern card-like admin sections with subtle glass effect */
        .admin-section { margin:1.5rem 0; padding:1.25rem; border-radius:12px; background:linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.02)); box-shadow:0 6px 30px rgba(2,6,23,0.6); border:1px solid rgba(255,255,255,0.04); }
        .admin-section h2 { display:flex; align-items:center; gap:.75rem; font-size:1.25rem; color: #f3e8ff; }

        /* Inputs: dark translucent background but high contrast text */
        .admin-form .input { width:100%; padding:0.6rem 0.75rem; border-radius:10px; border:1px solid rgba(255,255,255,0.06); background:rgba(255,255,255,0.02); color:#eef2ff; box-shadow: inset 0 1px 0 rgba(255,255,255,0.02); }
        .admin-form .input:focus { outline:none; box-shadow:0 6px 24px rgba(124,58,237,0.12); border-color: rgba(124,58,237,0.6); }
        .admin-form label { display:block; color: rgba(216,180,254,0.95); font-weight:600; margin-bottom:0.35rem; }

        /* Make file inputs feel consistent */
        .admin-form input[type=file] { padding:0.4rem; }

    /* Project card tweaks */
    .project-edit { transition: transform 0.18s ease, box-shadow .18s ease; background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.015)); border-radius:10px; padding:0.85rem; border:1px solid rgba(255,255,255,0.04); }
    .project-edit:hover { transform: translateY(-6px); box-shadow:0 18px 40px rgba(2,6,23,0.65); }
    .project-edit h3 { color:#fff; margin-bottom:.5rem; }

    /* Remove button visual improvements (red, accessible) */
    .remove-project { background:#ff4757; border:1px solid rgba(255,71,87,0.15); color:#fff; padding:6px 10px; border-radius:8px; cursor:pointer; }
    .remove-project:hover { background:#e0434f; transform:translateY(-2px); box-shadow:0 8px 20px rgba(224,67,79,0.16); }

        /* Primary action button (Save) — modern gradient with shadow */
        .btn-primary { background:linear-gradient(90deg,#7c3aed,#ec4899); color:#fff; padding:0.8rem 1.1rem; border-radius:10px; border:none; cursor:pointer; font-weight:600; box-shadow:0 8px 30px rgba(124,58,237,0.2); }
        .btn-primary:hover { transform:translateY(-3px); box-shadow:0 18px 50px rgba(124,58,237,0.22); }

        /* Alerts */
        .alert { border-radius:8px; padding:0.6rem; }

        /* Make success message more visible */
        #success-alert { background: linear-gradient(90deg, rgba(16,185,129,0.12), rgba(34,197,94,0.06)); border:1px solid rgba(34,197,94,0.12); color:#bbf7d0; }

        /* Small responsive tweaks */
        @media (max-width:640px){
            .admin-form { margin:1rem; }
            .admin-save { right:12px; bottom:12px; }
        }

        /* Header layout and logout button */
        .dashboard-header { display:flex; align-items:center; justify-content:space-between; gap:1rem; }
        .dashboard-header h1 { color: #fff; font-size:1.4rem; }
        .logout-btn { background:transparent; border:1px solid rgba(255,255,255,0.06); color:#ffd6fb; padding:0.45rem 0.8rem; border-radius:8px; cursor:pointer; font-weight:600; }
        .logout-btn:hover { background: rgba(255,255,255,0.02); color:#fff; transform:translateY(-2px); }
    </style>
</head>
<body>

@php
    $content = \App\Models\PortfolioContent::all()->groupBy('section');
    function getVal($content, $section, $key) {
        if(!isset($content[$section])) return '';
        $item = $content[$section]->firstWhere('key', $key);
        return $item ? $item->value : '';
    }
@endphp

<main class="dashboard-main">
    <header class="dashboard-header" style="padding:1rem 2rem;">
        <h1>Dashboard</h1>
        <div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
        @if(session('success'))
            <div id="success-alert" class="alert alert-success" style="margin-top:0.75rem;padding:0.6rem;border-radius:6px;background:#ecfdf5;color:#065f46;">
            {{ session('success') }}
            </div>
            <script>
            setTimeout(function() {
                var el = document.getElementById('success-alert');
                if (el) el.style.display = 'none';
            }, 3000);
            </script>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="margin-top:0.75rem;padding:0.6rem;border-radius:6px;background:#fff1f2;color:#991b1b;">
                <strong>There were validation errors:</strong>
                <ul style="margin:0.5rem 0 0 1rem;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </header>

    <form class="admin-form" action="{{ route('portfolio.update') }}" method="POST" enctype="multipart/form-data" style="padding:1rem 2rem;">
        @csrf
        @method('PUT')

        <!-- HERO -->
        <section class="admin-section">
            <h2>Home</h2>
            <div class="form-row">
                <div class="form-group">
                    <label>Subtitle</label>
                    <input type="text" name="content[hero_subtitle][section]" value="hero" hidden>
                    <input type="text" name="content[hero_subtitle][key]" value="subtitle" hidden>
                    <input class="input" type="text" name="content[hero_subtitle][value]" value="{{ getVal($content,'hero','subtitle') }}">
                </div>

                <div class="form-group">
                    <label>Name</label>
                    <input class="input" type="text" name="content[hero_name][value]" value="{{ getVal($content,'hero','name') }}">
                    <input type="hidden" name="content[hero_name][section]" value="hero">
                    <input type="hidden" name="content[hero_name][key]" value="name">
                </div>

                <div class="form-group">
                    <label>Title</label>
                    <input class="input" type="text" name="content[hero_job][value]" value="{{ getVal($content,'hero','job') }}">
                    <input type="hidden" name="content[hero_job][section]" value="hero">
                    <input type="hidden" name="content[hero_job][key]" value="job">
                </div>
                <div class="form-group">
                    <label>Profile Image</label>
                    @php $heroImg = getVal($content,'hero','profile_image'); @endphp
                    @if($heroImg)
                        <div style="margin-bottom:.5rem;">
                            <img src="{{ Str::startsWith($heroImg, 'http') ? $heroImg : asset('storage/'.ltrim($heroImg,'/')) }}" alt="profile" style="max-width:160px; border-radius:6px;" />
                        </div>
                    @endif
                    <input class="input" type="file" name="content_files[hero_profile_image]" accept="image/*">
                    <input type="hidden" name="content_file_metas[hero_profile_image][section]" value="hero">
                    <input type="hidden" name="content_file_metas[hero_profile_image][key]" value="profile_image">
                </div>
            </div>

            <div class="form-group" style="margin-top:1rem;">
                <label>Description</label>
                <textarea class="input" name="content[hero_description][value]" rows="4">{{ getVal($content,'hero','description') }}</textarea>
                <input type="hidden" name="content[hero_description][section]" value="hero">
                <input type="hidden" name="content[hero_description][key]" value="description">
            </div>
        </section>

        <!-- ABOUT -->
        <section class="admin-section">
            <h2>About</h2>
            <div class="form-row">
                <div class="form-group">
                    <label>About Text (paragraph 1)</label>
                    <textarea class="input" name="content[about_p1][value]" rows="3">{{ getVal($content,'about','p1') }}</textarea>
                    <input type="hidden" name="content[about_p1][section]" value="about">
                    <input type="hidden" name="content[about_p1][key]" value="p1">
                </div>
                <div class="form-group">
                    <label>About Text (paragraph 2)</label>
                    <textarea class="input" name="content[about_p2][value]" rows="3">{{ getVal($content,'about','p2') }}</textarea>
                    <input type="hidden" name="content[about_p2][section]" value="about">
                    <input type="hidden" name="content[about_p2][key]" value="p2">
                </div>
                <div class="form-group">
                    <label>About Text (paragraph 3)</label>
                    <textarea class="input" name="content[about_p3][value]" rows="3">{{ getVal($content,'about','p3') }}</textarea>
                    <input type="hidden" name="content[about_p3][section]" value="about">
                    <input type="hidden" name="content[about_p3][key]" value="p3">
                </div>
            </div>
        </section>

        <!-- PROJECTS (dynamic list with Add +) -->
        <section class="admin-section">
            <h2>Projects <button type="button" id="add-project" class="btn-primary" style="margin-left:1rem; padding:.2rem .6rem; font-weight:600;">+ Add Project</button></h2>

            @php
                // Determine existing project indexes from stored keys (e.g. title_1, desc_2, image_3)
                $projectIndexes = [];
                if(isset($content['projects'])){
                    foreach($content['projects'] as $item){
                        if(preg_match('/(\d+)$/', $item->key, $m)){
                            $projectIndexes[] = (int)$m[1];
                        }
                    }
                }
                $maxIndex = count($projectIndexes) ? max($projectIndexes) : 0;
                // Keep at least 4 initial blocks for parity with the old layout
                $initialCount = max(4, $maxIndex);
            @endphp

            <div id="projects-list">
                @for($i=1;$i<=$initialCount;$i++)
                    <div class="project-edit" data-project-index="{{ $i }}" style="margin-bottom:1rem; position:relative;">
                        <button type="button" class="remove-project" style="position:absolute; right:8px; top:8px;">Remove</button>
                        <h3 style="margin-top:0;">Project {{ $i }}</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Title</label>
                                <input class="input" type="text" name="content[project_{{ $i }}_title][value]" value="{{ getVal($content,'projects','title_'.$i) }}">
                                <input type="hidden" name="content[project_{{ $i }}_title][section]" value="projects">
                                <input type="hidden" name="content[project_{{ $i }}_title][key]" value="title_{{ $i }}">
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="input" name="content[project_{{ $i }}_desc][value]" rows="2">{{ getVal($content,'projects','desc_'.$i) }}</textarea>
                                <input type="hidden" name="content[project_{{ $i }}_desc][section]" value="projects">
                                <input type="hidden" name="content[project_{{ $i }}_desc][key]" value="desc_{{ $i }}">
                            </div>
                            <div class="form-group">
                                <label>Image (optional)</label>
                                @php $img = getVal($content,'projects','image_'.$i); @endphp
                                @if($img)
                                    <div style="margin-bottom:.5rem;">
                                        <img src="{{ Str::startsWith($img, 'http') ? $img : asset('storage/'.ltrim($img,'/')) }}" alt="project {{ $i }}" style="max-width:160px; border-radius:6px;" />
                                    </div>
                                @endif
                                <input class="input" type="file" name="content_files[project_{{ $i }}_image]" accept="image/*">
                                <input type="hidden" name="content_file_metas[project_{{ $i }}_image][section]" value="projects">
                                <input type="hidden" name="content_file_metas[project_{{ $i }}_image][key]" value="image_{{ $i }}">
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            {{-- Template for new projects; use __IDX__ placeholder replaced by JS --}}
            <template id="project-template">
                <div class="project-edit" data-project-index="__IDX__" style="margin-bottom:1rem; position:relative;">
                    <button type="button" class="remove-project" style="position:absolute; right:8px; top:8px;">Remove</button>
                    <h3 style="margin-top:0;">Project __IDX__</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Title</label>
                            <input class="input" type="text" name="content[project___IDX___title][value]" value="">
                            <input type="hidden" name="content[project___IDX___title][section]" value="projects">
                            <input type="hidden" name="content[project___IDX___title][key]" value="title___IDX__">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="input" name="content[project___IDX___desc][value]" rows="2"></textarea>
                            <input type="hidden" name="content[project___IDX___desc][section]" value="projects">
                            <input type="hidden" name="content[project___IDX___desc][key]" value="desc___IDX__">
                        </div>
                        <div class="form-group">
                            <label>Image (optional)</label>
                            <input class="input" type="file" name="content_files[project___IDX___image]" accept="image/*">
                            <input type="hidden" name="content_file_metas[project___IDX___image][section]" value="projects">
                            <input type="hidden" name="content_file_metas[project___IDX___image][key]" value="image___IDX__">
                        </div>
                    </div>
                </div>
            </template>

            <script>
                (function(){
                    // Start counter from the rendered initial count + 1
                    var nextIndex = {{ $initialCount }} + 1;
                    var list = document.getElementById('projects-list');
                    var tpl = document.getElementById('project-template').innerHTML;
                    document.getElementById('add-project').addEventListener('click', function(){
                        var html = tpl.replace(/__IDX__/g, nextIndex).replace(/___IDX___/g, nextIndex).replace(/___IDX__/g, nextIndex);
                        var wrapper = document.createElement('div');
                        wrapper.innerHTML = html;
                        // attach remove handler
                        var removeBtn = wrapper.querySelector('.remove-project');
                        if(removeBtn){ removeBtn.addEventListener('click', function(){ wrapper.remove(); }); }
                        list.appendChild(wrapper.firstElementChild);
                        nextIndex++;
                    });

                    // attach remove handlers to existing blocks
                    document.querySelectorAll('.project-edit .remove-project').forEach(function(btn){
                        btn.addEventListener('click', function(e){
                            var node = e.target.closest('.project-edit');
                            if(node) node.remove();
                        });
                    });
                })();
            </script>
        </section>

        <!-- CONTACT -->
        <section class="admin-section">
            <h2>Contact</h2>
            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <input class="input" type="email" name="content[contact_email][value]" value="{{ getVal($content,'contact','email') }}">
                    <input type="hidden" name="content[contact_email][section]" value="contact">
                    <input type="hidden" name="content[contact_email][key]" value="email">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input class="input" type="text" name="content[contact_phone][value]" value="{{ getVal($content,'contact','phone') }}">
                    <input type="hidden" name="content[contact_phone][section]" value="contact">
                    <input type="hidden" name="content[contact_phone][key]" value="phone">
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <input class="input" type="text" name="content[contact_location][value]" value="{{ getVal($content,'contact','location') }}">
                    <input type="hidden" name="content[contact_location][section]" value="contact">
                    <input type="hidden" name="content[contact_location][key]" value="location">
                </div>
                <div class="form-group">
                    <label>GitHub (full URL)</label>
                    <input class="input" type="url" name="content[contact_github][value]" value="{{ getVal($content,'contact','github') }}">
                    <input type="hidden" name="content[contact_github][section]" value="contact">
                    <input type="hidden" name="content[contact_github][key]" value="github">
                </div>
                <div class="form-group">
                    <label>LinkedIn (full URL)</label>
                    <input class="input" type="url" name="content[contact_linkedin][value]" value="{{ getVal($content,'contact','linkedin') }}">
                    <input type="hidden" name="content[contact_linkedin][section]" value="contact">
                    <input type="hidden" name="content[contact_linkedin][key]" value="linkedin">
                </div>
            </div>
        </section>

        <div class="admin-actions" style="padding:1rem 2rem;">
            <button type="submit" class="btn-primary admin-save">Save Profile</button>
        </div>
    </form>

    <script> if (typeof lucide !== 'undefined') try { lucide.createIcons(); } catch (e) {}</script>
</main>
</body>
</html>

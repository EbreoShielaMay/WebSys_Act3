<?php

namespace App\Http\Controllers;

use App\Models\PortfolioContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PortfolioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    public function index()
    {
        // Ensure default profile content exists in DB so the static text becomes editable.
        // This will only create missing entries and won't overwrite any existing user edits.
        $defaults = [
            'hero' => [
                'badge' => 'Available for freelance',
                'subtitle' => "Hello, I\'m",
                'name' => 'Shiela May Ebreo',
                    'profile_image' => '',
                'job_prefix' => 'Full Stack',
                'job' => 'Developer',
                'description' => 'Specializing in CSS, JavaScript, and PHP. I transform complex problems into elegant, pixel-perfect solutions that users love.'
            ],
            'about' => [
                'p1' => "I'm a passionate developer who believes that great code is like poetry – it should be elegant, efficient, and enjoyable to read.",
                'p2' => "My approach combines technical excellence with creative problem-solving. Whether it's building responsive interfaces, optimizing performance, or architecting scalable backends, I bring dedication and innovation to every project.",
                'p3' => "Beyond coding, I'm an advocate for clean code practices, continuous learning, and sharing knowledge with the developer community. Let's build something amazing together.",
                'projects' => '50+',
                'clients' => '30+',
                'years' => '5+',
                'quality' => '100%'
            ],
            'projects' => [
                'header' => 'Featured Projects',
                'description' => 'A selection of my recent work showcasing expertise in CSS, JavaScript, and PHP development.',
                'title_1' => 'E-Commerce Platform',
                'desc_1' => 'Full-featured shopping platform with payment integration, real-time inventory, and admin dashboard. Built for scalability and performance.',
                'title_2' => 'Analytics Dashboard',
                'desc_2' => 'Real-time analytics with interactive charts, data visualization, and export capabilities for business intelligence.',
                'title_3' => 'Developer Portfolio',
                'desc_3' => 'Modern portfolio with smooth animations, responsive design, and optimized performance showcasing projects and skills.',
                'title_4' => 'Mobile App Landing',
                'desc_4' => 'Sleek landing page with interactive elements, smooth scrolling, and conversion-optimized design for mobile app.'
            ],
            'contact' => [
                'email' => 'your.email@example.com',
                'phone' => '+1 (555) 123-4567',
                'location' => 'Your City, Country',
                'github' => '',
                'linkedin' => ''
            ]
        ];

        foreach ($defaults as $section => $pairs) {
            foreach ($pairs as $key => $value) {
                PortfolioContent::firstOrCreate([
                    'section' => $section,
                    'key' => $key,
                ], ['value' => $value]);
            }
        }

        $content = PortfolioContent::all()->groupBy('section');
        return view('profile', ['content' => $content->mapWithKeys(function($items, $section) {
            return [$section => $items->mapWithKeys(function($item) {
                return [$item->key => $item->value];
            })];
        })]);
    }

    public function edit()
    {
        $content = PortfolioContent::all()->groupBy('section');
        return view('portfolio.edit', ['content' => $content->mapWithKeys(function($items, $section) {
            return [$section => $items->mapWithKeys(function($item) {
                return [$item->key => $item->value];
            })];
        })]);
    }

    public function update(Request $request)
    {
        // Log incoming request for debugging (exclude large files)
        Log::info('Portfolio update request', array_filter($request->except(['_token', '_method', 'content_files'])));

        $validator = Validator::make($request->all(), [
            'content' => 'sometimes|array',
            'content.*.section' => 'required_with:content|string',
            'content.*.key' => 'required_with:content|string',
            'content.*.value' => 'nullable|string',
            'content_files' => 'sometimes|array',
            'content_files.*' => 'file|image|max:4096',
            'content_file_metas' => 'sometimes|array',
            'content_file_metas.*.section' => 'required_with:content_files|string',
            'content_file_metas.*.key' => 'required_with:content_files|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Save/Update textual content first
        if ($request->filled('content')) {
            foreach ($request->content as $item) {
                PortfolioContent::updateOrCreate(
                    [
                        'section' => $item['section'],
                        'key' => $item['key'],
                    ],
                    ['value' => $item['value'] ?? '']
                );
            }
        }

        // Process uploaded files (project images)
        $fileMetas = $request->input('content_file_metas', []);
        $files = $request->file('content_files', []);

        foreach ($files as $field => $uploaded) {
            if (!$uploaded) continue;

            // Expect a matching meta entry to know section/key
            if (!isset($fileMetas[$field])) continue;

            $meta = $fileMetas[$field];
            // Choose storage folder depending on the meta (profile image vs project images)
            $folder = 'projects';
            if (isset($meta['section']) && $meta['section'] === 'hero' && isset($meta['key']) && $meta['key'] === 'profile_image') {
                $folder = 'profiles';
            }

            // Store the file in the public disk
            $path = $uploaded->store($folder, 'public');

            PortfolioContent::updateOrCreate(
                [
                    'section' => $meta['section'],
                    'key' => $meta['key'],
                ],
                ['value' => $path]
            );
        }

        return redirect()->back()->with('success', 'Portfolio content updated successfully!');
    }
}
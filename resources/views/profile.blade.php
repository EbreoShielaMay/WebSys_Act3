<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Portfolio') }}</title>

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    @php
        $img = Vite::asset('resources/image/profile.png');

        // Ensure $content exists (controller provides it when routed through PortfolioController)
        if (!isset($content)) {
            $content = \App\Models\PortfolioContent::all()->groupBy('section')->mapWithKeys(function($items, $section) {
                return [$section => $items->mapWithKeys(function($item) {
                    return [$item->key => $item->value];
                })];
            });
        }

        function getVal($content, $section, $key, $fallback = '') {
            try {
                if (!isset($content[$section])) return $fallback;
                $sectionData = $content[$section];
                // support both Collection and array
                if ($sectionData instanceof \Illuminate\Support\Collection) {
                    return $sectionData->get($key, $fallback);
                }
                if (is_array($sectionData)) return $sectionData[$key] ?? $fallback;
                return $fallback;
            } catch (\Exception $e) {
                return $fallback;
            }
        }

        // Determine profile image source (DB value wins, otherwise use Vite asset)
        $profileImage = getVal($content, 'hero', 'profile_image', '');
        $imgSrc = $profileImage ? (Str::startsWith($profileImage, 'http') ? $profileImage : asset('storage/'.ltrim($profileImage,'/'))) : $img;
    @endphp

    <!-- Navigation -->
    <nav id="navbar">
        <div class="nav-container">
            <div class="nav-content">
                <!-- Logo -->
                <a href="#" class="nav-logo">
                    <span class="logo-text"><Dev/></span>
                    <div class="logo-underline"></div>
                </a>

                <!-- Desktop Navigation -->
                <div class="nav-links-desktop">
                    <a href="#" class="nav-link active" data-section="home">
                        <div class="nav-link-bg"></div>
                        <span class="nav-link-text">Home</span>
                    </a>
                    <a href="#about" class="nav-link" data-section="about">
                        <div class="nav-link-bg"></div>
                        <span class="nav-link-text">About</span>
                    </a>
                    <a href="#skills" class="nav-link" data-section="skills">
                        <div class="nav-link-bg"></div>
                        <span class="nav-link-text">Skills</span>
                    </a>
                    <a href="#projects" class="nav-link" data-section="projects">
                        <div class="nav-link-bg"></div>
                        <span class="nav-link-text">Projects</span>
                    </a>
                    <a href="#contact" class="nav-link" data-section="contact">
                        <div class="nav-link-bg"></div>
                        <span class="nav-link-text">Contact</span>
                    </a>
                </div>

                <!-- CTA Button -->
                <a href="#contact" class="nav-cta">
                    <span class="nav-cta-text">Hire Me</span>
                    <div class="nav-cta-bg"></div>
                </a>

                <!-- Mobile Menu Button -->
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i data-lucide="menu" class="menu-icon"></i>
                    <i data-lucide="x" class="close-icon" style="display: none;"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-content">
            <a href="#" class="mobile-nav-link active" data-section="home">Home</a>
            <a href="#about" class="mobile-nav-link" data-section="about">About</a>
            <a href="#skills" class="mobile-nav-link" data-section="skills">Skills</a>
            <a href="#projects" class="mobile-nav-link" data-section="projects">Projects</a>
            <a href="#contact" class="mobile-nav-link" data-section="contact">Contact</a>
            <a href="#contact" class="mobile-nav-cta">Hire Me</a>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <main>
        <!-- Hero Section -->
        <section class="hero-section" id="home">
            <!-- Morphing background blobs -->
            <div class="hero-bg-blobs">
                <div class="blob blob-1"></div>
                <div class="blob blob-2"></div>
                <div class="blob blob-3"></div>
            </div>

            <!-- Grid pattern overlay -->
            <div class="grid-pattern"></div>

            <div class="hero-container">
                <div class="hero-grid">
                    <!-- Left Content -->
                    <div class="hero-content">
                        <!-- Badge -->
                        <div class="hero-badge">
                            <i data-lucide="sparkles" class="badge-icon"></i>
                            <span>{{ getVal($content, 'hero', 'badge', 'Available for freelance') }}</span>
                        </div>

                        <!-- Title -->
                        <h1 class="hero-title">
                            <span class="hero-subtitle">{{ getVal($content, 'hero', 'subtitle', "Hello, I'm") }}</span>
                            <span class="hero-name">{{ getVal($content, 'hero', 'name', 'Your Name') }}</span>
                        </h1>

                        <!-- Job Title -->
                        <h2 class="hero-job-title">
                                <span class="highlight-text">{{ getVal($content, 'hero', 'job', 'Developer') }}</span>
                                <span class="highlight-underline"></span>
                            </span>
                        </h2>

                        <!-- Description -->
                        <p class="hero-description">
                            {{ getVal($content, 'hero', 'description', 'Specializing in CSS, JavaScript, and PHP. I transform complex problems into elegant, pixel-perfect solutions that users love.') }}
                        </p>

                        <!-- Buttons -->
                        <div class="hero-buttons">
                            <a href="#projects" class="btn-primary">
                                <span class="btn-text">View My Work</span>
                                <span class="btn-arrow">→</span>
                                <div class="btn-bg"></div>
                            </a>
                            <a href="#contact" class="btn-outline">Let's Talk</a>
                        </div>

                        <!-- Social Links -->
                        <div class="social-links">
                            @php
                                $github = getVal($content,'contact','github');
                                $linkedin = getVal($content,'contact','linkedin');
                                $email = getVal($content,'contact','email','your.email@example.com');
                            @endphp
                            @if($github)
                                <a href="{{ $github }}" target="_blank" rel="noopener noreferrer" class="social-link">
                                    <i data-lucide="github"></i>
                                </a>
                            @endif
                            @if($linkedin)
                                <a href="{{ $linkedin }}" target="_blank" rel="noopener noreferrer" class="social-link">
                                    <i data-lucide="linkedin"></i>
                                </a>
                            @endif
                            <a href="mailto:{{ $email }}" class="social-link">
                                <i data-lucide="mail"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Right Content - Profile Picture -->
                    <div class="hero-profile">
                        <div class="profile-container">
                            <!-- Rotating gradient border -->
                            <div class="profile-glow"></div>
                            
                            <!-- Profile Picture Container -->
                            <div class="profile-wrapper">
                                <!-- Morphing blob background -->
                                <div class="profile-blob"></div>

                                <!-- Main profile picture (use Vite asset helper) -->
                                <div class="profile-image">
                                    <img src="{{ $imgSrc }}" alt="Profile Picture">
                                    
                                    <!-- Gradient overlay -->
                                    <div class="profile-overlay"></div>
                                </div>

                                <!-- Floating decorative elements -->
                                <div class="float-element float-element-1">
                                    <i data-lucide="sparkles"></i>
                                </div>

                                <div class="float-element float-element-2">
                                    <span class="sparkle">🤍</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scroll indicator -->
                <div class="scroll-indicator">
                    <div class="scroll-content">
                        <span class="scroll-text">Scroll</span>
                        <i data-lucide="arrow-down" class="scroll-icon"></i>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="about-section">
            <!-- Diagonal background split -->
            <div class="diagonal-bg"></div>
            
            <div class="section-container">
                <div class="about-grid">
                    <!-- Left - Stats Cards -->
                    <div class="stats-grid">
                        <div class="stat-card" data-gradient="purple-fuchsia">
                            <div class="stat-glow"></div>
                            <div class="stat-content">
                                <div class="stat-icon">
                                    <i data-lucide="award"></i>
                                </div>
                                <div class="stat-number">{{ getVal($content,'about','projects','50+') }}</div>
                                <div class="stat-label">Projects</div>
                            </div>
                        </div>

                        <div class="stat-card" data-gradient="fuchsia-pink">
                            <div class="stat-glow"></div>
                            <div class="stat-content">
                                <div class="stat-icon">
                                    <i data-lucide="users"></i>
                                </div>
                                <div class="stat-number">{{ getVal($content,'about','clients','30+') }}</div>
                                <div class="stat-label">Clients</div>
                            </div>
                        </div>

                        <div class="stat-card" data-gradient="violet-purple">
                            <div class="stat-glow"></div>
                            <div class="stat-content">
                                <div class="stat-icon">
                                    <i data-lucide="code-2"></i>
                                </div>
                                <div class="stat-number">{{ getVal($content,'about','years','5+') }}</div>
                                <div class="stat-label">Years</div>
                            </div>
                        </div>

                        <div class="stat-card" data-gradient="purple-violet">
                            <div class="stat-glow"></div>
                            <div class="stat-content">
                                <div class="stat-icon">
                                    <i data-lucide="zap"></i>
                                </div>
                                <div class="stat-number">{{ getVal($content,'about','quality','100%') }}</div>
                                <div class="stat-label">Quality</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right - Content -->
                    <div class="about-content">
                        <!-- Badge -->
                        <span class="section-badge">About Me</span>

                        <!-- Title -->
                        <h2 class="section-title">
                            Crafting Digital <span class="gradient-text">
                                <span class="gradient-text-content">Experiences</span>
                                <div class="gradient-underline"></div>
                            </span>
                        </h2>

                        <!-- Text Content -->
                        <div class="about-text">
                            <p>{{ getVal($content,'about','p1', "I'm a passionate developer who believes that great code is like poetry – it should be elegant, efficient, and enjoyable to read.") }}</p>
                            <p>{{ getVal($content,'about','p2', 'My approach combines technical excellence with creative problem-solving. Whether it\'s building responsive interfaces, optimizing performance, or architecting scalable backends, I bring dedication and innovation to every project.') }}</p>
                            <p>{{ getVal($content,'about','p3', "Beyond coding, I'm an advocate for clean code practices, continuous learning, and sharing knowledge with the developer community. Let's build something amazing together.") }}</p>
                        </div>

                        <!-- Traits -->
                        <div class="traits">
                            <span class="trait">Problem Solver</span>
                            <span class="trait">Team Player</span>
                            <span class="trait">Quick Learner</span>
                            <span class="trait">Detail Oriented</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Projects Section -->
        <section id="projects" class="projects-section">
            <!-- Diagonal split background -->
            <div class="diagonal-bg-reverse"></div>
            
            <div class="section-container">
                <!-- Header -->
                <div class="section-header">
                    <span class="section-badge">Portfolio</span>
                    
                    <h2 class="section-title">
                        <span class="gradient-text-multicolor">{{ getVal($content,'projects','header','Featured Projects') }}</span>
                    </h2>
                    <p class="section-description">
                        {{ getVal($content,'projects','description','A selection of my recent work showcasing expertise in CSS, JavaScript, and PHP development.') }}
                    </p>
                </div>

                <!-- Projects Grid - Asymmetric Layout -->
                <div class="projects-grid">
                    <!-- Project 1 - Featured -->
                    <div class="project-card featured">
                        <div class="project-gradient-border"></div>
                        <div class="project-content-wrapper">
                            <!-- Image Container -->
                            <div class="project-image-wrapper">
                                <div class="project-image-container">
                                    <img src="https://images.unsplash.com/photo-1658297063569-162817482fb6?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080" 
                                         alt="E-Commerce Platform">
                                </div>
                                
                                <!-- Gradient overlay -->
                                <div class="project-overlay"></div>
                                
                                <!-- Hover overlay with actions -->
                                <div class="project-actions">
                                    <a href="#" class="project-action-btn">
                                        <i data-lucide="eye"></i>
                                    </a>
                                    <a href="#" class="project-action-btn">
                                        <i data-lucide="github"></i>
                                    </a>
                                    <a href="#" class="project-action-btn">
                                        <i data-lucide="external-link"></i>
                                    </a>
                                </div>

                                <!-- Featured badge -->
                                <div class="featured-badge">Featured</div>
                            </div>

                            <!-- Content -->
                            <div class="project-info">
                                <h3>{{ getVal($content,'projects','title_1','E-Commerce Platform') }}</h3>
                                <p>{{ getVal($content,'projects','desc_1','Full-featured shopping platform with payment integration, real-time inventory, and admin dashboard. Built for scalability and performance.') }}</p>

                                <!-- Tags -->
                                <div class="project-tags">
                                    <span class="tag">PHP</span>
                                    <span class="tag">JavaScript</span>
                                    <span class="tag">MySQL</span>
                                    <span class="tag">Stripe</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Project 2 -->
                    <div class="project-card">
                        <div class="project-gradient-border"></div>
                        <div class="project-content-wrapper">
                            <div class="project-image-wrapper">
                                <div class="project-image-container">
                                    <img src="https://images.unsplash.com/photo-1608222351212-18fe0ec7b13b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080" 
                                         alt="Analytics Dashboard">
                                </div>
                                <div class="project-overlay"></div>
                                <div class="project-actions">
                                    <a href="#" class="project-action-btn">
                                        <i data-lucide="eye"></i>
                                    </a>
                                    <a href="#" class="project-action-btn">
                                        <i data-lucide="github"></i>
                                    </a>
                                    <a href="#" class="project-action-btn">
                                        <i data-lucide="external-link"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="project-info">
                                <h3>{{ getVal($content,'projects','title_2','Analytics Dashboard') }}</h3>
                                <p>{{ getVal($content,'projects','desc_2','Real-time analytics with interactive charts, data visualization, and export capabilities for business intelligence.') }}</p>
                                <div class="project-tags">
                                    <span class="tag">React</span>
                                    <span class="tag">JavaScript</span>
                                    <span class="tag">CSS</span>
                                    <span class="tag">API</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Project 3 -->
                    <div class="project-card">
                        <div class="project-gradient-border"></div>
                        <div class="project-content-wrapper">
                            <div class="project-image-wrapper">
                                <div class="project-image-container">
                                    <img src="https://images.unsplash.com/photo-1593720213681-e9a8778330a7?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080" 
                                         alt="Developer Portfolio">
                                </div>
                                <div class="project-overlay"></div>
                                <div class="project-actions">
                                    <a href="#" class="project-action-btn">
                                        <i data-lucide="eye"></i>
                                    </a>
                                    <a href="#" class="project-action-btn">
                                        <i data-lucide="github"></i>
                                    </a>
                                    <a href="#" class="project-action-btn">
                                        <i data-lucide="external-link"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="project-info">
                                <h3>{{ getVal($content,'projects','title_3','Developer Portfolio') }}</h3>
                                <p>{{ getVal($content,'projects','desc_3','Modern portfolio with smooth animations, responsive design, and optimized performance showcasing projects and skills.') }}</p>
                                <div class="project-tags">
                                    <span class="tag">CSS</span>
                                    <span class="tag">JavaScript</span>
                                    <span class="tag">HTML</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Project 4 - Featured -->
                    <div class="project-card featured">
                        <div class="project-gradient-border"></div>
                        <div class="project-content-wrapper">
                            <div class="project-image-wrapper">
                                <div class="project-image-container">
                                    <img src="https://images.unsplash.com/photo-1609921212029-bb5a28e60960?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080" 
                                         alt="Mobile App Landing">
                                </div>
                                <div class="project-overlay"></div>
                                <div class="project-actions">
                                    <a href="#" class="project-action-btn">
                                        <i data-lucide="eye"></i>
                                    </a>
                                    <a href="#" class="project-action-btn">
                                        <i data-lucide="github"></i>
                                    </a>
                                    <a href="#" class="project-action-btn">
                                        <i data-lucide="external-link"></i>
                                    </a>
                                </div>
                                <div class="featured-badge">Featured</div>
                            </div>

                            <div class="project-info">
                                <h3>{{ getVal($content,'projects','title_4','Mobile App Landing') }}</h3>
                                <p>{{ getVal($content,'projects','desc_4','Sleek landing page with interactive elements, smooth scrolling, and conversion-optimized design for mobile app.') }}</p>
                                <div class="project-tags">
                                    <span class="tag">CSS</span>
                                    <span class="tag">JavaScript</span>
                                    <span class="tag">PHP</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="contact-section">
            <!-- Background effects -->
            <div class="contact-bg">
                <div class="bg-blob bg-blob-1"></div>
                <div class="bg-blob bg-blob-2"></div>
            </div>

            <div class="section-container">
                <!-- Header -->
                <div class="section-header">
 
                    <h2 class="section-title">
                        <span class="gradient-text-multicolor">Let's Work Together</span>
                    </h2>
                    <p class="section-description">
                        Have a project in mind? Let's create something amazing together. 
                        I'm always open to discussing new opportunities and collaborations.
                    </p>
                </div>

                <div class="contact-grid">
                    <!-- Contact Info Cards -->
                    <div class="contact-info">
                        <!-- Email Card -->
                        <div class="contact-info-card">
                            <div class="contact-card-glow"></div>
                            <div class="contact-card-content">
                                <div class="contact-card-icon icon-mail">
                                    <i data-lucide="mail"></i>
                                </div>
                                <div class="contact-card-text">
                                    <h4>Email</h4>
                                    <a href="mailto:your.email@example.com">your.email@example.com</a>
                                </div>
                            </div>
                        </div>

                        <!-- Phone Card -->
                        <div class="contact-info-card">
                            <div class="contact-card-glow"></div>
                            <div class="contact-card-content">
                                <div class="contact-card-icon icon-phone">
                                    <i data-lucide="phone"></i>
                                </div>
                                <div class="contact-card-text">
                                    <h4>Phone</h4>
                                    <a href="tel:+15551234567">+1 (555) 123-4567</a>
                                </div>
                            </div>
                        </div>

                        <!-- Location Card -->
                        <div class="contact-info-card">
                            <div class="contact-card-glow"></div>
                            <div class="contact-card-content">
                                <div class="contact-card-icon icon-location">
                                    <i data-lucide="map-pin"></i>
                                </div>
                                <div class="contact-card-text">
                                    <h4>Location</h4>
                                    <p>Your City, Country</p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Response Box -->
                        <div class="quick-response-box">
                            <h4>Quick Response</h4>
                            <p>
                                I typically respond within 24 hours. For urgent inquiries, 
                                feel free to call directly.
                            </p>
                            <div class="response-badges">
                                <span class="response-badge">Available</span>
                                <span class="response-badge">Fast Reply</span>
                                <span class="response-badge">Friendly</span>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="contact-form-wrapper">
                        <div class="form-gradient-border"></div>
                        
                        <div class="contact-form-container">
                            <form class="contact-form" id="contactForm">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="name">Your Name</label>
                                        <input type="text" id="name" name="name" placeholder="John Doe" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Your Email</label>
                                        <input type="email" id="email" name="email" placeholder="john@example.com" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="message">Your Message</label>
                                    <textarea id="message" name="message" rows="6" placeholder="Tell me about your project..." required></textarea>
                                </div>

                                <button type="submit" class="form-submit-btn">
                                    <div class="btn-spinner"></div>
                                    <i data-lucide="send" class="btn-icon"></i>
                                    <i data-lucide="check" class="btn-check" style="display: none;"></i>
                                    <span class="btn-text">Send Message</span>
                                    <span class="btn-text-success" style="display: none;">Message Sent!</span>
                                    <div class="btn-hover-bg"></div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-bg"></div>
            <div class="footer-blobs">
                <div class="footer-blob footer-blob-1"></div>
                <div class="footer-blob footer-blob-2"></div>
            </div>
            <div class="footer-container">
                <div class="footer-bottom">
                    <div class="footer-copyright">
                        <span>© {{ date('Y') }} Your Name. Crafted with</span>
                        <svg class="heart-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    <script> if (typeof lucide !== 'undefined') try { lucide.createIcons(); } catch (e) {}</script>
</body>
</html>

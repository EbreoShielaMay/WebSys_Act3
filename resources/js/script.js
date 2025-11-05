// ================================================
// PORTFOLIO JAVASCRIPT - Improved safety guards
// - Guards null elements to avoid runtime errors
// - Moves global DOM queries into init functions
// - Keeps behavior consistent with original design
// ================================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons if available
    if (typeof lucide !== 'undefined') {
        try { lucide.createIcons(); } catch (e) { /* ignore */ }
    }

    initializeNavigation();
    initializeSmoothScroll();
    initializeScrollAnimations();
    initializeContactForm();
    initializeBackToTop();
    initializeProjectCardHover();
    initializeNewsletterForm();
    scheduleIconReinit();
});

// NAVIGATION
function initializeNavigation() {
    const navbar = document.getElementById('navbar');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

    // If the navbar isn't present, nothing to do
    if (!navbar) return;

    const navLinks = document.querySelectorAll('.nav-link');
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

    // Scroll effect
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        if (scrollTop > 50) navbar.classList.add('scrolled'); else navbar.classList.remove('scrolled');
        updateActiveSectionOnScroll();
    });

    // Mobile menu toggle (guard elements)
    if (mobileMenuBtn && mobileMenu && mobileMenuOverlay) {
        const menuIcon = mobileMenuBtn.querySelector('.menu-icon');
        const closeIcon = mobileMenuBtn.querySelector('.close-icon');

        function toggleMobileMenu() {
            const isActive = mobileMenu.classList.contains('active');
            if (isActive) {
                mobileMenu.classList.remove('active');
                mobileMenuOverlay.classList.remove('active');
                if (menuIcon) menuIcon.style.display = 'block';
                if (closeIcon) closeIcon.style.display = 'none';
            } else {
                mobileMenu.classList.add('active');
                mobileMenuOverlay.classList.add('active');
                if (menuIcon) menuIcon.style.display = 'none';
                if (closeIcon) closeIcon.style.display = 'block';
            }
        }

        mobileMenuBtn.addEventListener('click', toggleMobileMenu);
        mobileMenuOverlay.addEventListener('click', toggleMobileMenu);

        mobileNavLinks.forEach(link => {
            link.addEventListener('click', function() {
                toggleMobileMenu();
                const section = (this.getAttribute('href') || '#').substring(1) || 'home';
                updateActiveSection(section);
            });
        });
    }

    // Desktop navigation links
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href') || '#';
            const section = href.substring(1) || 'home';
            updateActiveSection(section);
        });
    });

    function updateActiveSection(section) {
        navLinks.forEach(link => {
            link.classList.toggle('active', (link.getAttribute('href') || '#').substring(1) === section);
        });
        mobileNavLinks.forEach(link => {
            link.classList.toggle('active', (link.getAttribute('href') || '#').substring(1) === section);
        });
    }

    function updateActiveSectionOnScroll() {
        const sections = document.querySelectorAll('section[id]');
        const scrollY = window.pageYOffset;
        sections.forEach(section => {
            const sectionHeight = section.offsetHeight;
            const sectionTop = section.offsetTop - 150;
            const sectionId = section.getAttribute('id');
            if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) updateActiveSection(sectionId);
        });
        if (scrollY < 100) updateActiveSection('home');
    }

    // Close mobile menu on resize to desktop
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            const mobileMenuEl = document.getElementById('mobileMenu');
            if (window.innerWidth >= 768 && mobileMenuEl && mobileMenuEl.classList.contains('active')) {
                mobileMenuEl.classList.remove('active');
                const overlay = document.getElementById('mobileMenuOverlay');
                if (overlay) overlay.classList.remove('active');
                const menuBtn = document.getElementById('mobileMenuBtn');
                if (menuBtn) {
                    const mi = menuBtn.querySelector('.menu-icon');
                    const ci = menuBtn.querySelector('.close-icon');
                    if (mi) mi.style.display = 'block';
                    if (ci) ci.style.display = 'none';
                }
            }
        }, 250);
    });
}

// SMOOTH SCROLLING
function initializeSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href') || '#';
            if (href === '#') { e.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' }); return; }
            const target = document.querySelector(href);
            if (target) { e.preventDefault(); const offsetTop = target.offsetTop - 80; window.scrollTo({ top: offsetTop, behavior: 'smooth' }); }
        });
    });
}

// SCROLL ANIMATIONS
function initializeScrollAnimations() {
    const observerOptions = { root: null, rootMargin: '-100px', threshold: 0.1 };
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                if (entry.target.classList.contains('stats-grid')) animateStatsCards(entry.target);
                if (entry.target.classList.contains('skills-grid')) animateSkillsProgress();
                if (entry.target.classList.contains('traits')) animateTraits(entry.target);
            }
        });
    }, observerOptions);

    const observeSelectors = ['.about-content', '.stats-grid', '.skills-section', '.skills-grid', '.projects-section', '.contact-section', '.traits'];
    observeSelectors.forEach(selector => document.querySelectorAll(selector).forEach(el => observer.observe(el)));
}

// STATS CARDS ANIMATION
function animateStatsCards(container) {
    const cards = container.querySelectorAll('.stat-card');
    cards.forEach((card, index) => setTimeout(() => { card.style.opacity = '1'; card.style.transform = 'translateY(0) rotateX(0)'; }, index * 100));
}

// SKILLS CIRCULAR PROGRESS
let skillsAnimated = false;
function animateSkillsProgress() {
    if (skillsAnimated) return; skillsAnimated = true;
    const skillCards = document.querySelectorAll('.skill-card');
    skillCards.forEach((card, index) => {
        const circle = card.querySelector('.progress-ring-circle');
        if (!circle) return;
        const percentage = parseInt(circle.dataset.percentage || '0', 10);
        setTimeout(() => animateCircle(circle, percentage), index * 200 + 500);
    });
}

function animateCircle(circle, percentage) {
    const radius = 56; const circumference = 2 * Math.PI * radius;
    const duration = 1500; const frameDuration = 1000 / 60; const totalFrames = Math.round(duration / frameDuration);
    let frame = 0;
    const animation = setInterval(function() {
        frame++; const progress = frame / totalFrames; const easeProgress = 1 - Math.pow(1 - progress, 3);
        const currentProgress = percentage * easeProgress; const offset = circumference - (currentProgress / 100) * circumference;
        circle.style.strokeDasharray = `${circumference - offset} ${circumference}`;
        if (frame >= totalFrames) { clearInterval(animation); const finalOffset = circumference - (percentage / 100) * circumference; circle.style.strokeDasharray = `${circumference - finalOffset} ${circumference}`; }
    }, frameDuration);
}

// TRAITS ANIMATION
function animateTraits(container) {
    const traits = container.querySelectorAll('.trait');
    traits.forEach((trait, index) => setTimeout(() => { trait.style.opacity = '1'; trait.style.transform = 'scale(1)'; }, 900 + index * 100));
}

// PROJECT CARDS HOVER
function initializeProjectCardHover() {
    const projectCards = document.querySelectorAll('.project-card');
    projectCards.forEach(card => {
        card.addEventListener('mouseenter', function() { this.dataset.hover = 'true'; });
        card.addEventListener('mouseleave', function() { this.dataset.hover = 'false'; });
    });
}

// CONTACT FORM
function initializeContactForm() {
    const contactForm = document.getElementById('contactForm');
    if (!contactForm) return;
    const submitBtn = contactForm.querySelector('.form-submit-btn');
    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const nameEl = document.getElementById('name'); const emailEl = document.getElementById('email'); const messageEl = document.getElementById('message');
        const formData = { name: nameEl ? nameEl.value : '', email: emailEl ? emailEl.value : '', message: messageEl ? messageEl.value : '' };
        if (submitBtn) { submitBtn.classList.add('loading'); submitBtn.disabled = true; }
        await new Promise(resolve => setTimeout(resolve, 1500));
        if (submitBtn) { submitBtn.classList.remove('loading'); submitBtn.classList.add('success'); }
        contactForm.reset();
        setTimeout(() => { if (submitBtn) { submitBtn.classList.remove('success'); submitBtn.disabled = false; } if (typeof lucide !== 'undefined') try { lucide.createIcons(); } catch (e) {} }, 3000);
        if (typeof lucide !== 'undefined') try { lucide.createIcons(); } catch (e) {}
    });
}

// BACK TO TOP BUTTON
function initializeBackToTop() {
    const backToTopBtn = document.getElementById('backToTop'); if (!backToTopBtn) return;
    backToTopBtn.addEventListener('click', function() { window.scrollTo({ top: 0, behavior: 'smooth' }); });
}

// NEWSLETTER FORM
function initializeNewsletterForm() {
    const newsletterForm = document.querySelector('.newsletter-form');
    if (!newsletterForm) return;
    newsletterForm.addEventListener('submit', function(e) { e.preventDefault(); const input = this.querySelector('input[type="email"]'); if (input) { const email = input.value; alert('Thanks for subscribing! (This is a demo)'); this.reset(); } });
}

// ICON REINIT SCHEDULER (safe, bounded)
function scheduleIconReinit() {
    if (typeof lucide === 'undefined') return;
    let iconInitCount = 0; const maxRuns = 6; const interval = setInterval(() => { try { lucide.createIcons(); } catch (e) {} iconInitCount++; if (iconInitCount >= maxRuns) clearInterval(interval); }, 500);
    // debounce on scroll as well
    let scrollTimeout; window.addEventListener('scroll', function() { clearTimeout(scrollTimeout); scrollTimeout = setTimeout(function() { if (typeof lucide !== 'undefined') try { lucide.createIcons(); } catch (e) {} }, 500); });
}

// ============================================
// Load All Components on Page Load
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Load components into their respective sections
    loadComponent('top-header', 'topHeader');
    loadComponent('main-nav', 'navigation');
    loadComponent('hero-slider', 'heroSlider');
    loadComponent('updates-ticker', 'updatesTicker');
    loadComponent('left-sidebar', 'leftSidebar');
    loadComponent('glimpses-section', 'glimpses');
    loadComponent('achievements-section', 'achievements');
    loadComponent('principal-desk', 'principalDesk');
    loadComponent('video-gallery', 'videoGallery');
    loadComponent('important-links-section', 'importantLinks');
    loadComponent('right-sidebar', 'rightSidebar');
    loadComponent('main-footer', 'footer');
    
    // Initialize features after components are loaded
    setTimeout(function() {
        initializeCarousel();
        initializeScrollTop();
        initializeSmoothScroll();
        initializeNavigation();
    }, 100);
});

// ============================================
// Component Loader Function
// ============================================
function loadComponent(elementId, componentName) {
    const element = document.getElementById(elementId);
    if (element && window.components && window.components[componentName]) {
        element.innerHTML = window.components[componentName];
    }
}

// ============================================
// Initialize Carousel
// ============================================
function initializeCarousel() {
    const carousel = document.querySelector('#heroCarousel');
    if (carousel) {
        new bootstrap.Carousel(carousel, {
            interval: 5000,
            wrap: true,
            keyboard: true
        });
    }
}

// ============================================
// Scroll to Top Button
// ============================================
function initializeScrollTop() {
    // Create scroll to top button
    const scrollBtn = document.createElement('button');
    scrollBtn.className = 'scroll-top';
    scrollBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    document.body.appendChild(scrollBtn);
    
    // Show/hide button on scroll
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollBtn.classList.add('show');
        } else {
            scrollBtn.classList.remove('show');
        }
    });
    
    // Scroll to top on click
    scrollBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ============================================
// Smooth Scroll for Anchor Links
// ============================================
function initializeSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#!') {
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
}

// ============================================
// Active Navigation State
// ============================================
function initializeNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Sticky navigation on scroll
    const nav = document.querySelector('#main-nav');
    if (nav) {
        const navOffset = nav.offsetTop;
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset >= navOffset) {
                nav.classList.add('sticky');
            } else {
                nav.classList.remove('sticky');
            }
        });
    }
}

// ============================================
// Video Card Click Handler
// ============================================
document.addEventListener('click', function(e) {
    if (e.target.closest('.video-card')) {
        const videoCard = e.target.closest('.video-card');
        const title = videoCard.querySelector('.video-title').textContent;
        alert('Video: ' + title + '\n\nVideo playback functionality can be integrated here.');
    }
});

// ============================================
// Image Lightbox Effect
// ============================================
document.addEventListener('click', function(e) {
    if (e.target.closest('.glimpse-item img') || e.target.closest('.achievement-photo')) {
        const img = e.target;
        showLightbox(img.src, img.alt);
    }
});

function showLightbox(src, alt) {
    // Create lightbox overlay
    const lightbox = document.createElement('div');
    lightbox.className = 'lightbox';
    lightbox.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        cursor: pointer;
    `;
    
    const img = document.createElement('img');
    img.src = src;
    img.alt = alt;
    img.style.cssText = `
        max-width: 90%;
        max-height: 90%;
        border-radius: 5px;
        box-shadow: 0 0 30px rgba(255, 255, 255, 0.3);
    `;
    
    lightbox.appendChild(img);
    document.body.appendChild(lightbox);
    
    // Close on click
    lightbox.addEventListener('click', function() {
        document.body.removeChild(lightbox);
    });
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.body.contains(lightbox)) {
            document.body.removeChild(lightbox);
        }
    });
}

// ============================================
// Notification Item Click Handler
// ============================================
document.addEventListener('click', function(e) {
    if (e.target.closest('.notification-item')) {
        const notification = e.target.closest('.notification-item');
        const title = notification.querySelector('.notification-title').textContent.trim();
        alert('Notification: ' + title + '\n\nThis would link to the full notification/document.');
    }
});

// ============================================
// Download Link Click Handler
// ============================================
document.addEventListener('click', function(e) {
    if (e.target.closest('.downloads-list a')) {
        e.preventDefault();
        const link = e.target.closest('.downloads-list a');
        const filename = link.textContent.trim();
        alert('Downloading: ' + filename + '\n\nDownload functionality would be implemented here.');
    }
});

// ============================================
// Responsive Menu Toggle
// ============================================
function initializeResponsiveMenu() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function() {
            const navbarCollapse = document.querySelector('#navbarNav');
            if (navbarCollapse) {
                navbarCollapse.classList.toggle('show');
            }
        });
    }
}

// ============================================
// Print Page Function
// ============================================
function printPage() {
    window.print();
}

// ============================================
// Search Functionality (Placeholder)
// ============================================
function performSearch(query) {
    console.log('Searching for: ' + query);
    // Implement search functionality here
    alert('Search functionality: ' + query);
}

// ============================================
// Form Validation Helper
// ============================================
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// ============================================
// Console Log for Debugging
// ============================================
console.log('%c College Website Loaded Successfully! ', 
    'background: #003366; color: #fff; padding: 10px; font-size: 14px; font-weight: bold;');
console.log('%c Bhola Nath College, Dhubri (Autonomous) ', 
    'background: #ffc107; color: #003366; padding: 5px; font-size: 12px;');

// ============================================
// Performance Monitoring
// ============================================
window.addEventListener('load', function() {
    const loadTime = window.performance.timing.domContentLoadedEventEnd - 
                     window.performance.timing.navigationStart;
    console.log('Page loaded in: ' + loadTime + 'ms');
});

// ============================================
// Lazy Loading Images
// ============================================
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            }
        });
    });
    
    // Observe all images with data-src attribute
    setTimeout(function() {
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }, 500);
}

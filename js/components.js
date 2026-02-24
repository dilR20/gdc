/**
 * Components Loader
 * Dynamically loads modular components into the page
 */

document.addEventListener('DOMContentLoaded', function() {
    // Load all components in order
    loadComponent('top-header', 'components/header.php');
    loadComponent('main-nav', 'components/navigation.php');
    loadComponent('hero-slider', 'components/hero-slider.php');
    loadComponent('updates-ticker', 'components/updates-ticker.php');
    loadComponent('three-column-section', 'components/three-column-section.php');
    loadComponent('category-cards', 'components/category-cards.php');
    loadComponent('glimpses', 'components/glimpses.php');
    loadComponent('video-gallery', 'components/video-gallery.php');
    loadComponent('important-links', 'components/important-links.php');
    loadComponent('main-footer', 'components/footer.php');
});

/**
 * Load a component into a container
 */
function loadComponent(containerId, componentPath) {
    const container = document.getElementById(containerId);

    if (!container) {
        console.warn(`Container #${containerId} not found`);
        return;
    }

    // Show loading state
    container.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary"></div></div>';

    // Load component via AJAX
    fetch(componentPath)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(html => {
            container.innerHTML = html;

            // Trigger custom event after component loads
            const event = new CustomEvent('componentLoaded', {
                detail: { containerId, componentPath }
            });
            document.dispatchEvent(event);

            console.log(`✅ Loaded: ${componentPath}`);
        })
        .catch(error => {
            console.error(`❌ Error loading ${componentPath}:`, error);
            container.innerHTML = `
                <div class="alert alert-warning m-3">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Failed to load component: ${componentPath}
                </div>
            `;
        });
}

/**
 * Reload a specific component
 */
function reloadComponent(containerId, componentPath) {
    console.log(`🔄 Reloading: ${componentPath}`);
    loadComponent(containerId, componentPath);
}

/**
 * Listen for component loaded events
 */
document.addEventListener('componentLoaded', function(e) {
    console.log(`📦 Component loaded:`, e.detail);

    // Initialize Bootstrap carousel for hero slider
    if (e.detail.containerId === 'hero-slider') {
        const carouselElement = document.querySelector('#heroCarousel');
        if (carouselElement) {
            new bootstrap.Carousel(carouselElement, {
                interval: 5000,
                ride: 'carousel'
            });
        }
    }
});
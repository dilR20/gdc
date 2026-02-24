// ============================================
// Modal Component for Principal's Desk
// ============================================

// Principal's Desk Modal HTML
const principalModalHTML = `
<div id="principalModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h2>Principal's desk</h2>
            <button class="modal-close" onclick="closePrincipalModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>The College has been granted A+ Grade with CGPA of 3.42 by NAAC in the fourth cycle in 2023.The College is also recognized under 2F and 12B of UGC act of UGC. These could be possible with the hardwork done by College Staff and students under the guidance of visionary management. With proud legacy of 79 years, the College has excelled in various fields. The college has been conferred Autonomous by UGC on 10-07-2024.</p>
            
            <p>From this academic session, the College is going to start the Integrated Teacher Education Programme (ITEP) with BSc B.Ed. and BA B.Ed under NCTE.</p>
            
            <p>The green, secured campus with Wi-Fi connectivity, solar power plant, modern digital classrooms and laboratories, fully automated digital library, computer centre, indoor and outdoor game facilities etc. provide a fascinating learning atmosphere. We try our best to keep in touch with all the stakeholders in the all-round development of the college. We believe that the hard work, commitment, zeal and determination are the most important tool that shows the path of success.</p>
            
            <p>Many students have registered their presence in the merit list of Gauhati University and other reputed institutions. Students educated from the College have carved a niche for themselves in various fields at national and international levels.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-close-modal" onclick="closePrincipalModal()">Close</button>
        </div>
    </div>
</div>
`;

// Function to open Principal Modal
function openPrincipalModal() {
    // Check if modal already exists
    let modal = document.getElementById('principalModal');

    // If modal doesn't exist, create it
    if (!modal) {
        document.body.insertAdjacentHTML('beforeend', principalModalHTML);
        modal = document.getElementById('principalModal');
    }

    // Show modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling

    // Add animation
    setTimeout(() => {
        modal.classList.add('active');
    }, 10);
}

// Function to close Principal Modal
function closePrincipalModal() {
    const modal = document.getElementById('principalModal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = ''; // Restore scrolling
        }, 300);
    }
}

// ============================================
// YouTube Video Modal Component
// ============================================

// Function to extract YouTube Video ID from URL
function getYouTubeVideoId(url) {
    const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
    const match = url.match(regExp);
    return (match && match[7].length === 11) ? match[7] : null;
}

// Function to open YouTube Video Modal
function openVideoModal(videoUrl, title) {
    // Extract video ID
    const videoId = getYouTubeVideoId(videoUrl);

    if (!videoId) {
        alert('Invalid YouTube URL');
        return;
    }

    // Create or get modal
    let modal = document.getElementById('videoModal');

    if (!modal) {
        // Create modal HTML
        const videoModalHTML = `
        <div id="videoModal" class="modal-overlay video-modal-overlay">
            <div class="video-modal-container">
                <div class="video-modal-header">
                    <h2 id="videoModalTitle"></h2>
                    <button class="modal-close" onclick="closeVideoModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="video-modal-body">
                    <div class="video-wrapper">
                        <iframe id="youtubeIframe" 
                                src="" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
        `;

        document.body.insertAdjacentHTML('beforeend', videoModalHTML);
        modal = document.getElementById('videoModal');
    }

    // Update modal content
    const iframe = document.getElementById('youtubeIframe');
    const titleElement = document.getElementById('videoModalTitle');

    titleElement.textContent = title || 'Video';

    // Set iframe src with autoplay parameter
    iframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;

    // Show modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Add animation
    setTimeout(() => {
        modal.classList.add('active');
    }, 10);
}

// Function to close Video Modal
function closeVideoModal() {
    const modal = document.getElementById('videoModal');
    const iframe = document.getElementById('youtubeIframe');

    if (modal) {
        modal.classList.remove('active');

        // Stop video by clearing iframe src
        if (iframe) {
            iframe.src = '';
        }

        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const principalModal = document.getElementById('principalModal');
    const videoModal = document.getElementById('videoModal');

    if (principalModal && e.target === principalModal) {
        closePrincipalModal();
    }

    if (videoModal && e.target === videoModal) {
        closeVideoModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePrincipalModal();
        closeVideoModal();
    }
});
<!-- Footer -->
<footer class="footer bg-dark text-white pt-5 pb-3">
    <div class="container-fluid">
        <div class="row">
            <!-- Column 1: About -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-uppercase mb-3">About College</h5>
                <p class="small">
                    Gyanpeeth Degree College, Nikashi is a premier institution dedicated to providing 
                    quality education and fostering holistic development of students.
                </p>
                <div class="social-links mt-3">
                    <a href="#" class="btn btn-outline-light btn-sm me-2" target="_blank">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="btn btn-outline-light btn-sm me-2" target="_blank">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="#" class="btn btn-outline-light btn-sm me-2" target="_blank">
                        <i class="fab fa-google"></i>
                    </a>
                    <a href="#" class="btn btn-outline-light btn-sm" target="_blank">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
            </div>
            
            <!-- Column 2: Important Links -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-uppercase mb-3">Important Links</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="https://www.gauhati.ac.in" target="_blank"><i class="fas fa-chevron-right"></i> Gauhati University</a></li>
                    <li><a href="https://www.tezpur.ac.in" target="_blank"><i class="fas fa-chevron-right"></i> Tezpur University</a></li>
                    <li><a href="https://www.naac.gov.in" target="_blank"><i class="fas fa-chevron-right"></i> NAAC</a></li>
                    <li><a href="https://swayam.gov.in" target="_blank"><i class="fas fa-chevron-right"></i> Swayam</a></li>
                    <li><a href="https://dibru.ac.in" target="_blank"><i class="fas fa-chevron-right"></i> Dibrugarh University</a></li>
                    <li><a href="https://www.assamuniversity.ac.in" target="_blank"><i class="fas fa-chevron-right"></i> Assam University</a></li>
                    <li><a href="https://www.ugc.ac.in" target="_blank"><i class="fas fa-chevron-right"></i> UGC</a></li>
                    <li><a href="https://epathshala.nic.in" target="_blank"><i class="fas fa-chevron-right"></i> ePathshala</a></li>
                </ul>
            </div>
            
            <!-- Column 3: Downloads -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-uppercase mb-3">Downloads</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="downloads/prospectus-2025-26.pdf" target="_blank"><i class="fas fa-file-pdf"></i> Prospectus 2025-26</a></li>
                    <li><a href="downloads/faculty-appraisal.pdf" target="_blank"><i class="fas fa-file-pdf"></i> Performance appraisal of faculty member</a></li>
                    <li><a href="iqac.php"><i class="fas fa-certificate"></i> IQAC</a></li>
                    <li><a href="downloads/academic-calendar-2025-26.pdf" target="_blank"><i class="fas fa-calendar"></i> Academic Calendar 2025-26</a></li>
                    <li><a href="downloads/quest-2023-24.pdf" target="_blank"><i class="fas fa-book"></i> Quest 2023-24</a></li>
                    <li><a href="facilities.php"><i class="fas fa-building"></i> Divyangjan Facilities</a></li>
                    <li><a href="downloads/college-song.pdf" target="_blank"><i class="fas fa-music"></i> College Song</a></li>
                    <li><a href="syllabus.php"><i class="fas fa-file-alt"></i> Syllabus</a></li>
                </ul>
            </div>
            
            <!-- Column 4: Contact -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-uppercase mb-3">Contact Us</h5>
                <ul class="list-unstyled footer-contact">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <strong>Address:</strong><br>
                        Gyanpeeth Degree College<br>
                        Nikashi, Dhubri, Assam<br>
                        PIN: 783324
                    </li>
                    <li class="mt-3">
                        <i class="fas fa-phone"></i>
                        <strong>Phone:</strong><br>
                        +91-1234567890
                    </li>
                    <li class="mt-3">
                        <i class="fas fa-envelope"></i>
                        <strong>Email:</strong><br>
                        info@gdc-college.edu.in
                    </li>
                    <li class="mt-3">
                        <i class="fas fa-globe"></i>
                        <strong>Website:</strong><br>
                        www.gdc-college.edu.in
                    </li>
                </ul>
            </div>
        </div>
        
        <hr class="my-4 bg-secondary">
        
        <!-- Bottom Bar -->
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 small">
                    © <?php echo date('Y'); ?> Gyanpeeth Degree College, Nikashi. All Rights Reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0 small">
                    <a href="privacy-policy.php" class="text-white text-decoration-none">Privacy Statement</a> | 
                    <a href="terms-of-service.php" class="text-white text-decoration-none">Terms of Service</a> | 
                    <a href="refund-policy.php" class="text-white text-decoration-none">Refund Policy</a>
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
.footer {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
}

.footer h5 {
    border-bottom: 2px solid rgba(255,255,255,0.2);
    padding-bottom: 10px;
    font-weight: 600;
}

.footer-links li {
    margin-bottom: 10px;
}

.footer-links a {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s;
    display: inline-block;
}

.footer-links a:hover {
    color: #fff;
    padding-left: 5px;
}

.footer-links i {
    margin-right: 8px;
    color: #ffc107;
    font-size: 12px;
}

.footer-contact li {
    color: rgba(255,255,255,0.8);
    font-size: 14px;
    line-height: 1.6;
}

.footer-contact i {
    color: #ffc107;
    margin-right: 10px;
    width: 20px;
}

.social-links .btn {
    width: 36px;
    height: 36px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s;
}

.social-links .btn:hover {
    background: #fff;
    color: #1e3c72;
    transform: translateY(-3px);
}

hr.bg-secondary {
    opacity: 0.2;
}

@media (max-width: 768px) {
    .footer {
        text-align: center;
    }
    
    .footer-links a,
    .footer-contact li {
        text-align: left;
    }
}
</style>

<!-- Back to Top Button -->
<button id="backToTop" class="btn btn-primary" title="Go to top">
    <i class="fas fa-chevron-up"></i>
</button>

<style>
#backToTop {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 999;
    display: none;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    transition: all 0.3s;
}

#backToTop:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 8px rgba(0,0,0,0.3);
}
</style>

<script>
// Back to Top Button Functionality
window.addEventListener('scroll', function() {
    const backToTop = document.getElementById('backToTop');
    if (window.pageYOffset > 300) {
        backToTop.style.display = 'block';
    } else {
        backToTop.style.display = 'none';
    }
});

document.getElementById('backToTop').addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});
</script>


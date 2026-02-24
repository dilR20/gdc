<style>
.top-header {
    background: #1a3a5c;
    padding: 15px 0;
    color: white;
}
.top-header .container-fluid {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-left: 30px;
    padding-right: 30px;
}
.college-branding {
    display: flex;
    align-items: center;
    gap: 20px;
}
.college-logo {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 3px solid white;
}
.college-info h1 {
    font-size: 26px;
    font-weight: bold;
    margin: 0;
    line-height: 1.2;
}
.college-info .assamese-text {
    font-size: 22px;
    color: #ffc107;
    margin: 0;
}
.naac-info {
    text-align: right;
    display: flex;
    align-items: center;
    gap: 15px;
}
.naac-badge {
    width: 100px;
    height: 100px;
    border-radius: 8px;
}
.naac-details h3 {
    font-size: 18px;
    margin: 0 0 5px 0;
    font-weight: 600;
}
.naac-details p {
    margin: 3px 0;
    font-size: 13px;
    opacity: 0.9;
}
@media (max-width: 992px) {
    .college-info h1 { font-size: 20px; }
    .college-info .assamese-text { font-size: 18px; }
    .naac-badge { width: 70px; height: 70px; }
    .naac-details h3 { font-size: 15px; }
    .naac-details p { font-size: 11px; }
}
@media (max-width: 768px) {
    .top-header .container-fluid {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    .naac-info {
        justify-content: center;
    }
}
</style>

<div class="top-header">
    <div class="container-fluid">
        <div class="college-branding">
        
            <img src="images/logo.jpg" alt="College Logo" class="college-logo">
            <div class="college-info">
                <h1>Gyanpeeth Degree College</h1>
                <!-- <p class="assamese-text">ভোলানাথ মহাবিদ্যালয়, ধুবুরী(স্বায়ত্তশাসিত)</p> -->
            </div>
        </div>
        <div class="naac-info">
            <div class="naac-details">
                <h3>Accredited by NAAC</h3>
                <p>Grade A+ (Cycle 4) with CGPA 3.42</p>
                <p>AISHE ID: C-17146</p>
            </div>
            <img src="images/naac.png" alt="NAAC A+" class="naac-badge">
        </div>
    </div>
</div>
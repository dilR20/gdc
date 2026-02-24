<!-- Glimpses Section -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0"><i class="fas fa-images"></i> Glimpses</h4>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="glimpse-item">
                    <img src="assets/images/library.jpg" alt="College Library" class="img-fluid rounded" 
                         onerror="this.src='https://via.placeholder.com/400x300?text=College+Library'">
                    <div class="glimpse-overlay">
                        <h5>College Library</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="glimpse-item">
                    <img src="assets/images/biodiversity.jpg" alt="B.N. College Biodiversity" class="img-fluid rounded"
                         onerror="this.src='https://via.placeholder.com/400x300?text=Biodiversity'">
                    <div class="glimpse-overlay">
                        <h5>B.N. COLLEGE BIODIVERSITY</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.glimpse-item {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    cursor: pointer;
}
.glimpse-item img {
    transition: transform 0.3s;
    width: 100%;
    height: 250px;
    object-fit: cover;
}
.glimpse-item:hover img {
    transform: scale(1.1);
}
.glimpse-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.8));
    color: white;
    padding: 20px;
}
.glimpse-overlay h5 {
    margin: 0;
    font-size: 16px;
}
</style>
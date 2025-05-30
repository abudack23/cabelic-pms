<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <h1 class="mb-4 text-center"><i class="bi bi-building"></i> Our Accommodations</h1>
            
            <!-- Building 1 Card -->
            <div class="card shadow-sm mb-5">
                <div class="card-header bg-primary text-white">
                    <h2><i class="bi bi-building"></i> Building A (2-Storey)</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Ground Floor -->
                        <div class="col-md-6">
                            <div class="card mb-4 border-success">
                                <div class="card-header bg-success text-white">
                                    <h3 class="mb-0"><i class="bi bi-house-door"></i> Ground Floor</h3>
                                </div>
                                <div class="card-body">
                                    <h4>Standard Rooms (3 units)</h4>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <i class="bi bi-check-circle text-success"></i> 25-30 sqm
                                        </li>
                                        <li class="list-group-item">
                                            <i class="bi bi-check-circle text-success"></i> Private CR with shower
                                        </li>
                                        <li class="list-group-item">
                                            <i class="bi bi-check-circle text-success"></i> Basic kitchenette
                                        </li>
                                        <li class="list-group-item">
                                            <i class="bi bi-check-circle text-success"></i> 3-5
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Rate:</strong> ₱3,000/month
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Second Floor -->
                        <div class="col-md-6">
                            <div class="card mb-4 border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h3 class="mb-0"><i class="bi bi-house-up"></i> Second Floor</h3>
                                </div>
                                <div class="card-body">
                                    <h4>Studio-Type Rooms (5 units)</h4>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <i class="bi bi-check-circle text-success"></i> 20-25 sqm
                                        </li>
                                        <li class="list-group-item">
                                            <i class="bi bi-check-circle text-success"></i> Private CR with shower
                                        </li>
                                        <li class="list-group-item">
                                            <i class="bi bi-check-circle text-success"></i> Basic kitchenette
                                        </li>
                                        <li class="list-group-item">
                                            <i class="bi bi-check-circle text-success"></i> 1-2 persons
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Rate:</strong> ₱2,500/month
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Building 2 Card -->
            <div class="card shadow-sm mb-5">
                <div class="card-header bg-info text-white">
                    <h2><i class="bi bi-building"></i> Building B (1-Storey)</h2>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card border-info">
                                <div class="card-body">
                                    <h3 class="text-center"><i class="bi bi-house-heart"></i> Studio-Type Rooms (5 units)</h3>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <i class="bi bi-check-circle text-success"></i> 28-32 sqm
                                        </li>
                                        <li class="list-group-item">
                                            <i class="bi bi-check-circle text-success"></i> Modern private CR
                                        </li>
                                        <li class="list-group-item">
                                            <i class="bi bi-check-circle text-success"></i> Full kitchenette
                                        </li>
                                        <li class="list-group-item">
                                            <i class="bi bi-check-circle text-success"></i> 1-3 persons
                                        </li>
                                        <li class="list-group-item">
                                            <i class="bi bi-check-circle text-success"></i> Tiled Floor
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Rate:</strong> ₱3,000/month
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Additional Information -->
            <div class="alert alert-info">
                <h4><i class="bi bi-info-circle"></i> General Information</h4>
                <ul class="mb-0">
                    <li>All rates include water and basic utilities</li>
                    <li>Electricity is submetered and billed separately</li>
                    <li>1-month security deposit required</li>
                    <li>Minimum 2-month contract</li>
                </ul>
            </div>
            
            <!-- CTA Buttons -->
            <div class="text-center mt-4">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo $_SESSION['role'] === 'admin' ? 'admin/rooms/' : 'tenant/room-request.php'; ?>" 
                       class="btn btn-primary btn-lg mx-2">
                        <?php echo $_SESSION['role'] === 'admin' ? 'Manage Rooms' : 'Request Viewing'; ?>
                    </a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary btn-lg mx-2">Apply Now</a>
                <?php endif; ?>
                <a href="contact.php" class="btn btn-outline-primary btn-lg mx-2">
                    <i class="bi bi-question-circle"></i> Inquire Now
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
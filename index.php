<?php
session_start();

// Redirect logged-in users to their dashboard
if(isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'tenant/dashboard.php'));
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="hero-section bg-light py-5">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Added Logo Here -->
                <img src="assets/img/logo.png" alt="Cabelic Apartment Logo" class="mb-4" style="max-height: 120px;">
                
                <h1 class="display-4">Welcome to Cabelic Apartment</h1>
                <p class="lead">Your comfortable and affordable boarding house in Lucanin.</p>
                <hr class="my-4 mx-auto" style="width: 50%;">
                <p>Manage your property and tenant information efficiently with our system.</p>
                
                <!-- Removed the login/register buttons from here -->
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <div class="row g-4">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-people text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="card-title text-center">For Tenants</h5>
                    <p class="card-text text-center">View the rental information and submit inquiries.</p>
                    <div class="text-center mt-3">
                        <a href="login.php" class="btn btn-outline-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Tenant Portal
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-house text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="card-title text-center">Room Information</h5>
                    <p class="card-text text-center">Learn about our available rooms and pricing options.</p>
                    <div class="text-center mt-3">
                        <a href="rooms.php" class="btn btn-outline-primary">
                            <i class="bi bi-search"></i> View Rooms
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-telephone text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="card-title text-center">Contact Us</h5>
                    <p class="card-text text-center">Have questions? Reach out our management team.</p>
                    <div class="text-center mt-3">
                        <a href="contact.php" class="btn btn-outline-primary">
                            <i class="bi bi-info-circle"></i> Contact Information
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kept the "Ready to get started?" section as primary call-to-action -->
<div class="bg-light py-5 mt-0">
    <div class="container text-center">
        <h2 class="mb-4">Ready to get started?</h2>
        <p class="lead mb-4">Join our community of satisfied tenants today</p>
        <div class="d-flex justify-content-center flex-wrap gap-3">
            <a href="login.php" class="btn btn-primary btn-lg px-4">
                <i class="bi bi-box-arrow-in-right"></i> Login to Your Account
            </a>
            <a href="register.php" class="btn btn-success btn-lg px-4">
                <i class="bi bi-person-plus"></i> Create New Account
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
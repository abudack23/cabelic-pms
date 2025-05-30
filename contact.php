<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4 text-center"><i class="bi bi-telephone"></i> Contact Us</h1>
            
            <div class="card shadow-sm mb-5">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h3><i class="bi bi-geo-alt"></i> Location</h3>
                            <p class="lead"> Cabelic Apartment,<br>Barangay Lucanin,<br>Mariveles, Bataan</p>
                            
                            <h3 class="mt-4"><i class="bi bi-clock"></i> Office Hours</h3>
                            <ul class="list-unstyled">
                                <li>Monday-Friday: 8:00 AM - 5:00 PM</li>
                                <li>Saturday: 9:00 AM - 2:00 PM</li>
                                <li>Sunday: Closed</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <h3><i class="bi bi-envelope"></i> Contact Details</h3>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <strong>Management:</strong><br>
                                    <i class="bi bi-telephone"></i> (02) 8123-4567<br>
                                    <i class="bi bi-phone"></i> 0917 123 4567
                                </li>
                                <li class="mb-2">
                                    <strong>Maintenance:</strong><br>
                                    <i class="bi bi-phone"></i> 0918 987 6543
                                </li>
                                <li>
                                    <strong>Email:</strong><br>
                                    <i class="bi bi-envelope"></i> info@cabelicapartment.com
                                </li>
                            </ul>
                            
                            <h3 class="mt-4"><i class="bi bi-chat-square-text"></i> Emergency</h3>
                            <p>For after-hours emergencies:<br>
                            <i class="bi bi-phone"></i> 09777031409</p>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h3><i class="bi bi-send"></i> Send Us a Message</h3>
                    <form class="mt-3">
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <select class="form-select" id="subject">
                                <option>General Inquiry</option>
                                <option>Room Availability</option>
                                <option>Maintenance Request</option>
                                <option>Billing Question</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
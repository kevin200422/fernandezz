<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center text-primary">Register as a Professional</h1>
        <p class="text-center">Join our community of doctors and help provide well-being.</p>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="process_registration.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="role" value="doctor">
                    
                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Specialty</label>
                        <input type="text" name="specialty" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="address" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Professional Certificate (PDF)</label>
                        <input type="file" name="certificate" class="form-control" accept="application/pdf" required>
                    </div>
                    <div class="mb-3">
                        <label>Profile Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register Doctor</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

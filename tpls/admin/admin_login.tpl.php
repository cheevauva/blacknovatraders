<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-header bg-dark text-white text-center">
                    <h4 class="mb-0">Administration Login</h4>
                </div>
                <div class="card-body">
                    <form action="admin.php" method="POST">
                        <div class="mb-3">
                            <label for="swordfish" class="form-label">
                                <i class="bi bi-key me-2"></i>Password
                            </label>
                            <input type="password" 
                                   class="form-control form-control-lg" 
                                   id="swordfish" 
                                   name="swordfish" 
                                   size="20" 
                                   maxlength="20"
                                   placeholder="Enter administrator password"
                                   required>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary me-md-2">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Optional: Include Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
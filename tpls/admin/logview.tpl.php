
<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0">
            <i class="bi bi-journal-text me-2"></i>Log Viewer
        </h3>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <!-- Admin Log Form -->
            <div class="col-md-6">
                <div class="card h-100 border-primary">
                    <div class="card-body text-center">
                        <h5 class="card-title text-primary mb-3">
                            <i class="bi bi-shield-lock me-2"></i>Administrator Logs
                        </h5>
                        <p class="card-text text-muted mb-4">
                            View system administrator activity logs
                        </p>
                        <form action="log.php" method="POST" class="d-inline-block">
                            <input type="hidden" name="swordfish" value="<?php echo isset($swordfish) ? htmlspecialchars($swordfish, ENT_QUOTES) : ''; ?>">
                            <input type="hidden" name="player" value="0">
                            <button type="submit" class="btn btn-outline-primary btn-lg px-4">
                                <i class="bi bi-eye me-2"></i>View Admin Log
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Player Log Form -->
            <div class="col-md-6">
                <div class="card h-100 border-success">
                    <div class="card-body">
                        <h5 class="card-title text-success mb-3">
                            <i class="bi bi-person-lines-fill me-2"></i>Player Logs
                        </h5>
                        <p class="card-text text-muted mb-3">
                            Select a player to view their activity logs
                        </p>
                        <form action="log.php" method="POST">
                            <input type="hidden" name="swordfish" value="<?php echo isset($swordfish) ? htmlspecialchars($swordfish, ENT_QUOTES) : ''; ?>">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-8">
                                    <select class="form-select" name="player">
                                        <?php echo options($ships, null); ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-search me-1"></i>View Log
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <hr class="my-4">

        <!-- Additional Information -->
        <div class="alert alert-info mt-3">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Note:</strong> Logs contain detailed records of administrator actions and player activities for auditing purposes.
        </div>
    </div>
</div>

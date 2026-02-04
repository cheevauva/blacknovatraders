<div class="card shadow">
    <div class="card-header bg-danger text-white">
        <h4 class="mb-0">
            <i class="bi bi-globe-americas me-2"></i>Universe Editor
        </h4>
    </div>
    <div class="card-body">
        <h5 class="text-danger mb-3">
            <i class="bi bi-exclamation-triangle me-2"></i>Expand/Contract the Universe
        </h5>

        <p class="lead mb-4">
            <i class="bi bi-info-circle me-2"></i>Expand or Contract the Universe
        </p>

        <form action="admin.php" method="POST">
            <div class="row align-items-center mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        <i class="bi bi-arrows-angle-expand me-2"></i>Universe Size:
                    </label>
                </div>
                <div class="col-md-6">
                    <input type="number" 
                           class="form-control form-control-lg" 
                           name="radius" 
                           value="<?php echo isset($universe_size) ? htmlspecialchars($universe_size, ENT_QUOTES) : ''; ?>"
                           min="1"
                           required>
                </div>
            </div>

            <input type="hidden" name="swordfish" value="<?php echo isset($swordfish) ? htmlspecialchars($swordfish, ENT_QUOTES) : ''; ?>">
            <input type="hidden" name="menu" value="univedit">
            <input type="hidden" name="action" value="doexpand">

            <div class="text-center">
                <button type="submit" class="btn btn-danger btn-lg px-5">
                    <i class="bi bi-stars me-2"></i>Play God
                </button>
            </div>
        </form>
    </div>
    <div class="card-footer bg-light">
        <small class="text-muted">
            <i class="bi bi-exclamation-diamond me-1"></i>
            Warning: This action will modify the entire game universe structure.
        </small>
    </div>
</div>

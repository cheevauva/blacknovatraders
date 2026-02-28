<?php include_header(); ?>
<?php $self = \BNT\Controller\AdminSectorController::as($self); ?>
<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0">
            Sector Editor
        </h3>
    </div>
    <div class="card-body">
        <div class="alert alert-warning mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Note:</strong> Cannot Edit Sector 0
        </div>

        <form action="<?= route('admin'); ?>" method="GET">
            <div class="row align-items-start">
                <div class="col-md-8 mb-3">
                    <label class="form-label fw-bold mb-2">Select Sector:</label>
                    <select size="10" class="form-select h-100" name="sector">
                        <?php echo options($self->sectors, null); ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3 d-flex flex-column align-items-center">
                    <div class="mt-4 pt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            Edit Sector
                        </button>
                    </div>
                </div>
            </div>

            <input type="hidden" name="module" value="sector">
            <input type="hidden" name="operation" value="edit">
        </form>
    </div>
    <div class="card-footer bg-light">
        <small class="text-muted">
            Select a sector from the list to edit its properties.
        </small>
    </div>
</div>
<?php include_footer(); ?>

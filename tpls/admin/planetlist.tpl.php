<?php include_header(); ?>
<?php $self = \BNT\Controller\AdminPlanetController::as($self); ?>
<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0">
            Planet Editor
        </h3>
    </div>
    <div class="card-body">
        <form action="admin.php" method="GET">
            <div class="row align-items-start">
                <div class="col-md-8 mb-3">
                    <label class="form-label fw-bold mb-2">Select Planet:</label>
                    <select size="10" class="form-select h-100" name="planet">
                        <?php echo options($self->planets, null); ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3 d-flex flex-column align-items-center">
                    <div class="mt-4 pt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            Edit Planet
                        </button>
                    </div>
                </div>
            </div>

            <input type="hidden" name="module" value="planet">
            <input type="hidden" name="operation" value="edit">
        </form>
    </div>
    <div class="card-footer bg-light">
        <small class="text-muted">
            <i class="bi bi-info-circle me-1"></i>
            Select a planet from the list to edit its properties.
        </small>
    </div>
</div>
<?php include_footer(); ?>
<?php $self = BNT\Controller\AdminShipController::as($self); ?>
<?php include_header(); ?>
<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0">
            User Editor
        </h3>
    </div>
    <div class="card-body">
        <form action="<?= route('admin'); ?>" method="GET">
            <div class="row align-items-start">
                <div class="col-md-8 mb-3">
                    <label class="form-label fw-bold mb-2">Select User:</label>
                    <select size="10" class="form-select h-100" name="ship">
                        <?php echo options($self->ships, null); ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3 d-flex flex-column align-items-center">
                    <div class="mt-4 pt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            Edit User
                        </button>
                    </div>
                </div>
            </div>

            <input type="hidden" name="module" value="ship">
            <input type="hidden" name="operation" value="edit">
        </form>
    </div>
    <div class="card-footer bg-light">
        <small class="text-muted">
            Select a user from the list and click "Edit User" to modify their settings.
        </small>
    </div>
</div>
<?php include_footer(); ?>

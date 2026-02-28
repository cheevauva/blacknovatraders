<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0">
            Log Viewer
        </h3>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-12">
                <div class="card h-100">
                    <div class="card-body">
                        <form action="<?= route('log');?>" method="GET">
                            <div class="row align-items-start">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label fw-bold mb-2">Select User:</label>
                                    <select size="10" class="form-select h-100" name="player">
                                        <?php echo options($ships, null); ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 d-flex flex-column align-items-center">
                                    <div class="mt-4 pt-4">
                                        <button type="submit" class="btn btn-primary btn-lg px-4">
                                            View Log
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-light">
        <small class="text-muted">
            Logs contain detailed records of administrator actions and player activities for auditing purposes.
        </small>
    </div>
</div>

<?php include_header(); ?>
<?php $self = \BNT\Controller\AdminSectorController::as($self); ?>
<?php $sector = $self->sector; ?>
<div class="container-fluid mt-4">
    <h2 class="mb-4">
        Sector Editor
    </h2>

    <form action="<?= route('admin', 'module=sector&operation=save&sector=' . $sector['sector_id']); ?>" method="POST" id="bntSectoreditForm">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Sector Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Sector ID</span>
                        </label>
                        <div class="form-control bg-light">
                            <span class="text-success"><?= $sector['sector_id']; ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Sector Name</span>
                        </label>
                        <input type="text" class="form-control" name="sector_name" size="15" 
                               value="<?= htmlspecialchars($sector['sector_name']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Zone ID</span>
                        </label>
                        <select class="form-select" size="1" name="zone_id">
                            <?= options($self->zones, isset($sector['zone_id']) ? $sector['zone_id'] : null); ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">
                            <span class="font-monospace">Beacon</span>
                        </label>
                        <input type="text" class="form-control" name="beacon" size="70" value="<?= htmlspecialchars($sector['beacon']); ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">
                            <span class="font-monospace">Distance</span>
                        </label>
                        <input type="number" class="form-control" name="distance" size="9" value="<?= intval($sector['distance']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            <span class="font-monospace">Angle1</span>
                        </label>
                        <input type="number" class="form-control" name="angle1" size="9" value="<?= floatval($sector['angle1']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            <span class="font-monospace">Angle2</span>
                        </label>
                        <input type="number" class="form-control" name="angle2" size="9" value="<?= floatval($sector['angle2']); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Port Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Port Type</span>
                        </label>
                        <select class="form-select" size="1" name="port_type">
                            <?php
                            echo options([
                                'none' => 'none',
                                'organics' => 'organics',
                                'ore' => 'ore',
                                'goods' => 'goods',
                                'energy' => 'energy',
                            ], $sector['port_type']);
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Organics</span>
                        </label>
                        <input type="number" class="form-control" name="port_organics" size="9" value="<?= intval($sector['port_organics']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Ore</span>
                        </label>
                        <input type="number" class="form-control" name="port_ore" size="9" value="<?= intval($sector['port_ore']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Goods</span>
                        </label>
                        <input type="number" class="form-control" name="port_goods" size="9" value="<?= intval($sector['port_goods']); ?>">
                    </div>
                    <div class="col-md-3 mt-3">
                        <label class="form-label">
                            <span class="font-monospace">Energy</span>
                        </label>
                        <input type="number" class="form-control" name="port_energy" size="9" value="<?= intval($sector['port_energy']); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg px-5">
                Save Sector
            </button>
        </div>
    </form>
    <script type="text/javascript">
        bntForm('bntSectoreditForm');
    </script>
</div>
<?php include_footer(); ?>

<?php $self = BNT\Controller\AdminShipController::as($self); ?>
<?php $ship = $self->ship; ?>
<?php include_header(); ?>
<div class="container-fluid">
    <h2 class="mb-4">Ship Editor</h2>

    <form action="admin.php?module=ship&operation=save&ship=<?= $ship['ship_id']; ?>" id="bntUsereditForm" method="POST">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">ID</label>
                        <div class="form-control bg-light">
                            <?php echo $ship['ship_id']; ?>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Ship</label>
                        <input type="text" class="form-control" name="ship_name" value="<?php echo htmlspecialchars($ship['ship_name']); ?>">
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="ship_destroyed" value="ON" <?php echo CHECKED($ship['ship_destroyed']); ?>>
                            <label class="form-check-label fw-bold">Destroyed?</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Ship Levels</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Hull</label>
                        <input type="number" class="form-control" name="hull" size="5" value="<?php echo intval($ship['hull']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Engines</label>
                        <input type="number" class="form-control" name="engines" size="5" value="<?php echo intval($ship['engines']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Power</label>
                        <input type="number" class="form-control" name="power" size="5" value="<?php echo intval($ship['power']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Computer</label>
                        <input type="number" class="form-control" name="computer" size="5" value="<?php echo intval($ship['computer']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Sensors</label>
                        <input type="number" class="form-control" name="sensors" size="5" value="<?php echo intval($ship['sensors']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Armour</label>
                        <input type="number" class="form-control" name="armor" size="5" value="<?php echo intval($ship['armor']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Shields</label>
                        <input type="number" class="form-control" name="shields" size="5" value="<?php echo intval($ship['shields']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Beams</label>
                        <input type="number" class="form-control" name="beams" size="5" value="<?php echo intval($ship['beams']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Torpedoes</label>
                        <input type="number" class="form-control" name="torp_launchers" size="5" value="<?php echo intval($ship['torp_launchers']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Cloak</label>
                        <input type="number" class="form-control" name="cloak" size="5" value="<?php echo intval($ship['cloak']); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Ship Holds</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Ore</label>
                        <input type="number" class="form-control" name="ship_ore" size="8" value="<?php echo intval($ship['ship_ore']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Organics</label>
                        <input type="number" class="form-control" name="ship_organics" size="8" value="<?php echo intval($ship['ship_organics']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Goods</label>
                        <input type="number" class="form-control" name="ship_goods" size="8" value="<?php echo intval($ship['ship_goods']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Energy</label>
                        <input type="number" class="form-control" name="ship_energy" size="8" value="<?php echo intval($ship['ship_energy']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Colonists</label>
                        <input type="number" class="form-control" name="ship_colonists" size="8" value="<?php echo intval($ship['ship_colonists']); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Combat</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fighters</label>
                        <input type="number" class="form-control" name="ship_fighters" size="8" value="<?php echo intval($ship['ship_fighters']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Torpedoes</label>
                        <input type="number" class="form-control" name="torps" size="8" value="<?php echo intval($ship['torps']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Armour Pts</label>
                        <input type="number" class="form-control" name="armor_pts" size="8" value="<?php echo intval($ship['armor_pts']); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Devices</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Beacons</label>
                        <input type="number" class="form-control" name="dev_beacon" size="5" value="<?php echo intval($ship['dev_beacon']); ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Warp Editors</label>
                        <input type="number" class="form-control" name="dev_warpedit" size="5" value="<?php echo intval($ship['dev_warpedit']); ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Genesis Torpedoes</label>
                        <input type="number" class="form-control" name="dev_genesis" size="5" value="<?php echo intval($ship['dev_genesis']); ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Mine Deflectors</label>
                        <input type="number" class="form-control" name="dev_minedeflector" size="5" value="<?php echo intval($ship['dev_minedeflector']) ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Emergency Warp</label>
                        <input type="number" class="form-control" name="dev_emerwarp" size="5" value="<?php echo intval($ship['dev_emerwarp']); ?>">
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="dev_escapepod" value="ON"  <?php echo CHECKED($ship['dev_escapepod']); ?>>
                            <label class="form-check-label">Escape Pod</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="dev_fuelscoop" value="ON" <?php echo CHECKED($ship['dev_fuelscoop']); ?>>
                            <label class="form-check-label">FuelScoop</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Resources & Location</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Credits</label>
                        <input type="number" class="form-control" name="credits" value="<?php echo intval($ship['credits']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Turns</label>
                        <input type="number" class="form-control" name="turns" value="<?php echo intval($ship['turns']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Current sector</label>
                        <input type="number" class="form-control" name="sector" value="<?php echo intval($ship['sector']); ?>">
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-save me-2"></i>Save User
        </button>
    </form>
    <script type="text/javascript">
        bntForm('bntUsereditForm');
    </script>
</div>
<?php include_footer(); ?>

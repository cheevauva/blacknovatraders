<?php $title = "Administration"; ?>
<?php include 'header.php'; ?>
<div class="container-fluid mt-4">
    <h2 class="mb-4">
        Sector Editor
    </h2>

    <form action="admin.php?module=sectoredit&sector=<?php echo $sector; ?>&operation=save" method="POST" id="bntSectoreditForm">
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
                            <span class="text-success"><?php echo $sector; ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Sector Name</span>
                        </label>
                        <input type="text" class="form-control" name="sector_name" size="15" 
                               value="<?php echo htmlspecialchars($row['sector_name']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Zone ID</span>
                        </label>
                        <select class="form-select" size="1" name="zone_id">
                            <?php echo options($zones, isset($row['zone_id']) ? $row['zone_id'] : null); ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">
                            <span class="font-monospace">Beacon</span>
                        </label>
                        <input type="text" class="form-control" name="beacon" size="70" value="<?php echo htmlspecialchars($row['beacon']); ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">
                            <span class="font-monospace">Distance</span>
                        </label>
                        <input type="number" class="form-control" name="distance" size="9" value="<?php echo intval($row['distance']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            <span class="font-monospace">Angle1</span>
                        </label>
                        <input type="number" class="form-control" name="angle1" size="9" value="<?php echo floatval($row['angle1']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            <span class="font-monospace">Angle2</span>
                        </label>
                        <input type="number" class="form-control" name="angle2" size="9" value="<?php echo floatval($row['angle2']); ?>">
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
                            ], $row['port_type']);
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Organics</span>
                        </label>
                        <input type="number" class="form-control" name="port_organics" size="9" value="<?php echo intval($row['port_organics']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Ore</span>
                        </label>
                        <input type="number" class="form-control" name="port_ore" size="9" value="<?php echo intval($row['port_ore']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Goods</span>
                        </label>
                        <input type="number" class="form-control" name="port_goods" size="9" value="<?php echo intval($row['port_goods']); ?>">
                    </div>
                    <div class="col-md-3 mt-3">
                        <label class="form-label">
                            <span class="font-monospace">Energy</span>
                        </label>
                        <input type="number" class="form-control" name="port_energy" size="9" value="<?php echo intval($row['port_energy']); ?>">
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
<?php include 'footer.php'; ?>

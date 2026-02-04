<div class="container-fluid mt-4">
    <h2 class="mb-4">
        <i class="bi bi-globe me-2"></i>Planet Editor
    </h2>
    
    <form action="admin.php" method="POST">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Basic Planet Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Planet ID</span>
                        </label>
                        <div class="form-control bg-light">
                            <span class="text-success"><?php echo isset($planet) ? htmlspecialchars($planet, ENT_QUOTES) : ''; ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Sector ID</span>
                        </label>
                        <input type="number" class="form-control" name="sector_id" size="5" 
                               value="<?php echo isset($row['sector_id']) ? htmlspecialchars($row['sector_id'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="defeated" value="ON" 
                                   <?php echo CHECKED($row['defeated']); ?>>
                            <label class="form-check-label font-monospace">Defeated</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Planet Name</span>
                        </label>
                        <input type="text" class="form-control" name="name" size="15" 
                               value="<?php echo isset($row['name']) ? htmlspecialchars($row['name'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="base" value="ON" 
                                   <?php echo CHECKED($row['base']); ?>>
                            <label class="form-check-label font-monospace">Base</label>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="sells" value="ON" 
                                   <?php echo CHECKED($row['sells']); ?>>
                            <label class="form-check-label font-monospace">Sells</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Resources & Ownership</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Planet Owner</span>
                        </label>
                        <select class="form-select" size="1" name="owner">
                            <?php echo options($owners, isset($row['owner']) ? $row['owner'] : null); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Organics</span>
                        </label>
                        <input type="number" class="form-control" name="organics" size="9" 
                               value="<?php echo isset($row['organics']) ? htmlspecialchars($row['organics'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Ore</span>
                        </label>
                        <input type="number" class="form-control" name="ore" size="9" 
                               value="<?php echo isset($row['ore']) ? htmlspecialchars($row['ore'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Goods</span>
                        </label>
                        <input type="number" class="form-control" name="goods" size="9" 
                               value="<?php echo isset($row['goods']) ? htmlspecialchars($row['goods'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Energy</span>
                        </label>
                        <input type="number" class="form-control" name="energy" size="9" 
                               value="<?php echo isset($row['energy']) ? htmlspecialchars($row['energy'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Planet Corp</span>
                        </label>
                        <input type="text" class="form-control" name="corp" size="5" 
                               value="<?php echo isset($row['corp']) ? htmlspecialchars($row['corp'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Colonists</span>
                        </label>
                        <input type="number" class="form-control" name="colonists" size="9" 
                               value="<?php echo isset($row['colonists']) ? htmlspecialchars($row['colonists'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Credits</span>
                        </label>
                        <input type="number" class="form-control" name="credits" size="9" 
                               value="<?php echo isset($row['credits']) ? htmlspecialchars($row['credits'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Fighters</span>
                        </label>
                        <input type="number" class="form-control" name="fighters" size="9" 
                               value="<?php echo isset($row['fighters']) ? htmlspecialchars($row['fighters'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Torpedoes</span>
                        </label>
                        <input type="number" class="form-control" name="torps" size="9" 
                               value="<?php echo isset($row['torps']) ? htmlspecialchars($row['torps'], ENT_QUOTES) : ''; ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Planet Production</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Organics</span>
                        </label>
                        <input type="number" class="form-control" name="prod_organics" size="9" 
                               value="<?php echo isset($row['prod_organics']) ? htmlspecialchars($row['prod_organics'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Ore</span>
                        </label>
                        <input type="number" class="form-control" name="prod_ore" size="9" 
                               value="<?php echo isset($row['prod_ore']) ? htmlspecialchars($row['prod_ore'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Goods</span>
                        </label>
                        <input type="number" class="form-control" name="prod_goods" size="9" 
                               value="<?php echo isset($row['prod_goods']) ? htmlspecialchars($row['prod_goods'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Energy</span>
                        </label>
                        <input type="number" class="form-control" name="prod_energy" size="9" 
                               value="<?php echo isset($row['prod_energy']) ? htmlspecialchars($row['prod_energy'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Fighters</span>
                        </label>
                        <input type="number" class="form-control" name="prod_fighters" size="9" 
                               value="<?php echo isset($row['prod_fighters']) ? htmlspecialchars($row['prod_fighters'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Torpedoes</span>
                        </label>
                        <input type="number" class="form-control" name="prod_torp" size="9" 
                               value="<?php echo isset($row['prod_torp']) ? htmlspecialchars($row['prod_torp'], ENT_QUOTES) : ''; ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <input type="hidden" name="planet" value="<?php echo isset($planet) ? htmlspecialchars($planet, ENT_QUOTES) : ''; ?>">
        <input type="hidden" name="operation" value="save">
        <input type="hidden" name="menu" value="planedit">
        <input type="hidden" name="swordfish" value="<?php echo isset($swordfish) ? htmlspecialchars($swordfish, ENT_QUOTES) : ''; ?>">
        
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg px-5">
                <i class="bi bi-save me-2"></i>Save Planet
            </button>
        </div>
    </form>
</div>
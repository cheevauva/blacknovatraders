<?php
$shiptypes = shipTypes();
$self = \BNT\Controller\ShipsController::as($this);
?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<div class="container mt-4">
    <?php if (empty($self->ships)): ?>  
        <div class="alert alert-danger" role="alert">
            Empty ship list
        </div>
    <?php endif; ?>
    <form action="ships.php" method="post" id="bntShipsForm">
        <div class="accordion" id="accordionShips">
            <?php foreach ($self->ships as $ship) : ?>
                <?php
                $i = $ship['ship_id'];
                $isCurrent = $ship['ship_id'] == $self->userinfo['ship_id'];
                $isDestroyed = $ship['ship_destroyed'] === 'Y';
                ?>
                <div class="accordion-item" >
                    <h2 class="accordion-header" id="heading<?= $i; ?>">
                        <button 
                            class="accordion-button <?php if (!$isCurrent): ?>collapsed<?php endif; ?>" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#collapse<?= $i; ?>" 
                            aria-expanded="<?php if ($isCurrent): ?>true<?php else: ?>false<?php endif; ?>" 
                            aria-controls="collapse<?= $i; ?>"
                            >
                                <?php echo htmlspecialchars($ship['ship_name']); ?> 
                        </button>
                    </h2>
                    <div id="collapse<?= $i; ?>" class="accordion-collapse collapse <?php if ($isCurrent): ?>show<?php endif; ?>" aria-labelledby="heading<?= $i; ?>" data-bs-parent="#accordionShips">
                        <div class="accordion-body container">
                            <div class="row mb-5">
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input 
                                            class="form-check-input" 
                                            type="radio" 
                                            name="ship_id" 
                                            value="<?= $ship['ship_id']; ?>" 
                                            <?php if ($isCurrent): ?>checked<?php endif; ?>
                                            >
                                        <label class="form-check-label" for="ship_id">
                                            <img src="images/<?php echo $shiptypes[shipLevel($ship)]; ?>" border=0><br/>
                                            <?php echo $l->credits; ?>: <?php echo NUMBER($ship['credits']); ?>
                                            <?php if ($ship['ship_destroyed'] == 'Y'): ?>
                                                <div class="alert alert-danger" role="alert">
                                                    <?= $l->shipdestroyed; ?>
                                                </div>
                                                <?php if ($ship['dev_escapepod'] == 'Y'): ?> 
                                                    <div class="alert alert-success" role="alert">
                                                        <?= $l->escape_pod; ?>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="<?= $i; ?>tab1-tab" data-bs-toggle="tab" data-bs-target="#<?= $i; ?>tab1" type="button" role="tab" aria-controls="<?= $i; ?>tab1" aria-selected="true">
                                                <?php echo $l->ship_levels; ?>
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="<?= $i; ?>tab2-tab" data-bs-toggle="tab" data-bs-target="#<?= $i; ?>tab2" type="button" role="tab" aria-controls="<?= $i; ?>tab2" aria-selected="false">
                                                <?php echo $l->holds; ?>  
                                                [<?php echo NUMBER($ship['ship_ore'] + $ship['ship_organics'] + $ship['ship_goods'] + $ship['ship_colonists']); ?> / <?php echo NUMBER(NUM_HOLDS($ship['hull'])); ?>]
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="<?= $i; ?>tab3-tab" data-bs-toggle="tab" data-bs-target="#<?= $i; ?>tab3" type="button" role="tab" aria-controls="<?= $i; ?>tab3" aria-selected="false">
                                                <?php echo $l->arm_weap; ?>
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="<?= $i; ?>tab4-tab" data-bs-toggle="tab" data-bs-target="#<?= $i; ?>tab4" type="button" role="tab" aria-controls="<?= $i; ?>tab4" aria-selected="false">
                                                <?php echo $l->devices; ?>
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="<?= $i; ?>tab5-tab" data-bs-toggle="tab" data-bs-target="#<?= $i; ?>tab5" type="button" role="tab" aria-controls="<?= $i; ?>tab5" aria-selected="false">
                                                <?php echo $l->logs; ?>
                                            </button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade show active" id="<?= $i; ?>tab1" role="tabpanel" aria-labelledby="<?= $i; ?>tab1-tab">
                                            <table class="table table-hover">
                                                <tr><td><?php echo $l->hull; ?></td><td><?php echo $l->level; ?> <?php echo $ship['hull']; ?></td></tr>
                                                <tr><td><?php echo $l->engines; ?></td><td><?php echo $l->level; ?> <?php echo $ship['engines']; ?></td></tr>
                                                <tr><td><?php echo $l->power; ?></td><td><?php echo $l->level; ?> <?php echo $ship['power']; ?></td></tr>
                                                <tr><td><?php echo $l->computer; ?></td><td><?php echo $l->level; ?> <?php echo $ship['computer']; ?></td></tr>
                                                <tr><td><?php echo $l->sensors; ?></td><td><?php echo $l->level; ?> <?php echo $ship['sensors']; ?></td></tr>
                                                <tr><td><?php echo $l->armor; ?></td><td><?php echo $l->level; ?> <?php echo $ship['armor']; ?></td></tr>
                                                <tr><td><?php echo $l->shields; ?></td><td><?php echo $l->level; ?> <?php echo $ship['shields']; ?></td></tr>
                                                <tr><td><?php echo $l->beams; ?></td><td><?php echo $l->level; ?> <?php echo $ship['beams']; ?></td></tr>
                                                <tr><td><?php echo $l->torp_launch; ?></td><td><?php echo $l->level; ?> <?php echo $ship['torp_launchers']; ?></td></tr>
                                                <tr><td><?php echo $l->cloak; ?></td><td><?php echo $l->level; ?> <?php echo $ship['cloak']; ?></td></tr>
                                                <tr><td><i><?php echo $l->shipavg; ?></i></td><td><?php echo $l->level; ?> <?php echo NUMBER(shipScore($ship), 2); ?></td></tr>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade" id="<?= $i; ?>tab2" role="tabpanel" aria-labelledby="<?= $i; ?>tab2-tab">
                                            <table  class="table table-hover">

                                                <tr><td><?php echo $l->ore; ?></td><TD ALIGN=RIGHT><?php echo NUMBER($ship['ship_ore']); ?></td></tr>
                                                <tr><td><?php echo $l->organics; ?></td><TD ALIGN=RIGHT><?php echo NUMBER($ship['ship_organics']); ?></td></tr>
                                                <tr><td><?php echo $l->goods; ?></td><TD ALIGN=RIGHT><?php echo NUMBER($ship['ship_goods']); ?></td></tr>
                                                <tr><td><?php echo $l->colonists; ?></td><TD ALIGN=RIGHT><?php echo NUMBER($ship['ship_colonists']); ?></td></tr>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade" id="<?= $i; ?>tab3" role="tabpanel" aria-labelledby="<?= $i; ?>tab3-tab">
                                            <table  class="table table-hover">
                                                <tr><td><?php echo $l->armorpts; ?></td><TD ALIGN=RIGHT><?php echo NUMBER($ship['armor_pts']); ?> / <?php echo NUMBER(NUM_ARMOUR($ship['armor'])); ?></td></tr>
                                                <tr><td><?php echo $l->fighters; ?></td><TD ALIGN=RIGHT><?php echo NUMBER($ship['ship_fighters']); ?> / <?php echo NUMBER(NUM_FIGHTERS($ship['computer'])); ?></td></tr>
                                                <tr><td><?php echo $l->torps; ?></td><TD ALIGN=RIGHT><?php echo NUMBER($ship['torps']); ?> / <?php echo NUMBER(NUM_TORPEDOES($ship['torp_launchers'])); ?></td></tr>
                                                <td><?php echo $l->energy; ?></td>
                                                <TD ALIGN=RIGHT><?php echo NUMBER($ship['ship_energy']); ?> / <?php echo NUMBER(NUM_ENERGY($ship['power'])); ?></td>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade" id="<?= $i; ?>tab4" role="tabpanel" aria-labelledby="<?= $i; ?>tab4-tab">
                                            <table  class="table table-hover">
                                                <tr><td><?php echo $l->beacons; ?></td><TD ALIGN=RIGHT><?php echo $ship['dev_beacon']; ?></td></tr>
                                                <tr><td><?php echo $l->warpedit; ?></td><TD ALIGN=RIGHT><?php echo $ship['dev_warpedit']; ?></td></tr>
                                                <tr><td><?php echo $l->genesis; ?></td><TD ALIGN=RIGHT><?php echo $ship['dev_genesis']; ?></td></tr>
                                                <tr><td><?php echo $l->deflect; ?></td><TD ALIGN=RIGHT><?php echo $ship['dev_minedeflector']; ?></td></tr>
                                                <tr><td><?php echo $l->ewd; ?></td><TD ALIGN=RIGHT><?php echo $ship['dev_emerwarp']; ?></td></tr>
                                                <tr><td><?php echo $l->escape_pod; ?></td><TD ALIGN=RIGHT><?php echo ($ship['dev_escapepod'] == 'Y') ? $l->yes : $l->no; ?></td></tr>
                                                <tr><td><?php echo $l->fuel_scoop; ?></td><TD ALIGN=RIGHT><?php echo ($ship['dev_fuelscoop'] == 'Y') ? $l->yes : $l->no; ?></td></tr>
                                                <tr><td><?php echo $l->lssd; ?></td><TD ALIGN=RIGHT><?php echo ($ship['dev_lssd'] == 'Y') ? $l->yes : $l->no; ?></td></tr>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade" id="<?= $i; ?>tab5" role="tabpanel" aria-labelledby="<?= $i; ?>tab5-tab">
                                            AbC
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>

        <div class="row mt-4">
            <div class="col">
                <input type="submit"  class="btn btn-primary" value="<?php echo $l->submit; ?>">
                <input type="reset" class="btn btn-secondary"  value="<?php echo $l->reset; ?>">
            </div>
        </div>
    </form>
    <script type="text/javascript">
        bntForm('bntShipsForm');
    </script>
</div>
<?php include_footer(); ?>

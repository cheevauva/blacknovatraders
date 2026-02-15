<?php $self = \BNT\Controller\CreateUniverseController::as($this); ?>
<?php include_header(); ?>
<div class="alert alert-info mb-4" role="alert">
    Create Universe Confirmation [So you would like your <?php echo $self->sector_max; ?> sector universe to have]
</div>
<?php if ($self->fedsecs > $self->sector_max) : ?>
    <div class="alert alert-danger mb-4" role="alert">
        The number of Federation sectors must be smaller than the size of the universe!
    </div>
    <hr class="my-4">
<?php endif; ?>

<form action="create_universe.php" method="post" class="form-container">
    <input type="hidden" name="step" value="3">
    <input type="hidden" name="admin_mail" value="<?php echo htmlspecialchars($self->admin_mail); ?>">
    <input type="hidden" name="admin_pass" value="<?php echo htmlspecialchars($self->admin_pass); ?>">
    <input type="hidden" name="sched_ticks" value="<?php echo $self->sched_ticks; ?>">
    <input type="hidden" name="sched_turns" value="<?php echo $self->sched_turns; ?>">
    <input type="hidden" name="sched_igb" value="<?php echo $self->sched_igb; ?>">
    <input type="hidden" name="sched_news" value="<?php echo $self->sched_news; ?>">
    <input type="hidden" name="sched_planets" value="<?php echo $self->sched_planets; ?>">
    <input type="hidden" name="sched_ports" value="<?php echo $self->sched_ports; ?>">
    <input type="hidden" name="sched_degrade" value="<?php echo $self->sched_degrade; ?>">
    <input type="hidden" name="sched_apocalypse" value="<?php echo $self->sched_apocalypse; ?>">
    <input type="hidden" name="sched_ranking" value="<?php echo $self->sched_ranking; ?>">
    <input type="hidden" name="universe_size" value="<?php echo $self->universe_size; ?>">
    <input type="hidden" name="sector_max" value="<?php echo $self->sector_max; ?>">
    <input type="hidden" name="energy_limit" value="<?php echo $self->energy_limit; ?>">
    <input type="hidden" name="goods_limit" value="<?php echo $self->goods_limit; ?>">
    <input type="hidden" name="ore_limit" value="<?php echo $self->ore_limit; ?>">
    <input type="hidden" name="organics_limit" value="<?php echo $self->organics_limit; ?>">
    <input type="hidden" name="special" value="<?php echo $self->special; ?>">
    <input type="hidden" name="ore" value="<?php echo $self->ore; ?>">
    <input type="hidden" name="organics" value="<?php echo $self->organics; ?>">
    <input type="hidden" name="energy" value="<?php echo $self->energy; ?>">
    <input type="hidden" name="goods" value="<?php echo $self->goods; ?>">
    <input type="hidden" name="planets" value="<?php echo $self->planets; ?>">
    <input type="hidden" name="fedsecs" value="<?php echo $self->fedsecs; ?>">
    <input type="hidden" name="initscommod" value="<?php echo $self->initscommod; ?>">
    <input type="hidden" name="initbcommod" value="<?php echo $self->initbcommod; ?>">
    <input type="hidden" name="swordfish" value="<?php echo htmlspecialchars($self->swordfish); ?>">
    <div class="mb-4">
        <h4 class="mb-3">Configuration Summary</h4>

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Admin Email</div>
            <div class="col-md-6"><?php echo htmlspecialchars($self->admin_mail); ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Universe size</div>
            <div class="col-md-6"><?php echo (int) $self->universe_size; ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Special ports</div>
            <div class="col-md-6"><?php echo $self->startParams->specialSectorsCount ?? 0; ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Ore ports</div>
            <div class="col-md-6"><?php echo $self->startParams->oreSectorsCount ?? 0; ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Organics ports</div>
            <div class="col-md-6"><?php echo $self->startParams->organicsSectorsCount ?? 0; ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Goods ports</div>
            <div class="col-md-6"><?php echo $self->startParams->goodsSectorsCount ?? 0; ?></div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6 fw-bold">Energy ports</div>
            <div class="col-md-6"><?php echo $self->startParams->energySectorsCount ?? 0; ?></div>
        </div>

        <hr class="my-3">

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Initial commodities to sell</div>
            <div class="col-md-6"><?php echo number_format($self->initscommod, 2); ?>%</div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6 fw-bold">Initial commodities to buy</div>
            <div class="col-md-6"><?php echo number_format($self->initbcommod, 2); ?>%</div>
        </div>

        <hr class="my-3">

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Empty sectors</div>
            <div class="col-md-6"><?php echo $self->startParams->emptySectorsCount; ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Federation sectors</div>
            <div class="col-md-6"><?php echo $self->startParams->fedSectorsCount; ?></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6 fw-bold">Unowned planets</div>
            <div class="col-md-6"><?php echo $self->startParams->unownedPlanetsCount; ?></div>
        </div>

        <hr class="my-3">

        <div class="row mb-3">
            <div class="col-md-6 fw-bold">Scheduler settings</div>
            <div class="col-md-6">
                <?php echo $self->sched_ticks; ?> | 
                <?php echo $self->sched_turns; ?> | 
                <?php echo $self->sched_igb; ?> | 
                <?php echo $self->sched_news; ?> | 
                <?php echo $self->sched_planets; ?> | 
                <?php echo $self->sched_ports; ?> | 
                <?php echo $self->sched_degrade; ?> | 
                <?php echo $self->sched_apocalypse; ?> | 
                <?php echo $self->sched_ranking; ?> 
            </div>
        </div>


    </div>

    <div class="d-grid gap-2 d-md-flex  mb-4">
        <input type="submit" class="btn btn-primary" value="Confirm">
    </div>
</form>
<?php include_footer(); ?>

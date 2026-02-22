<?php $self = \BNT\Controller\CreateUniverseController::as($self); ?>
<?php include "header.php"; ?>
<form action="create_universe.php" method="post" class="form-container">
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Admin options</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col mb-2">
                    <label class="form-label">Email</label>
                    <input type="text" class="form-control" name="admin_mail" size="10" maxlength="10" value="<?php echo htmlspecialchars($self->admin_mail); ?>" required>
                </div>
                <div class="col mb-2">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="admin_password" size="10" maxlength="10" value="<?php echo htmlspecialchars($self->admin_pass); ?>" required>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Create Universe [Base/Planet Setup]</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col mb-2">
                    <label class="form-label">Percent Special</label>
                    <input type="text" class="form-control" name="special" size="10" maxlength="10" value="<?php echo $self->special; ?>">
                </div>
                <div class="col mb-2">
                    <label class="form-label">Percent Ore</label>
                    <input type="text" class="form-control" name="ore" size="10" maxlength="10" value="<?php echo $self->ore; ?>">
                </div>
                <div class="col mb-2">
                    <label class="form-label">Percent Organics</label>
                    <input type="text" class="form-control" name="organics" size="10" maxlength="10" value="<?php echo $self->organics; ?>">
                </div>
                <div class="col mb-2">
                    <label class="form-label">Percent Goods</label>
                    <input type="text" class="form-control" name="goods" size="10" maxlength="10" value="<?php echo $self->goods; ?>">
                </div>
                <div class="col mb-2">
                    <label class="form-label">Percent Energy</label>
                    <input type="text" class="form-control" name="energy" size="10" maxlength="10" value="<?php echo $self->energy; ?>">
                </div>
            </div>

        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Limits</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col mb-2">
                    <label class="form-label">Organics</label>
                    <input type="number" class="form-control" name="organics_limit" size="10" maxlength="10" value="<?php echo $self->organics_limit; ?>">
                </div>
                <div class="col mb-2">
                    <label class="form-label">Goods</label>
                    <input type="number" class="form-control" name="goods_limit" size="10" maxlength="10" value="<?php echo $self->goods_limit; ?>">
                </div>
                <div class="col mb-2">
                    <label class="form-label">Energy</label>
                    <input type="number" class="form-control" name="energy_limit" size="10" maxlength="10" value="<?php echo $self->energy_limit; ?>">
                </div>
                <div class="col mb-2">
                    <label class="form-label">Ore limit</label>
                    <input type="number" class="form-control" name="ore_limit" size="10" maxlength="10" value="<?php echo $self->ore_limit; ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Schedulers</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <label class="form-label">Ranking</label>
                    <input type="number" class="form-control" name="sched_ranking" size="3" maxlength="2" value="<?php echo $self->sched_ranking; ?>" required>
                </div>
                <div class="col">
                    <label class="form-label">Ticks</label>
                    <input type="number" class="form-control" name="sched_ticks" size="3" maxlength="2" value="<?php echo $self->sched_ticks; ?>" required>
                </div>
                <div class="col">
                    <label class="form-label">Turns</label>
                    <input type="number" class="form-control" name="sched_turns" size="3" maxlength="2" value="<?php echo $self->sched_turns; ?>" required>
                </div>
                <div class="col">
                    <label class="form-label">IGB</label>
                    <input type="number" class="form-control" name="sched_igb" size="3" maxlength="2" value="<?php echo $self->sched_igb; ?>" required>
                </div>
                <div class="col">
                    <label class="form-label">News</label>
                    <input type="number" class="form-control" name="sched_news" size="3" maxlength="2" value="<?php echo $self->sched_news; ?>" required>
                </div>
                <div class="col">
                    <label class="form-label">Planets</label>
                    <input type="number" class="form-control" name="sched_planets" size="3" maxlength="2" value="<?php echo $self->sched_planets; ?>" required>
                </div>
                <div class="col">
                    <label class="form-label">Ports</label>
                    <input type="number" class="form-control" name="sched_ports" size="3" maxlength="2" value="<?php echo $self->sched_ports; ?>" required>
                </div>
                <div class="col">
                    <label class="form-label">Degrade</label>
                    <input type="number" class="form-control" name="sched_degrade" size="3" maxlength="2" value="<?php echo $self->sched_degrade; ?>" required>
                </div>
                <div class="col">
                    <label class="form-label">Apocalypse</label>
                    <input type="number" class="form-control" name="sched_apocalypse" size="3" maxlength="2" value="<?php echo $self->sched_apocalypse; ?>" required>
                </div>
            </div>
        </div>
    </div>


    <div class="mb-4">

        <div class="alert alert-info mb-4">
            Percent Empty: Equal to 100 - total of above.
        </div>

        <div class="mb-3">
            <label class="form-label">Initial Commodities to Sell [% of max]</label>
            <input type="text" class="form-control" name="initscommod" size="10" maxlength="10" value="<?php echo number_format($self->initscommod, 2); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Initial Commodities to Buy [% of max]</label>
            <input type="text" class="form-control" name="initbcommod" size="10" maxlength="10" value="<?php echo number_format($self->initbcommod, 2); ?>">
        </div>
    </div>


    <div class="mb-4">
        <h4 class="mb-3">Create Universe [Sector/Link Setup]</h4>

        <div class="mb-3">
            <label class="form-label">Universe size</label>
            <input type="text" class="form-control" name="universe_size" size="10" maxlength="10" value="<?php echo $self->universe_size; ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Number of sectors total</label>
            <input type="text" class="form-control" name="sector_max" size="10" maxlength="10" value="<?php echo $self->sector_max; ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Number of Federation sectors</label>
            <input type="text" class="form-control" name="fedsecs" size="10" maxlength="10" value="<?php echo $self->fedsecs; ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Percent of sectors with unowned planets</label>
            <input type="text" class="form-control" name="planets" size="10" maxlength="10" value="<?php echo $self->planets; ?>">
        </div>
    </div>

    <input type="hidden" name="engage" value="1">
    <input type="hidden" name="step" value="2">
    <input type="hidden" name="swordfish" value="<?php echo htmlspecialchars($self->swordfish); ?>">

    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
        <input type="submit" class="btn btn-primary me-md-2" value="Submit">
        <input type="reset" class="btn btn-secondary" value="Reset">
    </div>
</form>
<?php include "footer.php"; ?>

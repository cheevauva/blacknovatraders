<?php $self = \BNT\Controller\CreateUniverseController::as($this); ?>
<?php include_header(); ?>
<div class="mb-5">
    <h4 class="mb-4">Configuring game scheduler — Stage 7</h4>

    <div class="mb-4">
        <div class="row mb-3">
            <div class="col-md-9">
                Update ticks will occur every <?php echo $self->sched_ticks; ?> minutes.
            </div>
            <div class="col-md-3 text-center">
                <span class="badge bg-info">Already Set</span>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-9">Turns will occur every <?php echo $self->sched_turns; ?> minutes</div>
            <div class="col-md-3"><span class="badge bg-success">Inserted</span></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-9">Defenses will be checked every <?php echo $self->sched_turns; ?> minutes</div>
            <div class="col-md-3"><span class="badge bg-success">Inserted</span></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-9">Xenobes will play every <?php echo $self->sched_turns; ?> minutes.</div>
            <div class="col-md-3"><span class="badge bg-success">Inserted</span></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-9">Interests on IGB accounts will be accumulated every <?php echo $self->sched_igb; ?> minutes.</div>
            <div class="col-md-3"><span class="badge bg-success">Inserted</span></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-9">News will be generated every <?php echo $self->sched_news; ?> minutes.</div>
            <div class="col-md-3"><span class="badge bg-success">Inserted</span></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-9">Planets will generate production every <?php echo $self->sched_planets; ?> minutes.</div>
            <div class="col-md-3"><span class="badge bg-success">Inserted</span></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-9">Ports will regenerate every <?php echo $self->sched_ports; ?> minutes.</div>
            <div class="col-md-3"><span class="badge bg-success">Inserted</span></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-9">Ships will be towed from fed sectors every <?php echo $self->sched_turns; ?> minutes.</div>
            <div class="col-md-3"><span class="badge bg-success">Inserted</span></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-9">Rankings will be generated every <?php echo $self->sched_ranking; ?> minutes.</div>
            <div class="col-md-3"><span class="badge bg-success">Inserted</span></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-9">Sector Defences will degrade every <?php echo $self->sched_degrade; ?> minutes.</div>
            <div class="col-md-3"><span class="badge bg-success">Inserted</span></div>
        </div>

        <div class="row mb-3">
            <div class="col-md-9">The planetary apocalypse will occur every <?php echo $this->sched_apocalypse; ?> minutes.</div>
            <div class="col-md-3"><span class="badge bg-success">Inserted</span></div>
        </div>

        <div class="alert alert-success mt-3">
            Completed successfully
        </div>
    </div>
</div>

<hr class="my-5">

<div class="mb-5">
    <h4 class="mb-4">Inserting Admins Account Information</h4>

    <div class="mb-4">
        <div class="row mb-2">
            <div class="col-md-9">Inserting Admins ibank Information</div>
            <div class="col-md-3"><span class="badge bg-success">Inserted</span></div>
        </div>

        <div class="alert alert-info mb-3">
            <strong>Admins login Information:</strong><br>
            Username: <?php echo $self->admin_mail; ?><br>
        </div>

        <div class="row mb-2">
            <div class="col-md-9">Inserting Admins Ship Information</div>
            <div class="col-md-3"><span class="badge bg-success">Inserted</span></div>
        </div>

        <div class="row mb-3">
            <div class="col-md-9">Inserting Admins Zone Information</div>
            <div class="col-md-3"><span class="badge bg-success">Inserted</span></div>
        </div>

        <div class="alert alert-success mt-3">
            Completed successfully.
        </div>
    </div>
</div>

<div class="text-center py-5">
    <div class="display-6 mb-4">Congratulations! Universe created successfully.</div>
    <p class="lead mb-4">
        Click <a href="login.php" class="btn btn-success btn-lg">here</a> to return to the login screen.
    </p>
</div>
<?php include_footer(); ?>


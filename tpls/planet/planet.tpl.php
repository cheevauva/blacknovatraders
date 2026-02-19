<?php
global $allow_ibank;
$self = \BNT\Controller\PlanetController::as($this);
include_header();
?>


<?php if (!empty($command)) : ?>
    <BR><a href="planet.php?planet_id=<?= $self->planetId; ?>"><?= $l->clickme; ?></a> <?= $l->toplanetmenu; ?><BR><BR>
<?php endif; ?>

<?php if ($allow_ibank) : ?>
    <?= $l->ifyouneedplan; ?> <A HREF="igb.php?planet_id=<?= $self->planetId; ?>"><?= $l->igb_term; ?></A>.<BR><BR>
<?php endif; ?>

<A HREF ="bounty.php"><?= $l->by_placebounty; ?></A><p>
<?php include_footer(); ?>

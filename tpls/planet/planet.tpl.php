<?php
global $allow_ibank;
$self = \BNT\Controller\PlanetController::as($self);
include_header();
?>


<?php if (!empty($command)) : ?>
    <BR><a href="planet.php?planet_id=<?= $self->planetId; ?>"><?= $l->l_clickme; ?></a> <?= $l->l_toplanetmenu; ?><BR><BR>
<?php endif; ?>

<?php if ($allow_ibank) : ?>
    <?= $l->l_ifyouneedplan; ?> <A HREF="igb.php?planet_id=<?= $self->planetId; ?>"><?= $l->l_igb_term; ?></A>.<BR><BR>
<?php endif; ?>

<A HREF ="bounty.php"><?= $l->l_by_placebounty; ?></A><p>
<?php include_footer(); ?>

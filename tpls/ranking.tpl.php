<?php $title = $l->ranks_title; ?>
<?php $self = BNT\Controller\RankingController::as($self);?>
<?php include("header.php"); ?>
<?php bigtitle(); ?>
<?php if (empty($self->ships)) : ?>
    <?= $l->ranks_none; ?>
<?php else : ?>
    <p class="text-start"><?= $l->ranks_pnum; ?>: <?= $self->numPlayers; ?></p>
    <div class="alert alert-info" role="alert">
        <?= $l->ranks_dships; ?>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <tr>
                <td><?= $l->ranks_rank; ?></td>
                <td><a href="ranking.php"><?= $l->score; ?></a></td>
                <td><?= $l->player; ?></td>
                <td><a href="ranking.php?sort=turns"><?= $l->turns_used; ?></a></td>
                <td><a href="ranking.php?sort=good"><?= $l->ranks_good; ?></a>/<a href="ranking.php?sort=bad"><?= $l->ranks_evil; ?></a></td>
                <td><a href="ranking.php?sort=alliance"><?= $l->team_alliance; ?></a></td>
                <td><a href="ranking.php?sort=efficiency">Eff. Rating.</a></td>
            </TR>
            <?php $i = 0; ?>
            <?php foreach ($self->ships as $ship) : ?>
                <?php $i++; ?>
                <?php $rating = round(sqrt(abs($ship['rating']))); ?>
                <?php $rating = $ship['rating'] < 0 ? -1 * $rating : $rating; ?>
                <tr>
                    <td><?= NUMBER($i); ?></td>
                    <td><?= NUMBER($ship['score']); ?></td>
                    <td><?= player_insignia_name($ship['ship_id']); ?>&nbsp;<?= htmlspecialchars($ship['ship_name']); ?></td>
                    <td><?= NUMBER($ship['turns_used'] <= 0 ? 1 : $ship['turns_used']); ?></td>
                    <td>&nbsp;&nbsp;<?= NUMBER($rating); ?></td>
                    <td><?= htmlspecialchars($ship['team_name']); ?>&nbsp;</td>
                    <td><?= $ship['efficiency']; ?></td>
                </TR>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>

<?php include("footer.php"); ?>

<?php $self = BNT\Controller\RankingController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<?php if (empty($self->ships)) : ?>
    <?= $l->l_ranks_none; ?>
<?php else : ?>
    <p class="text-start"><?= $l->l_ranks_pnum; ?>: <?= $self->numPlayers; ?></p>
    <div class="alert alert-info" role="alert">
        <?= $l->l_ranks_dships; ?>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <tr>
                <td><?= $l->l_ranks_rank; ?></td>
                <td><a href="<?= route('ranking');?>"><?= $l->l_score; ?></a></td>
                <td><?= $l->l_player; ?></td>
                <td><a href="<?= route('ranking', 'sort=turns'); ?>"><?= $l->l_turns_used; ?></a></td>
                <td><a href="<?= route('ranking', 'sort=good'); ?>"><?= $l->l_ranks_good; ?></a>/<a href="<?= route('ranking', 'sort=bad'); ?>"><?= $l->l_ranks_evil; ?></a></td>
                <td><a href="<?= route('ranking', 'sort=alliance'); ?>"><?= $l->l_team_alliance; ?></a></td>
                <td><a href="<?= route('ranking', 'sort=efficiency'); ?>">Eff. Rating.</a></td>
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
<?php include_footer(); ?>

<?php $title = $l_ranks_title; ?>
<?php include("header.php"); ?>
<?php bigtitle(); ?>
<?php if (empty($rows)) : ?>
    <?php echo $l_ranks_none; ?>
<?php else : ?>
    <p class="text-start"><?php echo $l_ranks_pnum; ?>: <?php echo NUMBER($num_players); ?></p>
    <div class="alert alert-info" role="alert">
        <?php echo $l_ranks_dships; ?>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <tr BGCOLOR="<?php echo $color_header; ?>">
                <td><?php echo $l_ranks_rank; ?></td>
                <td><a href="ranking.php"><?php echo $l_score; ?></a></td>
                <td><?php echo $l_player; ?></td>
                <td><a href="ranking.php?sort=turns"><?php echo $l_turns_used; ?></a></td>
                <td><a href="ranking.php?sort=login"><?php echo $l_ranks_lastlog; ?></a></td>
                <td><a href="ranking.php?sort=good"><?php echo $l_ranks_good; ?></a>/<a href="ranking.php?sort=bad"><?php echo $l_ranks_evil; ?></a></td>
                <td><a href="ranking.php?sort=alliance"><?php echo $l_team_alliance; ?></a></td>
                <td><a href="ranking.php?sort=online">Online</a></td>
                <td><a href="ranking.php?sort=efficiency">Eff. Rating.</a></td>
            </TR>
            <?php $i = 0; ?>
            <?php foreach ($rows as $row) : ?>
                <?php $i++; ?>
                <?php $rating = round(sqrt(abs($row['rating']))); ?>
                <?php $rating = $row['rating'] < 0 ? -1 * $rating : $rating; ?>
                <tr>
                    <td><?php echo NUMBER($i); ?></td>
                    <td><?php echo NUMBER($row['score']); ?></td>
                    <td><?php echo player_insignia_name($row['email']); ?>&nbsp;<?php echo htmlspecialchars($row['character_name']); ?></td>
                    <td><?php echo NUMBER($row['turns_used'] <= 0 ? 1 : $row['turns_used']); ?></td>
                    <td><?php echo $row['last_login']; ?></td>
                    <td>&nbsp;&nbsp;<?php echo NUMBER($rating); ?></td>
                    <td><?php echo htmlspecialchars($row['team_name']); ?>&nbsp;</td>
                    <td><?php echo (time() - $row['online']) / 60 <= 5 ? 'Online' : 'Offline'; ?></td>
                    <td><?php echo $row['efficiency']; ?></td>
                </TR>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>

<?php include("footer.php"); ?>

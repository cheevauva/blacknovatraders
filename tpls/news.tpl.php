<?php include_header();?>

<?php bigtitle(); ?>

<div class="container">
    <div class="alert alert-info" role="alert">
            <?= $l->news_info ?>
    </div>
        <p><?= $l->news_for ?> <?= $self->startdate ?></p>
    <table class="table">
        <tr>
            <td colspan="2" align="right">
                <a href="news.php?startdate=<?= $self->previousday ?>"><?= $l->news_prev ?></a> - <a href="news.php?startdate=<?= $self->nextday ?>"><?= $l->news_next ?></a>
            </td>
        </tr>
        <?php if (empty($self->news)) : ?>
            <tr class="table-warning">
                <td  width="27%">
                    <?= $l->news_flash; ?>
                </td>
                <td ><?= $l->news_none; ?></td>
            </tr>
        <?php else : ?>
            <?php foreach ($self->news as $row) : ?>
                <tr>
                    <td width="27%"><?= htmlspecialchars($row['headline']) ?></td>
                    <td>
                        <?= htmlspecialchars($row['newstext']) ?>
                    </td>
                </tr>

            <?php endforeach; ?>
        <?php endif; ?>
        <tr>
            <td height="22" colspan="2">&nbsp;</td>
        </tr>
    </table>
</div>

<?php include_footer();?>

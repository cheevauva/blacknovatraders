<?php include("header.php"); ?>

<?php bigtitle(); ?>

<div class="container">
    <div class="alert alert-info" role="alert">
        <?php echo $l_news_info ?>
    </div>
    <p><?php echo $l_news_for ?> <?php echo $startdate ?></p>
    <table class="table">
        <tr>
            <td colspan="2" align="right">
                <a href="news.php?startdate=<?php echo $previousday ?>"><?php echo $l_news_prev ?></a> - <a href="news.php?startdate=<?php echo $nextday ?>"><?php echo $l_news_next ?></a>
            </td>
        </tr>
        <?php if (empty($rows)): ?>
            <tr class="table-warning">
                <td  width="27%">
                    <?php echo $l_news_flash; ?>
                </td>
                <td ><?php echo $l_news_none; ?></td>
            </tr>
        <?php else: ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td width="27%"><?php echo $row['headline'] ?></td>
                    <td>
                        <?php echo $row['newstext'] ?>
                    </td>
                </tr>

            <?php endforeach; ?>
        <?php endif; ?>
        <tr>
            <td height="22" colspan="2">&nbsp;</td>
        </tr>
    </table>
</div>

<?php include("footer.php"); ?>

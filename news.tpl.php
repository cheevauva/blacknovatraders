<?php include("header.php"); ?>

<?php bigtitle(); ?>

<div class="container">
    <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
            <td colspan="2">
                <div class="alert alert-info" role="alert">
                    <?php echo $l_news_info ?>
                </div>
                <p><?php echo $l_news_for ?> <?php echo $startdate ?></p>
            </td>
        </tr>
        <tr>
            <td height="22" width="27%" bgcolor="#00001A">&nbsp;</td>
            <td height="22" width="73%" bgcolor="#00001A" align="right"><a href="news.php?startdate=<?php echo $previousday ?>"><?php echo $l_news_prev ?></a> - <a href="news.php?startdate=<?php echo $nextday ?>"><?php echo $l_news_next ?></a></td>
        </tr>
        <tr>
            <td height="22" colspan="2">&nbsp;</td>
        </tr>
        <?php if (empty($rows)): ?>
            <tr class="alert alert-warning" role="alert">
                <td>
                    <?php echo $l_news_flash; ?>
                </td>
                <td ><?php echo $l_news_none; ?></td>
            </tr>
        <?php else: ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?php echo $row['headline'] ?></td>
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

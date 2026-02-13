<?php include "header.php"; ?>
<h1><?php echo str_replace("[player]", htmlspecialchars($playerinfo['character_name']), $l_log_log); ?></h1>
<table class="table table-hover">
    <?php foreach ($logs as $log) : ?>
        <tr>
            <td><?php echo $log['title']; ?></td>
            <td>
                <?php echo $l_log_months[date('n', strtotime($log['time'])) - 1]; ?> 
                <?php echo date('d', strtotime($log['time'])); ?> 
                <?php echo date('Y', strtotime($log['time'])); ?> 
                <?php echo date('H:i:s', strtotime($log['time'])); ?>
            </td>
            <td><?php echo htmlspecialchars($log['text']); ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php $postlink = $isAdmin ? '&player=' . fromGet('player') : ''; ?>
<a href="log.php?startdate=<?php echo date('Y-m-d', strtotime($startdate . ' -3 day')) . $postlink; ?>" class="btn btn-primary"><<<</a>
<a href="log.php?startdate=<?php echo date('Y-m-d', strtotime($startdate . ' -2 day')) . $postlink; ?>" class="btn btn-primary"><?php echo date('Y-m-d', strtotime($startdate . ' -2 day')); ?></a>
<a href="log.php?startdate=<?php echo date('Y-m-d', strtotime($startdate . ' -1 day')) . $postlink; ?>" class="btn btn-primary"><?php echo date('Y-m-d', strtotime($startdate . ' -1 day')); ?></a>
<a href="log.php?startdate=<?php echo $startdate . $postlink; ?>" class="btn btn-primary"><?php echo $startdate; ?></a>
<?php if (strtotime($startdate) < strtotime('today')) : ?>
    <a href="log.php?startdate=<?php echo date('Y-m-d', strtotime($startdate . ' + 1 day')) . $postlink; ?>" class="btn btn-primary">>>></a>
<?php endif; ?>
<?php include "footer.php"; ?>

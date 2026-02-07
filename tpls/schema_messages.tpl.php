<?php include "header.php"; ?>
<?php foreach ($messages as $message): ?>
    <div class="alert alert-info">
        <?php echo $message; ?>
    </div>
<?php endforeach; ?>
<?php include "footer.php"; ?>

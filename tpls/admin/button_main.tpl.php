<?php if ($button_main): ?>
    <div class="container-fluid mt-4">
        <form action="admin.php" method="POST">
            <input type="hidden" name="swordfish" value="<?php echo isset($swordfish) ? htmlspecialchars($swordfish, ENT_QUOTES) : ''; ?>">
            <button type="submit" class="btn btn-outline-secondary btn-lg px-5">
                <i class="bi bi-house-door me-2"></i>Return to Main Menu
            </button>
        </form>
    </div>
<?php endif; ?>
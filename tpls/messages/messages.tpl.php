<?= include_header(); ?>
<?php $self = \BNT\Controller\MessagesController::as($this); ?>
<form action="mailto2.php" method="POST">
    <table class="form-table">
        <tr>
            <td>To:</td>
            <td>
                <select name="to">
                    <?php foreach ($self->ships as $ship): ?>
                        <option value="<?php echo $ship['ship_id']; ?>" <?php echo ($ship['ship_id'] == $self->to) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($ship['ship_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><?= $l->mt_from; ?></td>
            <td>
                <input disabled type="text" name="dummy" size="40" maxlength="40" value="<?php echo htmlspecialchars($self->playerinfo['ship_name']); ?>">
            </td>
        </tr>
        <tr>
            <td><?= $l->mt_subject; ?></td>
            <td>
                <input type="text" name="subject" size="40" maxlength="40">
            </td>
        </tr>
        <tr>
            <td><?= $l->mt_message; ?>:</td>
            <td>
                <textarea name="content" rows="5" cols="40"></textarea>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" value="<?= $l->mt_send; ?>">
                <input type="reset" value="Clear">
            </td>
        </tr>
    </table>
</form>
<div class="container mt-4">
    <div class="row">
        <div class="col">
            <div class="card bg-light border">
                <div class="card-body p-1">

                    <!-- Header row -->
                    <div class="card bg-dark text-white rounded-0 border-0 mb-1">
                        <div class="card-body p-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold small"><?= $l->dreadm_center ?></span>
                                <span class="small"><?php echo "$cur_D" ?>&nbsp;<?php echo "$cur_T" ?></span>
                                <a href="main.php" class="text-white text-decoration-none">
                                    <i class="bi bi-house-door-fill"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Message list area -->
                    <?php if (empty($self->messages)): ?>
                        <div class="card bg-dark text-white rounded-0 border-0 mt-1">
                            <div class="card-body p-0">
                                <div class="card bg-white text-black rounded-0 border">
                                    <div class="card-body text-center text-danger fw-bold">
                                        <?= $l->dreadm_nomessage ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($self->messages as $msg): ?>
                            <?php $sender = $msg['sender']; ?>
                            <div class="mt-2 mb-2">
                                <hr class="bg-dark border-0" style="height: 4px; opacity: 1; margin: 4px 0;">

                                <!-- Sender row -->
                                <div class="card bg-secondary text-white rounded-0 border-0">
                                    <div class="card-body p-1">
                                        <div class="row align-items-center g-0">
                                            <div class="col-auto me-2">
                                                <span class="fw-bold small"><?= $l->dreadm_sender; ?></span>
                                            </div>
                                            <div class="col">
                                                <span class="text-warning small"><?php echo $sender['ship_name']; ?></span>
                                            </div>
                                            <div class="col-auto text-nowrap">
                                                <span class="small me-2"><?php echo $msg['sent']; ?></span>
                                                <a href="readmail.php?action=delete&ID=<?php echo $msg['ID']; ?>" class="text-white text-decoration-none">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Captain row -->
                                <div class="card bg-secondary text-white rounded-0 border-0 mt-1">
                                    <div class="card-body p-1">
                                        <div class="row g-0">
                                            <div class="col-auto me-2">
                                                <span class="fw-bold small"><?= $l->dreadm_captn ?></span>
                                            </div>
                                            <div class="col">
                                                <span class="text-warning small"><?php echo $sender['ship_name']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Subject row -->
                                <div class="card bg-secondary text-white rounded-0 border-0 mt-1">
                                    <div class="card-body p-1">
                                        <div class="row g-0">
                                            <div class="col-auto me-2">
                                                <span class="fw-bold small">Subject</span>
                                            </div>
                                            <div class="col">
                                                <span class="fw-bold text-warning small"><?php echo $msg['subject']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Message body -->
                                <div class="card bg-white text-black rounded-0 border mt-1">
                                    <div class="card-body p-2 small">
                                        <?php echo htmlspecialchars($msg['message']); ?>
                                    </div>
                                </div>

                                <!-- Action buttons -->
                                <div class="card bg-secondary text-white rounded-0 border mt-1">
                                    <div class="card-body p-1 text-center">
                                        <a href="readmail.php?action=delete&ID=<?php echo $msg['ID']; ?>" class="text-white text-decoration-none me-3"><?= $l->dreadm_del ?></a>
                                        <span class="text-white">|</span>
                                        <a href="mailto2.php?name=<?php echo $sender['ship_name']; ?>&subject=<?php echo $msg['subject']; ?>" class="text-white text-decoration-none ms-3"><?= $l->dreadm_repl ?></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Footer -->
                    <hr class="bg-dark border-0" style="height: 4px; opacity: 1; margin: 4px 0;">
                    <div class="card bg-dark text-white rounded-0 border-0">
                        <div class="card-body p-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small">Mail Reader</span>
                                <a href="readmail.php?action=delete_all" class="text-white text-decoration-none small">Delete All</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?php include_footer(); ?>

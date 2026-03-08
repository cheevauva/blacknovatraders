<?= include_header(); ?>
<?php $self = \BNT\Controller\MessagesController::as($self); ?>
<?php if ($self->reply): ?>
    <div class="card mb-3">
        <div class="card-header">
            <?= $l->l_messages_sender; ?>: <?= $self->reply['sender']['ship_name']; ?> [<?= $self->reply['sent']; ?>]
        </div>
        <div class="card-body">
            <p class="card-text"><?= htmlspecialchars($self->reply['message']); ?></p>
        </div>
    </div>
<?php endif; ?>
<?php if ($self->send): ?>
    <form action="<?= route('messages', ['send' => $self->send, 'read' => $self->read]); ?>" method="POST" id="bntMessageSendForm">
        <input type="hidden" name="action" value="send"/>
        <?php if ($self->reply): ?>
        <input type="hidden" name="reply_id" value="<?= $self->reply['id']; ?>"/>
        <?php endif;?>
        <div class="mb-3 row">
            <label class="col-sm-2 col-form-label fw-bold"><?= $l->l_messages_to ?>:</label>
            <div class="col-sm-10">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label"><?= $l->l_messages_ship ?>:</label>
                        <select name="ship" class="form-select">
                            <option value="0"></option>
                            <?php foreach ($self->ships as $ship): ?>
                                <option value="<?= $ship['ship_id']; ?>" <?= $ship['ship_id'] == $self->ship ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($ship['ship_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><?= $l->l_messages_team ?>:</label>
                        <select name="team" class="form-select">
                            <option value="0"></option>
                            <?php foreach ($self->teams as $team): ?>
                                <option value="<?= $team['id']; ?>" <?= $team['id'] == $self->team ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($team['team_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-2 col-form-label fw-bold"><?= $l->l_messages_content; ?>:</label>
            <div class="col-sm-10">
                <textarea name="content" class="form-control" rows="3" maxlength="140" required></textarea>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-10 offset-sm-2">
                <button type="submit" class="btn btn-primary"><?= $l->l_submit; ?></button>
                <button type="reset" class="btn btn-secondary"><?= $l->l_reset; ?></button>
            </div>
        </div>
    </form>
    <script type="text/javascript">
        bntForm('bntMessageSendForm');
    </script>
<?php endif; ?>
<?php if ($self->read): ?>
    <div class="overflow-auto" style="height: 400px;">
        <?php foreach ($self->messages as $msg): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <?= $l->l_messages_sender; ?>: <?= $msg['sender']['ship_name']; ?> [<?= $msg['sent']; ?>]
                </div>
                <div class="card-body">
                    <p class="card-text"><?= htmlspecialchars($msg['message']); ?></p>
                </div>
                <div class="card-footer">
                    <form action="<?= route('messages', ['send' => $self->send, 'read' => $self->read]); ?>" method="POST" id="bntMessageDelForm<?= $msg['id']; ?>" class="d-inline">
                        <input name="action" value="delete" type="hidden">
                        <input name="id" value="<?= $msg['id']; ?>" type="hidden">
                        <button type="submit" class="btn btn-danger btn-sm"><?= $l->l_messages_delete ?></button>
                    </form>
                    <script type="text/javascript">
                        bntForm('bntMessageDelForm<?= $msg['id']; ?>');
                    </script>
                    <a href="<?= route('messages', ['send' => 1, 'read' => $self->read, 'reply_id' => $msg['id'], 'ship' => $msg['sender']['ship_id']]); ?>" class="btn btn-secondary btn-sm"><?= $l->l_messages_reply ?></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card bg-dark text-white rounded-0 border-0">
        <div class="card-body p-1">
            <form action="<?= route('messages', ['send' => $self->send, 'read' => $self->read]); ?>" method="POST" id="bntMessageDelAllForm" class="d-inline">
                <input name="action" value="delete_all" type="hidden">
                <button type="submit" class="btn btn-primary btn-sm"><?= $l->l_messages_delete_all ?></button>
            </form>
            <script type="text/javascript">
                bntForm('bntMessageDelAllForm');
            </script>
        </div>
    </div>

<?php endif; ?>
<?php include_footer(); ?>

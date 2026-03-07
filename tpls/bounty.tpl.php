<?php $self = BNT\Controller\BountyController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<?php if (empty($self->response)): ?> 
    <form action="<?= route('bounty'); ?>" method="POST" id="bntBountyForm">
        <table class="table table-borderless w-auto">
            <tr>
                <td><?= $l->l_by_bountyon; ?></td>
                <td>
                    <select name="bounty_on" class="form-select">
                        <?php foreach ($self->ships as $ship): ?>
                            <?php $selected = (isset($bounty_on) && $bounty_on == $ship['ship_id']) ? "selected" : ""; ?>
                            <option value="<?= $ship['ship_id']; ?>" <?= $selected; ?>>
                                <?= htmlspecialchars($ship['ship_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?= $l->l_by_amount; ?>:</td>
                <td>
                    <input type="text" name="amount" class="form-control" size="20" maxlength="20">
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit" class="btn btn-primary"><?= $l->l_by_place; ?></button>
                    <button type="reset" class="btn btn-secondary"><?= $l->l_reset; ?></button>
                </td>
            </tr>
        </table>
        <input type="hidden" name="response" value="place">
    </form>
    <?php if (empty($self->bounties)): ?>
        <?= $l->l_by_nobounties; ?><br>
    <?php else: ?>
        <?= $l->l_by_moredetails; ?><br><br>

        <table class="table">
            <tr>
                <th><?= $l->l_by_bountyon; ?></th>
                <th><?= $l->l_amount; ?></th>
            </tr>

            <?php foreach ($self->bounties as $bounty): ?>
                <tr>
                    <td>
                        <a href="<?= route('bounty', 'bounty_on=' . $bounty['bounty_on']); ?>&response=display" class="text-decoration-none">
                            <?= htmlspecialchars($bounty['target']['ship_name']); ?>
                        </a>
                    </td>
                    <td><?= intval($bounty['total_bounty']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    <script type="text/javascript">
        bntForm('bntBountyForm');
    </script>
<?php endif; ?>
<?php if ($self->response == 'display'): ?> 
    <?php if (empty($self->bounty_details)): ?>
        <?= $l->l_by_nobounties; ?><br>
    <?php else: ?>
        <?= $l->l_by_bountyon . " " . $self->bounty_on['ship_name']; ?>

        <table class="table">
            <tr>
                <th><?= $l->l_amount; ?></th>
                <th><?= $l->l_by_placedby; ?></th>
                <th><?= $l->l_by_action; ?></th>
            </tr>

            <?php foreach ($self->bounty_details as $bounty): ?>
                <tr>
                    <td><?= $bounty['amount']; ?></td>

                    <td>
                        <?php if ($bounty['placed_by'] == 0): ?>
                            <?= $l->l_by_thefeds; ?>
                        <?php else: ?>
                            <?= $bounty['placer_info']['ship_name']; ?>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ($bounty['placed_by'] == $self->playerinfo['ship_id']): ?>
                            <form action="<?= route('bounty'); ?>" method="POST" id="bntBountyForm<?= $bounty['bounty_id']; ?>">
                                <input type="hidden" name="bid" value="<?= $bounty['bounty_id']; ?>"/>
                                <input type="hidden" name="response" value="cancel"/>
                                <button type="submit" class="btn btn-primary"><?= $l->l_by_cancel; ?></button>
                            </form>
                            <script type="text/javascript">
                                bntForm('bntBountyForm<?= $bounty['bounty_id']; ?>');
                            </script>
                        <?php else: ?>
                            &nbsp;
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
<?php endif; ?>
<?php include_footer(); ?>

<?php $self = BNT\Controller\BountyController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<?php if (empty($self->response)): ?> 
    <form action="bounty.php" method="POST" id="bntBountyForm">
        <table class="table table-borderless w-auto">
            <tr>
                <td><?= $l->by_bountyon; ?></td>
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
                <td><?= $l->by_amount; ?>:</td>
                <td>
                    <input type="text" name="amount" class="form-control" size="20" maxlength="20">
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button type="submit" class="btn btn-primary"><?= $l->by_place; ?></button>
                    <button type="reset" class="btn btn-secondary"><?= $l->reset; ?></button>
                </td>
            </tr>
        </table>
        <input type="hidden" name="response" value="place">
    </form>
    <?php if (empty($self->bounties)): ?>
        <?= $l->by_nobounties; ?><br>
    <?php else: ?>
        <?= $l->by_moredetails; ?><br><br>

        <table class="table">
            <tr>
                <th><?= $l->by_bountyon; ?></th>
                <th><?= $l->amount; ?></th>
            </tr>

            <?php foreach ($self->bounties as $bounty): ?>
                <tr>
                    <td>
                        <a href="bounty.php?bounty_on=<?= $bounty['bounty_on']; ?>&response=display" class="text-decoration-none">
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
        <?= $l->by_nobounties; ?><br>
    <?php else: ?>
        <?= $l->by_bountyon . " " . $self->bounty_on['ship_name']; ?>

        <table class="table">
            <tr>
                <th><?= $l->amount; ?></th>
                <th><?= $l->by_placedby; ?></th>
                <th><?= $l->by_action; ?></th>
            </tr>

            <?php foreach ($self->bounty_details as $bounty): ?>
                <tr>
                    <td><?= $bounty['amount']; ?></td>

                    <td>
                        <?php if ($bounty['placed_by'] == 0): ?>
                            <?= $l->by_thefeds; ?>
                        <?php else: ?>
                            <?= $bounty['placer_info']['ship_name']; ?>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ($bounty['placed_by'] == $self->playerinfo['ship_id']): ?>
                            <form action="bounty.php" method="POST" id="bntBountyForm<?= $bounty['bounty_id']; ?>">
                                <input type="hidden" name="bid" value="<?= $bounty['bounty_id']; ?>"/>
                                <input type="hidden" name="response" value="cancel"/>
                                <button type="submit" class="btn btn-primary"><?= $l->by_cancel; ?></button>
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

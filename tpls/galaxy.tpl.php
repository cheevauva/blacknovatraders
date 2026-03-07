<?php $self = \BNT\Controller\GalaxyController::as($self); ?>
<?php
$tile = [
    'special' => "space261_md_blk.gif",
    'ore' => "space262_md_blk.gif",
    'organics' => "space263_md_blk.gif",
    'energy' => "space264_md_blk.gif",
    'goods' => "space265_md_blk.gif",
    'none' => "space.gif",
    'unknown' => "uspace.gif"
];
?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<table>
    <?php foreach (array_chunk($self->sectors, 50) as $chuckSectors): ?>
        <tr>
            <td><?= $cur_sector ?? 0; ?></td>
            <?php foreach ($chuckSectors as $cur_sector): ?> 
                <td>
                    <a href="<?= route('rsmove', 'engage=1&destination=' . $cur_sector); ?>">
                        <img src = "images/<?= $tile[$self->explored_map[$cur_sector] ?? 'unknown']; ?>">
                    </a>
                </td>
            <?php endforeach; ?>
            <td><?= $cur_sector ?? 0; ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<div class="legend border mt-3 p-3 rounded bg-light">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-2"><img src="images/<?= $tile['special']; ?>" class="me-2"> - Special Port</div>
            <div class="mb-2"><img src="images/<?= $tile['ore']; ?>" class="me-2"> - Ore Port</div>
            <div class="mb-2"><img src="images/<?= $tile['organics']; ?>" class="me-2"> - Organics Port</div>
            <div class="mb-2"><img src="images/<?= $tile['energy']; ?>" class="me-2"> - Energy Port</div>
        </div>
        <div class="col-md-6">
            <div class="mb-2"><img src="images/<?= $tile['goods']; ?>" class="me-2"> - Goods Port</div>
            <div class="mb-2"><img src="images/<?= $tile['none']; ?>" class="me-2"> - No Port</div>
            <div class="mb-2"><img src="images/<?= $tile['unknown']; ?>" class="me-2"> - Unexplored</div>
        </div>
    </div>
</div>

<?php include_footer(); ?>

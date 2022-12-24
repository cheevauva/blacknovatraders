<?php $calculator = BNT\Sector\Servant\SectorPortTradeWithShipServant::as($calculator); ?>
<?php bigtitle($l_title_trade); ?>
<FORM ACTION=port2.php METHOD=POST>
    <table>
        <tr>
            <td><?php echo $l_commodity; ?></td>
            <td><?php echo $l_buying; ?>/<?php echo $l_selling; ?></td>
            <td><?php echo $l_amount; ?></td>
            <td><?php echo $l_price; ?></td>
            <td><?php echo $l_buy; ?>/<?php echo $l_sell; ?></td>
            <td><?php echo $l_cargo; ?></td>
        </tr>
        <tr>
            <td><?php echo $l_ore; ?></td>
            <td><?php echo $sectorinfo->port_type == BNT\Sector\SectorPortTypeEnum::Ore ? $l_selling : $l_buying; ?></td>
            <td><?php echo NUMBER($sectorinfo->port_ore); ?></td>
            <td><?php echo $calculator->ore_price; ?></td>
            <td>
                <INPUT TYPE=TEXT NAME=trade_ore SIZE=10 MAXLENGTH=20 VALUE="<?php echo $calculator->ore_amount; ?>">
            </td>
            <td><?php echo NUMBER($playerinfo->ship_ore); ?></td>
        </tr>
        <tr>
            <td><?php echo $l_organics; ?></td>
            <td><?php echo $sectorinfo->port_type == BNT\Sector\SectorPortTypeEnum::Organics ? $l_selling : $l_buying; ?></td>
            <td><?php echo NUMBER($sectorinfo->port_organics); ?></td>
            <td><?php echo $calculator->organics_price; ?></td>
            <td><INPUT TYPE=TEXT NAME=trade_organics SIZE=10 MAXLENGTH=20 VALUE="<?php echo $calculator->organics_amount; ?>"></td>
            <td><?php echo NUMBER($playerinfo->ship_organics); ?></td>
        </tr>
        <tr>
            <td><?php echo $l_goods; ?></td>
            <td><?php echo $sectorinfo->port_type == BNT\Sector\SectorPortTypeEnum::Goods ? $l_selling : $l_buying; ?></td>
            <td><?php echo NUMBER($sectorinfo->port_goods); ?></td>
            <td><?php echo $calculator->goods_price; ?></td>
            <td><INPUT TYPE=TEXT NAME=trade_goods SIZE=10 MAXLENGTH=20 VALUE="<?php echo $calculator->goods_amount; ?>"></td>
            <td><?php echo NUMBER($playerinfo->ship_goods); ?></td>
        </tr>
        <tr>
            <td><?php echo $l_energy; ?></td>
            <td><?php echo $sectorinfo->port_type == BNT\Sector\SectorPortTypeEnum::Energy ? $l_selling : $l_buying; ?></td>
            <td><?php echo NUMBER($sectorinfo->port_energy); ?></td>
            <td><?php echo $calculator->energy_price; ?></td>
            <td><INPUT TYPE=TEXT NAME=trade_energy SIZE=10 MAXLENGTH=20 VALUE="<?php echo $calculator->energy_amount; ?>"></td>
            <td><?php echo NUMBER($playerinfo->ship_energy); ?></td>
        </tr>
    </table>
    <INPUT TYPE=SUBMIT VALUE="<?php echo $l_trade; ?>">
</FORM>
<?php
echo strtr($l_trade_st_info, [
    '[free_holds]' => NUMBER($playerinfo->getFreeHolds()),
    '[free_power]' => NUMBER($playerinfo->getFreePower()),
    '[credits]' => NUMBER($playerinfo->credits),
]);
?>

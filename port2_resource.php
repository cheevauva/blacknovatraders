<?php $offerResource = \BNT\Sector\Servant\SectorPortResourceOfferServant::as($offerResource); ?>
<TABLE >
    <TR>
        <TD colspan=2><?php echo $l_trade_result; ?></TD>
    </TR>
    <TR>
        <TD colspan=2>
            <?php echo ($offerResource->total_cost < 0 ? $l_profit : $l_cost); ?> 
            <?php echo NUMBER(abs($offerResource->total_cost)); ?> <?php echo $l_credits;?>
        </TD>
    </TR>
    <TR>
        <TD><?php echo $l_traded_ore; ?> </TD>
        <TD ><?php echo NUMBER($offerResource->trade_ore); ?></TD>
    </TR>
    <TR>
        <TD><?php echo $l_traded_organics; ?></TD>
        <TD> <?php echo NUMBER($offerResource->trade_organics); ?></TD>
    </TR>
    <TR>
        <TD><?php echo $l_traded_goods; ?></TD>
        <TD ><?php echo NUMBER($offerResource->trade_goods); ?></TD>
    </TR>
    <TR>
        <TD><?php echo $l_traded_energy; ?></TD>
        <TD ><?php echo NUMBER($offerResource->trade_energy); ?></TD>
    </TR>
</TABLE>



<?php echo $l_trade_complete; ?><br/>
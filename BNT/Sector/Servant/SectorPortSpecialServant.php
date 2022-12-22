<?php

declare(strict_types=1);

namespace BNT\Sector\Servant;

use BNT\ServantInterface;
class SectorPortSpecialServant implements ServantInterface
{
    //put your code here
    public function serve(): void
    {
            $title = $l_special_port;
    bigtitle();
    if (isLoanPending($playerinfo->ship_id)) {
        $l_port_loannotrade<p>";
        <A HREF=igb.php>$l_igb_term</a><p>";
        TEXT_GOTOMAIN();
        include("footer.php");
        die();
    }

    $res2 = $db->Execute("SELECT SUM(amount) as total_bounty FROM $dbtables[bounty] WHERE placed_by = 0 AND bounty_on = $playerinfo->ship_id]");
    if ($res2) {
        $bty = $res2->fields;
        if ($bty[total_bounty] > 0) {
            if ($pay <> 1) {
                echo $l_port_bounty;
                $l_port_bounty2 = str_replace("[amount]", NUMBER($bty->total_bounty), $l_port_bounty2);
                echo $l_port_bounty2 . "<BR>";
                <A HREF=\"bounty.php\">$l_by_placebounty</A><BR><BR>";
                TEXT_GOTOMAIN();
                die();
            } else {
                if ($playerinfo->credits < $bty->total_bounty) {
                    $l_port_btynotenough = str_replace("[amount]", NUMBER($bty->total_bounty, $l_port_btynotenough));
                    echo $l_port_btynotenough;
                    TEXT_GOTOMAIN();
                    die();
                } else {
                    $db->Execute("UPDATE $dbtables[ships] SET credits=credits-$bty[total_bounty] WHERE ship_id = $playerinfo->ship_id]");
                    $db->Execute("DELETE from $dbtables[bounty] WHERE bounty_on = $playerinfo->ship_id] AND placed_by = 0");
                    echo $l_port_bountypaid;
                    die();
                }
            }
        }
    }
    $emerwarp_free = $max_emerwarp - $playerinfo->dev_emerwarp;
    $fighter_max = NUM_FIGHTERS($playerinfo->computer);
    $fighter_free = $fighter_max - $playerinfo->ship_fighters;
    $torpedo_max = NUM_TORPEDOES($playerinfo->torp_launchers);
    $torpedo_free = $torpedo_max - $playerinfo->torps;
    $armor_max = NUM_ARMOUR($playerinfo->armor);
    $armor_free = $armor_max - $playerinfo->armor_pts;
    $colonist_max = NUM_HOLDS($playerinfo->hull) - $playerinfo->ship_ore - $playerinfo->ship_organics - $playerinfo->ship_goods;
    $colonist_free = $colonist_max - $playerinfo->ship_colonists;

    <P>\n";
    $l_creds_to_spend = str_replace("[credits]", NUMBER($playerinfo->credits), $l_creds_to_spend);
    $l_creds_to_spend<BR>\n";

    if ($allow_ibank) {
        $igblink = "\n<A HREF=igb.php>$l_igb_term</a>";
        $l_ifyouneedmore = str_replace("[igb]", $igblink, $l_ifyouneedmore);

        $l_ifyouneedmore<BR>";
    }
    }

}

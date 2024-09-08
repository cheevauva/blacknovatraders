<?php

declare(strict_types=1);

namespace BNT\Servant;

use BNT\Enum\CalledFromEnum;
use BNT\ServantInterface;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;
use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Ship\Servant\ShipKillServant;
use BNT\Ship\Servant\ShipDestroyServant;
use BNT\Bounty\Servant\BountyCancelServant;
use BNT\DTO\SectorFightersDTO;

class SectorFightersSevant implements ServantInterface
{

    public Ship $ship;
    public int $sector_id;
    public CalledFromEnum $calledFrom;
    public int $ok = 1;
    public SectorFightersDTO $result;

    public function serve(): void
    {
        global $energyscooped;
        global $level_factor;
        global $torp_dmg_rate;
        global $l_sf_torphit;
        global $l_sf_fighthit;
        global $l_sf_lostfight;
        global $l_sf_lostfight2;
        global $l_sf_armorbreach;
        global $l_sf_armorbreach2;
        global $l_sf_destfightb;
        global $l_sf_destfightall;
        global $l_sf_escape;

        $this->result = new SectorFightersDTO;

        $retrieveSectorDefences = new SectorDefenceRetrieveManyByCriteriaDAO;
        $retrieveSectorDefences->sector_id = $this->sector_id;
        $retrieveSectorDefences->defence_type = SectorDefenceTypeEnum::Fighters;
        $retrieveSectorDefences->orderByQuantityDESC = true;
        $retrieveSectorDefences->serve();

        $totalSectorFighters = 0;

        foreach ($retrieveSectorDefences->defences as $defence) {
            $defence = SectorDefence::as($defence);

            $totalSectorFighters += $defence->quantity;
        }

        $targetfighters = $totalSectorFighters;

        if ($this->calledFrom === static::CALLED_FROM_RSMOVE) {
            $this->ship->ship_energy += $energyscooped;
        }

        $playerbeams = NUM_BEAMS($this->ship->beams);

        if ($playerbeams > $this->ship->ship_energy) {
            $playerbeams = $this->ship->ship_energy;
        }

        $this->ship->ship_energy = $this->ship->ship_energy - $playerbeams;

        $playershields = NUM_SHIELDS($this->ship->shields);

        if ($playershields > $this->ship->shields) {
            $playershields = $this->ship->shields;
        }
        //$this->ship->shields = $this->ship->shields - $playershields;

        $playertorpnum = round(mypw($level_factor, $this->ship->torp_launchers)) * 2;

        if ($playertorpnum > $this->ship->torp) {
            $playertorpnum = $this->ship->torp;
        }

        $playertorpdmg = $torp_dmg_rate * $playertorpnum;
        $playerarmor = $this->ship->armor_pts;
        $playerfighters = $this->ship->ship_fighters;

        if ($targetfighters > 0 && $playerbeams > 0) {
            if ($playerbeams > round($targetfighters / 2)) {
                $temp = round($targetfighters / 2);
                $lost = $targetfighters - $temp;
                $l_sf_destfight = str_replace("[lost]", $lost, $l_sf_destfight);
                echo $l_sf_destfight;
                $targetfighters = $temp;
                $playerbeams = $playerbeams - $lost;
            } else {
                $targetfighters = $targetfighters - $playerbeams;
                $l_sf_destfightb = str_replace("[lost]", $playerbeams, $l_sf_destfightb);
                echo $l_sf_destfightb;

                $playerbeams = 0;
            }
        }

        echo "<BR>$l_sf_torphit<BR>";
        if ($targetfighters > 0 && $playertorpdmg > 0) {
            if ($playertorpdmg > round($targetfighters / 2)) {
                $temp = round($targetfighters / 2);
                $lost = $targetfighters - $temp;
                $l_sf_destfightt = str_replace("[lost]", $lost, $l_sf_destfightt);
                echo $l_sf_destfightt;
                $targetfighters = $temp;
                $playertorpdmg = $playertorpdmg - $lost;
            } else {
                $targetfighters = $targetfighters - $playertorpdmg;
                $l_sf_destfightt = str_replace("[lost]", $playertorpdmg, $l_sf_destfightt);
                echo $l_sf_destfightt;
                $playertorpdmg = 0;
            }
        }

        echo "<BR>$l_sf_fighthit<BR>";
        if ($playerfighters > 0 && $targetfighters > 0) {
            if ($playerfighters > $targetfighters) {
                echo $l_sf_destfightall;
                $temptargfighters = 0;
            } else {
                $l_sf_destfightt2 = str_replace("[lost]", $playerfighters, $l_sf_destfightt2);
                echo $l_sf_destfightt2;
                $temptargfighters = $targetfighters - $playerfighters;
            }
            if ($targetfighters > $playerfighters) {
                echo $l_sf_lostfight;
                $tempplayfighters = 0;
            } else {
                $l_sf_lostfight2 = str_replace("[lost]", $targetfighters, $l_sf_lostfight2);
                echo $l_sf_lostfight2;
                $tempplayfighters = $playerfighters - $targetfighters;
            }
            $playerfighters = $tempplayfighters;
            $targetfighters = $temptargfighters;
        }
        if ($targetfighters > 0) {
            if ($targetfighters > $playerarmor) {
                $playerarmor = 0;
                echo $l_sf_armorbreach;
            } else {
                $playerarmor = $playerarmor - $targetfighters;
                $l_sf_armorbreach2 = str_replace("[lost]", $targetfighters, $l_sf_armorbreach2);
                echo $l_sf_armorbreach2;
            }
        }

        $fighterslost = $totalSectorFighters - $targetfighters;
        destroy_fighters($sector, $fighterslost);

        $l_sf_sendlog = str_replace("[player]", $playerinfo[character_name], $l_sf_sendlog);
        $l_sf_sendlog = str_replace("[lost]", $fighterslost, $l_sf_sendlog);
        $l_sf_sendlog = str_replace("[sector]", $sector, $l_sf_sendlog);

        message_defence_owner($sector, $l_sf_sendlog);
        playerlog($playerinfo[ship_id], LOG_DEFS_DESTROYED_F, "$fighterslost|$sector");
        $armor_lost = $playerinfo[armor_pts] - $playerarmor;
        $fighters_lost = $playerinfo[ship_fighters] - $playerfighters;
        $energy = $playerinfo[ship_energy];
        $update4b = $db->Execute("UPDATE $dbtables[ships] SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, armor_pts=armor_pts-$armor_lost, torps=torps-$playertorpnum WHERE ship_id=$playerinfo[ship_id]");
        $l_sf_lreport = str_replace("[armor]", $armor_lost, $l_sf_lreport);
        $l_sf_lreport = str_replace("[fighters]", $fighters_lost, $l_sf_lreport);
        $l_sf_lreport = str_replace("[torps]", $playertorpnum, $l_sf_lreport);
        echo $l_sf_lreport;

        if ($playerarmor < 1) {
            $this->result->shipDestroyed = true;
            echo $l_sf_shipdestroyed;
            playerlog($playerinfo[ship_id], LOG_DEFS_KABOOM, "$sector|$playerinfo[dev_escapepod]");
            $l_sf_sendlog2 = str_replace("[player]", $playerinfo[character_name], $l_sf_sendlog2);
            $l_sf_sendlog2 = str_replace("[sector]", $sector, $l_sf_sendlog2);
            message_defence_owner($sector, $l_sf_sendlog2);

            if ($this->ship->dev_escapepod) {
                $this->result->hasEscapePod = true;
                $this->result->ok = 0;
            } else {
                $this->result->ok = 0;
            }

            ShipDestroyServant::call($this->ship);
            return;
        }

        $this->result->ok = $targetfighters > 0 ? 0 : 2;
    }

}

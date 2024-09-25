<?php

declare(strict_types=1);

namespace BNT\Servant;

use BNT\ServantInterface;
use BNT\Planet\DAO\PlanetRetrieveManyByCriteriaDAO;
use BNT\Ship\DAO\ShipRetrieveByIdDAO;
use BNT\Planet\Entity\Planet;
use BNT\DTO\CalcOwnershipDTO;
use BNT\Sector\Entity\Sector;
use BNT\Sector\DAO\SectorRetrieveByIdDAO;
use BNT\Ship\DAO\ShipRetrieveManyByCriteriaDAO;

class CalcOwnershipServant implements ServantInterface
{

    public int $sector_id;
    public Sector $sector;
    public array $planets;
    public array $ownerTypes;
    public bool $doIt = true;

    public function serve(): void
    {
        $this->process();
        $this->doIt();
    }

    protected function prepareOwnerTypes(): array
    {
        $ownerTypes = [];

        foreach ($this->planets as $planet) {
            $planet = Planet::as($planet);

            foreach ($ownerTypes as $owner) {
                $owner = CalcOwnershipDTO::as($owner);

                if ($planet->corp != 0 && $owner->type == CalcOwnershipDTO::TYPE_CORP && $owner->id == $planet->corp) {
                    $owner->num++;
                    continue;
                }

                if ($planet->owner != 0 && $owner->type == CalcOwnershipDTO::TYPE_SHIP && $owner->id == $planet->owner) {
                    $owner->num++;
                    continue;
                }
            }

            if ($planet->corp != 0) {
                $ownerCorp = new CalcOwnershipDTO;
                $ownerCorp->id = $planet->owner;
                $ownerCorp->num = 1;
                $ownerCorp->type = CalcOwnershipDTO::TYPE_CORP;

                $ownerTypes[] = $ownerCorp;
            }

            if ($planet->owner != 0) {
                $ownerShip = new CalcOwnershipDTO;
                $ownerShip->id = $planet->owner;
                $ownerShip->num = 1;
                $ownerShip->type = CalcOwnershipDTO::TYPE_SHIP;

                $ownerTypes[] = $ownerShip;
            }
        }

        return $ownerTypes;
    }

    protected function process(): void
    {
        $this->sector = SectorRetrieveByIdDAO::call($this->sector_id);

        $retrievePlanet = new PlanetRetrieveManyByCriteriaDAO;
        $retrievePlanet->sector_id = $this->sector_id;
        $retrievePlanet->base = true;
        $retrievePlanet->serve();

        if ($retrievePlanet->planets) {
            return;
        }

        $this->planets = $retrievePlanet->planets;
        $this->ownerTypes = $this->prepareOwnerTypes();

        // We've got all the contenders with their bases.
        // Time to test for conflict

        $nbcorps = 0; // number corps
        $nbships = 0; // number ships
        $ships = [];
        $scorps = [];

        foreach ($this->ownerTypes as $owner) {
            $owner = CalcOwnershipDTO::as($owner);

            switch ($owner->type) {
                case CalcOwnershipDTO::TYPE_CORP:
                    $nbcorps++;
                    break;
                case CalcOwnershipDTO::TYPE_SHIP:
                    $currentShip = ShipRetrieveByIdDAO::call($owner->id);
                    $ships[] = $owner->id;
                    $scorps[] = $currentShip->team;
                    $owner->team = $currentShip->team;
                    $nbships++;
                    break;
            }
        }

        // More than one corp, war
        if ($nbcorps > 1) {
            $this->sector->zone_id = Sector::ZONE_ID_WAR;
            return;
        }


        // More than one unallied ship, war
        $numunallied = 0;

        foreach ($scorps as $corp) {
            if ($corp == 0) {
                $numunallied++;
            }
        }

        if ($numunallied > 1) {
            $this->sector->zone_id = Sector::ZONE_ID_WAR;
            return;
        }

        // Unallied ship, another corp present, war
        if ($numunallied > 0 && $nbcorps > 0) {
            $this->sector->zone_id = Sector::ZONE_ID_WAR;
            return;
        }

        if ($numunallied > 0) {
            $shipsWithTeam = new ShipRetrieveManyByCriteriaDAO;
            $shipsWithTeam->ships = $ships;
            $shipsWithTeam->excludeTeam = 0;
            $shipsWithTeam->limit = 1;
            $shipsWithTeam->serve();

            if (!empty($shipsWithTeam->ships)) {
                $this->sector->zone_id = Sector::ZONE_ID_WAR;
                return;
            }
        }
    }

    private function doIt(): void
    {
        if (!$this->doIt) {
            return;
        }
    }

    function calc_ownership($sector)
    {
        global $min_bases_to_own, $l_global_warzone, $l_global_nzone, $l_global_team, $l_global_player;
        global $db, $dbtables;

        $res = $db->Execute("SELECT owner, corp FROM $dbtables[planets] WHERE sector_id=$sector AND base='Y'");
        $num_bases = $res->RecordCount();

        $i = 0;
        if ($num_bases > 0) {

            while (!$res->EOF) {
                $bases[$i] = $res->fields;
                $i++;
                $res->MoveNext();
            }
        } else
            return "Sector ownership didn't change";

        $owner_num = 0;

        foreach ($bases as $curbase) {
            $curcorp = -1;
            $curship = -1;
            $loop = 0;
            while ($loop < $owner_num) {
                if ($curbase[corp] != 0) {
                    if ($owners[$loop][type] == 'C') {
                        if ($owners[$loop][id] == $curbase[corp]) {
                            $curcorp = $loop;
                            $owners[$loop][num]++;
                        }
                    }
                }

                if ($owners[$loop][type] == 'S') {
                    if ($owners[$loop][id] == $curbase[owner]) {
                        $curship = $loop;
                        $owners[$loop][num]++;
                    }
                }

                $loop++;
            }

            if ($curcorp == -1) {
                if ($curbase[corp] != 0) {
                    $curcorp = $owner_num;
                    $owner_num++;
                    $owners[$curcorp][type] = 'C';
                    $owners[$curcorp][num] = 1;
                    $owners[$curcorp][id] = $curbase[corp];
                }
            }

            if ($curship == -1) {
                if ($curbase[owner] != 0) {
                    $curship = $owner_num;
                    $owner_num++;
                    $owners[$curship][type] = 'S';
                    $owners[$curship][num] = 1;
                    $owners[$curship][id] = $curbase[owner];
                }
            }
        }

        // We've got all the contenders with their bases.
        // Time to test for conflict

        $loop = 0;
        $nbcorps = 0;
        $nbships = 0;
        while ($loop < $owner_num) {
            if ($owners[$loop][type] == 'C')
                $nbcorps++;
            else {
                $res = $db->Execute("SELECT team FROM $dbtables[ships] WHERE ship_id=" . $owners[$loop][id]);
                if ($res && $res->RecordCount() != 0) {
                    $curship = $res->fields;
                    $ships[$nbships] = $owners[$loop][id];
                    $scorps[$nbships] = $curship[team];
                    $nbships++;
                }
            }

            $loop++;
        }

        //More than one corp, war
        if ($nbcorps > 1) {
            $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
            return $l_global_warzone;
        }

        //More than one unallied ship, war
        $numunallied = 0;
        foreach ($scorps as $corp) {
            if ($corp == 0)
                $numunallied++;
        }
        if ($numunallied > 1) {
            $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
            return $l_global_warzone;
        }

        //Unallied ship, another corp present, war
        if ($numunallied > 0 && $nbcorps > 0) {
            $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
            return $l_global_warzone;
        }

        //Unallied ship, another ship in a corp, war
        if ($numunallied > 0) {
            $query = "SELECT team FROM $dbtables[ships] WHERE (";
            $i = 0;
            foreach ($ships as $ship) {
                $query = $query . "ship_id=$ship";
                $i++;
                if ($i != $nbships)
                    $query = $query . " OR ";
                else
                    $query = $query . ")";
            }
            $query = $query . " AND team!=0";
            $res = $db->Execute($query);
            if ($res->RecordCount() != 0) {
                $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
                return $l_global_warzone;
            }
        }


        //Ok, all bases are allied at this point. Let's make a winner.
        $winner = 0;
        $i = 1;
        while ($i < $owner_num) {
            if ($owners[$i][num] > $owners[$winner][num])
                $winner = $i;
            elseif ($owners[$i][num] == $owners[$winner][num]) {
                if ($owners[$i][type] == 'C')
                    $winner = $i;
            }
            $i++;
        }

        if ($owners[$winner][num] < $min_bases_to_own) {
            $db->Execute("UPDATE $dbtables[universe] SET zone_id=1 WHERE sector_id=$sector");
            return $l_global_nzone;
        }


        if ($owners[$winner][type] == 'C') {
            $res = $db->Execute("SELECT zone_id FROM $dbtables[zones] WHERE corp_zone='Y' && owner=" . $owners[$winner][id]);
            $zone = $res->fields;

            $res = $db->Execute("SELECT team_name FROM $dbtables[teams] WHERE id=" . $owners[$winner][id]);
            $corp = $res->fields;

            $db->Execute("UPDATE $dbtables[universe] SET zone_id=$zone[zone_id] WHERE sector_id=$sector");
            return "$l_global_team $corp[team_name]!";
        } else {
            $onpar = 0;
            foreach ($owners as $curowner) {
                if ($curowner[type] == 'S' && $curowner[id] != $owners[$winner][id] && $curowner[num] == $owners[winners][num])
                    $onpar = 1;
                break;
            }

            //Two allies have the same number of bases
            if ($onpar == 1) {
                $db->Execute("UPDATE $dbtables[universe] SET zone_id=1 WHERE sector_id=$sector");
                return $l_global_nzone;
            } else {
                $res = $db->Execute("SELECT zone_id FROM $dbtables[zones] WHERE corp_zone='N' && owner=" . $owners[$winner][id]);
                $zone = $res->fields;

                $res = $db->Execute("SELECT character_name FROM $dbtables[ships] WHERE ship_id=" . $owners[$winner][id]);
                $ship = $res->fields;

                $db->Execute("UPDATE $dbtables[universe] SET zone_id=$zone[zone_id] WHERE sector_id=$sector");
                return "$l_global_player $ship[character_name]!";
            }
        }
    }

}

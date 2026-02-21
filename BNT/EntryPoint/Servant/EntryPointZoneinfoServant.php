<?php

declare(strict_types=1);

namespace BNT\EntryPoint\Servant;

use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Zone\DAO\ZoneByIdDAO;
use BNT\Team\DAO\TeamByIdDAO;

class EntryPointZoneinfoServant extends \UUA\Servant
{

    public array $playerinfo;
    public int $zone;
    public ?array $zoneinfo;
    public ?string $ownername;
    public ?array $ownerinfo;
    public bool $isAllowChangeZone;
    public $hull;

    #[\Override]
    public function serve(): void
    {
        global $l_zname;
        global $l_zi_feds;
        global $l_zi_traders;
        global $l_zi_nobody;
        global $l_zi_war;

        $this->zoneinfo = ZoneByIdDAO::call($this->container, $this->zone)->zone;

        if (empty($this->zoneinfo)) {
            return;
        }

        if ($this->zoneinfo['zone_id'] < 5) {
            $this->zoneinfo['zone_name'] = $l_zname[$this->zoneinfo['zone_id']];
        }

        if ($this->zoneinfo['zone_id'] == 2) {
            $this->ownername = $l_zi_feds;
        } elseif ($this->zoneinfo['zone_id'] == 3) {
            $this->ownername = $l_zi_traders;
        } elseif ($this->zoneinfo['zone_id'] == 1) {
            $this->ownername = $l_zi_nobody;
        } elseif ($this->zoneinfo['zone_id'] == 4) {
            $this->ownername = $l_zi_war;
        } else {
            if ($this->zoneinfo['corp_zone'] == 'N') {
                $this->ownerinfo = ShipByIdDAO::call($this->container, $this->zoneinfo['owner'])->ship;
                $this->ownername = $this->ownerinfo['ship_name'] ?? null;
            } else {
                $this->ownerinfo = TeamByIdDAO::call($this->container, $this->zoneinfo['owner'])->team;
                $this->ownername = $this->ownerinfo['team_name'] ?? null;
            }
        }

        $this->isAllowChangeZone = $this->isAllowChangeZone();
    }

    protected function isAllowChangeZone(): bool
    {
        if ($this->zoneinfo['corp_zone'] == 'N' && $this->zoneinfo['owner'] == $this->playerinfo['ship_id']) {
            return true;
        }

        if ($this->zoneinfo['corp_zone'] == 'Y') {
            if ($this->zoneinfo['owner'] == $this->playerinfo['team'] && $this->playerinfo['ship_id'] == ($this->ownerinfo['creator'] ?? null)) {
                return true;
            }
        }

        return false;
    }
}

<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Zone\DAO\ZoneByIdDAO;
use BNT\Team\DAO\TeamByIdDAO;

class ZoneinfoController extends BaseController
{

    public ?array $zoneinfo = null;
    public bool $isAllowChangeZone = false;
    public string $ownername;
    public ?array $ownerinfo;
    public int $zone;
    public $hull;

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->zone = (int) $this->fromQueryParams('zone', 'zone ' . $this->l->is_required);
        $this->zoneinfo = ZoneByIdDAO::call($this->container, $this->zone)->zone;

        if (empty($this->zoneinfo)) {
            $this->render('tpls/zoneinfo.tpl.php');
            return;
        }

        if ($this->zoneinfo['zone_id'] < 5) {
            $this->zoneinfo['zone_name'] = $this->l->zname[$this->zoneinfo['zone_id']];
        }

        if ($this->zoneinfo['zone_id'] == 2) {
            $this->ownername = $this->l->zi_feds;
        } elseif ($this->zoneinfo['zone_id'] == 3) {
            $this->ownername = $this->l->zi_traders;
        } elseif ($this->zoneinfo['zone_id'] == 1) {
            $this->ownername = $this->l->zi_nobody;
        } elseif ($this->zoneinfo['zone_id'] == 4) {
            $this->ownername = $this->l->zi_war;
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
        $this->render('tpls/zoneinfo.tpl.php');
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

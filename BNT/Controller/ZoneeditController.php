<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Zone\DAO\ZoneUpdateDAO;
use BNT\Zone\DAO\ZoneByIdDAO;
use BNT\Exception\WarningException;
use BNT\Team\DAO\TeamByIdDAO;

class ZoneeditController extends BaseController
{

    public int $zone;
    public ?array $currentZone;
    public array $owner;

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->l->ze_title;
        $this->zone = (int) $this->fromQueryParams('zone', 'zone ' . $this->l->i_required);
        $this->currentZone = ZoneByIdDAO::call($this->container, $this->zone)->zone ?: throw new WarningException($this->l->zi_nexist);

        if ($this->currentZone['corp_zone'] == 'N') {
            $this->ownerinfo = $this->playerinfo;
        } else {
            $this->ownerinfo = TeamByIdDAO::call($this->container, $this->currentZone['owner'])->team;
        }

        if ($this->currentZone['corp_zone'] == 'N' && $this->currentZone['owner'] != $this->playerinfo['ship_id']) {
            throw new WarningException($this->l->ze_notowner);
        }

        if ($this->currentZone['corp_zone'] == 'Y' && $this->currentZone['owner'] != $this->playerinfo['id'] && $this->currentZone['owner'] == $this->playerinfo['creator']) {
            throw new WarningException($this->l->ze_notowner);
        }
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/zoneedit.tpl.php');
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        ZoneUpdateDAO::call($this->container, [
            'zone_name' => $this->fromParsedBody('name', 'name ' . $this->l->is_required),
            'allow_beacon' => $this->fromParsedBody('beacons', 'beacons ' . $this->l->is_required),
            'allow_attack' => $this->fromParsedBody('attacks', 'attacks ' . $this->l->is_required),
            'allow_warpedit' => $this->fromParsedBody('attacks', 'warpedits ' . $this->l->is_required),
            'allow_planet' => $this->fromParsedBody('planets', 'planets ' . $this->l->is_required),
            'allow_trade' => $this->fromParsedBody('trades', 'trades ' . $this->l->is_required),
            'allow_defenses' => $this->fromParsedBody('defenses', 'defenses ' . $this->l->is_required),
        ], $this->zone);

        $this->redirectTo('zoneinfo.php?zone=' . $this->zone);
    }
}

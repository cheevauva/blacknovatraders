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
        $this->zone = $this->fromQueryParams( 'zone')->notEmpty()->asInt();
        $this->currentZone = ZoneByIdDAO::call($this->container, $this->zone)->zone ?: throw new WarningException('l_zi_nexist');

        if ($this->currentZone['corp_zone'] == 'N') {
            $this->ownerinfo = $this->playerinfo;
        } else {
            $this->ownerinfo = TeamByIdDAO::call($this->container, $this->currentZone['owner'])->team;
        }

        if ($this->currentZone['corp_zone'] == 'N' && $this->currentZone['owner'] != $this->playerinfo['ship_id']) {
            throw new WarningException('l_ze_notowner');
        }

        if ($this->currentZone['corp_zone'] == 'Y' && $this->currentZone['owner'] != $this->playerinfo['ship_id'] && $this->currentZone['owner'] == $this->playerinfo['creator']) {
            throw new WarningException('l_ze_notowner');
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
            'zone_name' => $this->fromParsedBody('name')->trim()->notEmpty()->asString(),
            'allow_beacon' => $this->fromParsedBody('beacons')->enum(['Y', 'N', 'L'])->notEmpty()->asString(),
            'allow_attack' => $this->fromParsedBody('attacks')->enum(['Y', 'N'])->notEmpty()->asString(), 
            'allow_warpedit' => $this->fromParsedBody('warpedits')->enum(['Y', 'N', 'L'])->notEmpty()->asString(),
            'allow_planet' => $this->fromParsedBody('planets')->enum(['Y', 'N', 'L'])->notEmpty()->asString(), 
            'allow_trade' => $this->fromParsedBody('trades')->enum(['Y', 'N', 'L'])->notEmpty()->asString(), 
            'allow_defenses' => $this->fromParsedBody('defenses')->enum(['Y', 'N', 'L'])->notEmpty()->asString(), 
        ], $this->zone);

        $this->redirectTo('zoneinfo', [
            'zone' => $this->zone,
        ]);
    }
}

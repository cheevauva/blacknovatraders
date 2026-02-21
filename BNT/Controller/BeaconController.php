<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Sector\DAO\SectorUpdateDAO;
use BNT\Sector\Servant\SectorCheckAccessBeaconByShipServant;

class BeaconController extends BaseController
{

    public ?array $sectorinfo = null;

    #[\Override]
    protected function preProcess(): void
    {
        $this->checkTurns();

        $this->sectorinfo = SectorByIdDAO::call($this->container, $this->playerinfo['sector'])->sector;

        $checkAccess = SectorCheckAccessBeaconByShipServant::new($this->container);
        $checkAccess->ship = $this->playerinfo;
        $checkAccess->serve();
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/beacon.tpl.php');
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        global $l;

        SectorUpdateDAO::call($this->container, [
            'beacon' => (string) $this->fromParsedBody('beacon_text', $l->beacon_name . ' ' . $l->is_required),
        ], $this->playerinfo['sector']);

        $this->playerinfo['dev_beacon'] -= 1;
        $this->playerinfo['turns'] -= 1;

        $this->playerinfoUpdate();
        $this->redirectTo('beacon.php');
    }
}

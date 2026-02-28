<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Exception\ErrorException;

class AdminShipController extends BaseController
{

    public array $ship = [];
    public array $ships = [];
    public string $operation;

    #[\Override]
    protected function preProcess(): void
    {
        $this->isAdmin() ?: throw new ErrorException('You not admin');
        $this->operation = (string) $this->fromQueryParams('operation');
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        global $l;

        if ($this->operation === 'list') {
            $this->ships = db()->fetchAllKeyValue("SELECT ship_id, ship_name FROM ships ORDER BY ship_name");
            $this->render('tpls/admin/shiplist.tpl.php');
            return;
        }

        if ($this->operation === 'edit') {
            $ship = (int) $this->fromQueryParams('ship', 'ship ' . $l->is_required);
            $this->ship = ShipByIdDAO::call($this->container, $ship)->ship;
            $this->render('tpls/admin/shipedit.tpl.php');
            return;
        }

        throw new ErrorException('Not implemented');
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        global $l;

        if ($this->operation === 'save') {
            $ship = (int) $this->fromQueryParams('ship', 'ship ' . $l->is_required);

            $shipinfo = [
                'ship_name' => (string) fromPOST('ship_name', new \Exception('ship_name')),
                'ship_destroyed' => !fromPOST('ship_destroyed') ? 'N' : 'Y',
                'hull' => (int) fromPOST('hull', 0),
                'engines' => (int) fromPOST('engines', 0),
                'power' => (int) fromPOST('power', 0),
                'computer' => (int) fromPOST('computer', 0),
                'sensors' => (int) fromPOST('sensors', 0),
                'armor' => (int) fromPOST('armor', 0),
                'shields' => (int) fromPOST('shields', 0),
                'beams' => (int) fromPOST('beams', 0),
                'torp_launchers' => (int) fromPOST('torp_launchers', 0),
                'cloak' => (int) fromPOST('cloak', 0),
                'credits' => (int) fromPOST('credits', 0),
                'turns' => (int) fromPOST('turns', 0),
                'dev_warpedit' => (int) fromPOST('dev_warpedit'),
                'dev_genesis' => (int) fromPOST('dev_genesis'),
                'dev_beacon' => (int) fromPOST('dev_beacon'),
                'dev_emerwarp' => (int) fromPOST('dev_emerwarp'),
                'dev_escapepod' => !fromPOST('dev_escapepod') ? 'N' : 'Y',
                'dev_fuelscoop' => !fromPOST('dev_fuelscoop') ? 'N' : 'Y',
                'dev_minedeflector' => (int) fromPOST('dev_minedeflector'),
                'sector' => (int) fromPOST('sector'),
                'ship_ore' => (int) fromPOST('ship_ore'),
                'ship_organics' => (int) fromPOST('ship_organics'),
                'ship_goods' => (int) fromPOST('ship_goods'),
                'ship_energy' => (int) fromPOST('ship_energy'),
                'ship_colonists' => (int) fromPOST('ship_colonists'),
                'ship_fighters' => (int) fromPOST('ship_fighters'),
                'torps' => (int) fromPOST('torps'),
                'armor_pts' => (int) fromPOST('armor_pts'),
            ];

            ShipUpdateDAO::call($this->container, $shipinfo, $ship);
            $this->redirectTo('admin', [
                'module' => 'ship',
                'operation' => 'list',
            ]);
            return;
        }

        throw new ErrorException('Not implemented');
    }
}

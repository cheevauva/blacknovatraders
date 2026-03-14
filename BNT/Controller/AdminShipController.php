<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Ship\DAO\ShipsByCriteriaDAO;
use BNT\Exception\ErrorException;

class AdminShipController extends BaseController
{

    public array $ship = [];
    public array $ships = [];
    public string $operation;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->enableCheckShip = false;
    }

    #[\Override]
    protected function preProcess(): void
    {
        $this->isAdmin() ?: throw new ErrorException('You not admin');
        $this->operation = $this->fromQueryParams('operation')->enum(['list', 'edit', 'save'])->asString();

        if (in_array($this->operation, ['edit', 'save'], true)) {
            $ship = $this->fromQueryParams('ship')->notEmpty()->asInt();
            $this->ship = ShipByIdDAO::call($this->container, $ship)->ship ?: throw new ErrorException('l_not_found');
        }
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        if ($this->operation === 'list') {
            $this->ships = array_column(ShipsByCriteriaDAO::call($this->container)->ships, 'ship_name', 'ship_id');
            $this->render('tpls/admin/shiplist.tpl.php');
            return;
        }

        if ($this->operation === 'edit') {
            $this->render('tpls/admin/shipedit.tpl.php');
            return;
        }
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        if ($this->operation === 'save') {
            ShipUpdateDAO::call($this->container, [
                'ship_name' => $this->fromParsedBody('ship_name')->notEmpty()->asString(),
                'ship_destroyed' => !$this->fromParsedBody('ship_destroyed')->asBool() ? 'N' : 'Y',
                'hull' => $this->fromParsedBody('hull')->default(0)->asInt(),
                'engines' => $this->fromParsedBody('engines')->default(0)->asInt(),
                'power' => $this->fromParsedBody('power')->default(0)->asInt(),
                'computer' => $this->fromParsedBody('computer')->default(0)->asInt(),
                'sensors' => $this->fromParsedBody('sensors')->default(0)->asInt(),
                'armor' => $this->fromParsedBody('armor')->default(0)->asInt(),
                'shields' => $this->fromParsedBody('shields')->default(0)->asInt(),
                'beams' => $this->fromParsedBody('beams')->default(0)->asInt(),
                'torp_launchers' => $this->fromParsedBody('torp_launchers')->default(0)->asInt(),
                'cloak' => $this->fromParsedBody('cloak')->default(0)->asInt(),
                'credits' => $this->fromParsedBody('credits')->default(0)->asInt(),
                'turns' => $this->fromParsedBody('turns')->default(0)->asInt(),
                'dev_warpedit' => $this->fromParsedBody('dev_warpedit')->default(0)->asInt(),
                'dev_genesis' => $this->fromParsedBody('dev_genesis')->default(0)->asInt(),
                'dev_beacon' => $this->fromParsedBody('dev_beacon')->default(0)->asInt(),
                'dev_emerwarp' => $this->fromParsedBody('dev_emerwarp')->default(0)->asInt(),
                'dev_escapepod' => !$this->fromParsedBody('dev_escapepod')->asBool() ? 'N' : 'Y',
                'dev_fuelscoop' => !$this->fromParsedBody('dev_fuelscoop')->asBool() ? 'N' : 'Y',
                'dev_minedeflector' => $this->fromParsedBody('dev_minedeflector')->default(0)->asInt(),
                'sector' => $this->fromParsedBody('sector')->notEmpty()->asInt(),
                'ship_ore' => $this->fromParsedBody('ship_ore')->default(0)->asInt(),
                'ship_organics' => $this->fromParsedBody('ship_organics')->default(0)->asInt(),
                'ship_goods' => $this->fromParsedBody('ship_goods')->default(0)->asInt(),
                'ship_energy' => $this->fromParsedBody('ship_energy')->default(0)->asInt(),
                'ship_colonists' => $this->fromParsedBody('ship_colonists')->default(0)->asInt(),
                'ship_fighters' => $this->fromParsedBody('ship_fighters')->default(0)->asInt(),
                'torps' => $this->fromParsedBody('torps')->default(0)->asInt(),
                'armor_pts' => $this->fromParsedBody('armor_pts')->default(0)->asInt(),
            ], $this->ship['ship_id']);

            $this->redirectTo('admin', [
                'module' => 'ship',
                'operation' => 'list',
            ]);
        }
    }
}

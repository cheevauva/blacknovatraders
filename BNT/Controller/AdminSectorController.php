<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Sector\DAO\SectorUpdateDAO;
use BNT\Sector\DAO\SectorsByCriteriaDAO;
use BNT\Zone\DAO\ZonesByCriteriaDAO;
use BNT\Exception\ErrorException;

class AdminSectorController extends BaseController
{

    public array $sectors = [];
    public array $sector = [];
    public array $zones = [];
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
            $sectorId = $this->fromQueryParams('sector')->notEmpty()->asInt();
            $this->sector = SectorByIdDAO::call($this->container, $sectorId)->sector ?? throw new ErrorException('l_not_found');
        }
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        if ($this->operation === 'list') {
            $this->sectors = array_column(SectorsByCriteriaDAO::call($this->container)->sectors, 'sector_id', 'sector_id');
            $this->render('tpls/admin/sectorlist.tpl.php');
            return;
        }

        if ($this->operation === 'edit') {
            $this->zones = array_column(ZonesByCriteriaDAO::call($this->container)->zones, 'zone_name', 'zone_id');
            $this->render('tpls/admin/sectoredit.tpl.php');
            return;
        }

        parent::processGetAsHtml();
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        if ($this->operation === 'save') {
            SectorUpdateDAO::call($this->container, [
                'sector_name' => $this->fromParsedBody('sector_name')->asString(),
                'zone_id' => $this->fromParsedBody('zone_id')->notEmpty()->asInt(),
                'beacon' => $this->fromParsedBody('beacon')->asString(),
                'port_type' => $this->fromParsedBody('port_type')->enum(['', 'ore', 'organics', 'goods', 'energy'])->asString(),
                'port_organics' => $this->fromParsedBody('port_organics')->asInt(),
                'port_ore' => $this->fromParsedBody('port_ore')->asInt(),
                'port_goods' => $this->fromParsedBody('port_goods')->asInt(),
                'port_energy' => $this->fromParsedBody('port_energy')->asInt(),
                'distance' => $this->fromParsedBody('distance')->asInt(),
                'angle1' => $this->fromParsedBody('angle1')->notEmpty()->asFloat(),
                'angle2' => $this->fromParsedBody('angle2')->notEmpty()->asFloat(),
            ], $this->sector['sector_id']);

            $this->redirectTo('admin', [
                'module' => 'sector',
                'operation' => 'list',
            ]);
            return;
        }

        parent::processPostAsJson();
    }
}

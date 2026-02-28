<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Sector\DAO\SectorUpdateDAO;
use BNT\Exception\ErrorException;

class AdminSectorController extends BaseController
{

    public array $sectors = [];
    public array $sector = [];
    public array $zones = [];
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
        if ($this->operation === 'list') {
            $this->sectors = db()->fetchAllKeyValue("SELECT sector_id, sector_id  AS value FROM universe ORDER BY sector_id");
            $this->render('tpls/admin/sectorlist.tpl.php');
            return;
        }

        if ($this->operation === 'edit') {
            $sector = (int) $this->fromQueryParams('sector', 'sector ' . $this->l->is_required);

            $this->sector = SectorByIdDAO::call($this->container, $sector)->sector;
            $this->zones = db()->fetchAllKeyValue('SELECT zone_id, zone_name FROM zones ORDER BY zone_name');
            $this->render('tpls/admin/sectoredit.tpl.php');
            return;
        }

        parent::processGetAsHtml();
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        global $l;

        if ($this->operation === 'save') {
            $sector = (int) $this->fromQueryParams('sector', 'sector ' . $this->l->is_required);

            $this->sector = SectorByIdDAO::call($this->container, $sector)->sector;

            SectorUpdateDAO::call($this->container, [
                'sector_name' => (string) fromPOST('sector_name'),
                'zone_id' => (int) fromPOST('zone_id'),
                'beacon' => (string) fromPOST('beacon'),
                'port_type' => (string) fromPOST('port_type'),
                'port_organics' => (int) fromPOST('port_organics'),
                'port_ore' => (int) fromPOST('port_ore'),
                'port_goods' => (int) fromPOST('port_goods'),
                'port_energy' => (int) fromPOST('port_energy'),
                'distance' => (int) fromPOST('distance'),
                'angle1' => (float) fromPOST('angle1'),
                'angle2' => (float) fromPOST('angle2'),
            ], $sector);
            $this->redirectTo('admin', [
                'module' => 'sector',
                'operation' => 'list',
            ]);
            return;
        }

        parent::processPostAsJson();
    }
}

<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Planet\DAO\PlanetByIdDAO;
use BNT\Planet\DAO\PlanetUpdateDAO;

class AdminPlanetController extends BaseController
{

    public string $operation;
    public array $ships;
    public array $planet;
    public array $planets;

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

        if ($this->operation === 'edit') {
            $planet = (int) $this->fromQueryParams('planet', 'planet ' . $l->is_required);

            $this->planet = PlanetByIdDAO::call($this->container, $planet)->planet;
            $this->ships = db()->fetchAllKeyValue("SELECT ship_id, ship_name FROM ships ORDER BY ship_name");
            $this->render('tpls/admin/planetedit.tpl.php');
            return;
        }

        if ($this->operation === 'list') {
            $this->planets = db()->fetchAllKeyValue("SELECT planet_id, CONCAT_WS(' in ', name, sector_id) FROM planets ORDER BY sector_id");
            $this->render('tpls/admin/planetlist.tpl.php');
            return;
        }
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        global $l;

        if ($this->operation === 'save') {
            $planet = (int) $this->fromQueryParams('planet', 'planet ' . $l->is_required);

            PlanetUpdateDAO::call($this->container, [
                'sector_id' => (int) fromPOST('sector_id'),
                'defeated' => !fromPOST('defeated') ? 'N' : 'Y',
                'name' => (string) fromPOST('name'),
                'base' => !fromPOST('base') ? 'N' : 'Y',
                'sells' => !fromPOST('sells') ? 'N' : 'Y',
                'owner' => (string) fromPOST('owner'),
                'organics' => (int) fromPOST('organics', 0),
                'ore' => (int) fromPOST('ore', 0),
                'goods' => (int) fromPOST('goods', 0),
                'energy' => (int) fromPOST('energy', 0),
                'corp' => (string) fromPOST('corp'),
                'colonists' => (int) fromPOST('colonists', 0),
                'credits' => (int) fromPOST('credits', 0),
                'fighters' => (int) fromPOST('fighters', 0),
                'torps' => (int) fromPOST('torps', 0),
                'prod_organics' => (int) fromPOST('prod_organics', 0),
                'prod_ore' => (int) fromPOST('prod_ore', 0),
                'prod_goods' => (int) fromPOST('prod_goods', 0),
                'prod_energy' => (int) fromPOST('prod_energy', 0),
                'prod_fighters' => (int) fromPOST('prod_fighters', 0),
                'prod_torp' => (int) fromPOST('prod_torp', 0)
            ], $planet);
            $this->redirectTo('admin', [
                'module' => 'planet',
                'operation' => 'list',
            ]);
            return;
        }
        parent::processPostAsJson();
    }
}

<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Planet\DAO\PlanetByIdDAO;
use BNT\Planet\DAO\PlanetUpdateDAO;
use BNT\Ship\DAO\ShipsByCriteriaDAO;

// @todo replace sql to dao
class AdminPlanetController extends BaseController
{

    public string $operation;
    public array $ships;
    public array $planet;
    public array $planets;

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
        $this->operation = $this->fromQueryParams('operation')->trim()->enum(['edit', 'list', 'save'])->asString();
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        if ($this->operation === 'edit') {
            $planet = $this->fromQueryParams('planet')->notEmpty()->asInt();

            $this->planet = PlanetByIdDAO::call($this->container, $planet)->planet;
            $this->ships = array_column(ShipsByCriteriaDAO::call($this->container)->ships, 'ship_name', 'ship_id');
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
        if ($this->operation === 'save') {
            $planet = $this->fromQueryParams('planet')->notEmpty()->asInt();

            PlanetUpdateDAO::call($this->container, [
                'sector_id' => $this->fromParsedBody('sector_id')->notEmpty()->asInt(),
                'defeated' => !$this->fromParsedBody('defeated')->asBool() ? 'N' : 'Y',
                'name' => $this->fromParsedBody('name')->asString(),
                'base' => !$this->fromParsedBody('base')->asBool() ? 'N' : 'Y',
                'sells' => !$this->fromParsedBody('sells')->asBool() ? 'N' : 'Y',
                'owner' => $this->fromParsedBody('owner')->default(0)->asInt(),
                'organics' => $this->fromParsedBody('organics')->default(0)->asInt(),
                'ore' => $this->fromParsedBody('ore')->default(0)->asInt(),
                'goods' => $this->fromParsedBody('goods')->default(0)->asInt(),
                'energy' => $this->fromParsedBody('energy')->default(0)->asInt(),
                'corp' => $this->fromParsedBody('corp')->default(0)->asInt(),
                'colonists' => $this->fromParsedBody('colonists')->default(0)->asInt(),
                'credits' => $this->fromParsedBody('credits')->default(0)->asInt(),
                'fighters' => $this->fromParsedBody('fighters')->default(0)->asInt(),
                'torps' => $this->fromParsedBody('torps')->default(0)->asInt(),
                'prod_organics' => $this->fromParsedBody('prod_organics')->default(0)->asInt(),
                'prod_ore' => $this->fromParsedBody('prod_ore')->default(0)->asInt(),
                'prod_goods' => $this->fromParsedBody('prod_goods')->default(0)->asInt(),
                'prod_energy' => $this->fromParsedBody('prod_energy')->default(0)->asInt(),
                'prod_fighters' => $this->fromParsedBody('prod_fighters')->default(0)->asInt(),
                'prod_torp' => $this->fromParsedBody('prod_torp')->default(0)->asInt()
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

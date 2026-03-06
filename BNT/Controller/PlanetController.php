<?php

declare(strict_types=1);

namespace BNT\Controller;

use Exception;
use BNT\Planet\DAO\PlanetByIdDAO;

class PlanetController extends BaseController
{

    public ?array $planet = null;
    public int $planetId = 0;

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->planetId = $this->fromQueryParams('planet_id')->notEmpty()->asInt();
        $this->planet = PlanetByIdDAO::call($this->container, $this->planetId)->planet ?: throw new Exception('l_planet_none');

        $this->render('tpls/planet/planet.tpl.php');
    }
}

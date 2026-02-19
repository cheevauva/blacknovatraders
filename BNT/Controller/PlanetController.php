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
    protected function processGet(): void
    {
        global $l_planet_none;
        global $l_is_required;

        try {
            $this->planetId = intval($this->queryParams['planet_id'] ?? 0) ?: throw new Exception('planet_id ' . $l_is_required);
            
            $this->planet = PlanetByIdDAO::call($this->container, $this->planetId)->planet;

            if (empty($this->playerinfo)) {
                throw new Exception($l_planet_none);
            }
            
            $this->render('tpls/planet/planet.tpl.php');
        } catch (Exception $ex) {
            $this->exception = $ex;
            $this->render('tpls/error.tpl.php');
        }
    }
}

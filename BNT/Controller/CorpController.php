<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Planet\DAO\PlanetByIdDAO;
use BNT\Exception\WarningException;
use BNT\Exception\SuccessException;
use BNT\Game\Servant\GameCalcOwnershipServant;
use BNT\Planet\DAO\PlanetUpdateDAO;
use BNT\Ship\DAO\ShipsKickOthersFromPlanetDAO;

class CorpController extends BaseController
{

    public int $planetId;
    public array $planet = [];

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->l->corpm_title;
        $this->planetId = (int) $this->fromQueryParams('planet_id', 'planet_id ' . $this->l->is_required);
        $this->planet = PlanetByIdDAO::call($this->container, $this->planetId)->planet;

        $hasAccess = $this->planet['owner'] == $this->playerinfo['ship_id'] || ($this->planet['corp'] == $this->playerinfo['team'] && !empty($this->playerinfo['team']));

        if (!$hasAccess) {
            throw new WarningException($this->l->corpm_exploit);
        }
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $action = $this->fromQueryParams('action', 'action ' . $this->l->is_required);

        if ($action == 'planetcorp') {
            PlanetUpdateDAO::call($this->container, [
                'corp' => $this->playerinfo['team'],
                'owner' => $this->playerinfo['ship_id'],
            ], $this->planetId);
            GameCalcOwnershipServant::call($this->container, $this->playerinfo['sector']);

            throw new SuccessException($this->l->corpm_tocorp);
        }

        if ($action == 'planetpersonal') {
            PlanetUpdateDAO::call($this->container, [
                'corp' => 0,
                'owner' => $this->playerinfo['ship_id'],
            ], $this->planetId);
            GameCalcOwnershipServant::call($this->container, $this->playerinfo['sector']);

            $kickOthers = ShipsKickOthersFromPlanetDAO::new($this->container);
            $kickOthers->planet = $this->planetId;
            $kickOthers->ship = $this->playerinfo['ship_id'];
            $kickOthers->serve();

            throw new SuccessException($this->l->corpm_topersonal);
        }
        
        $this->redirectTo('planet', [
            'planet_id' => $this->planetId,
        ]);
    }

}

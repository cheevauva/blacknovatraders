<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Sector\Exception\SectorFightException;
use BNT\Sector\Exception\SectorRetreatException;
use BNT\Sector\Exception\SectorNotEnoghtCreditsTollException;
use BNT\Sector\Exception\SectorChooseMoveException;
use BNT\Exception\ErrorException;
use BNT\SectorDefence\DAO\SectorDefencesCleanUpDAO;
use BNT\SectorDefence\DAO\SectorDefencesByCriteriaDAO;
use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;

class GameCheckFightersServant extends \UUA\Servant
{

    public string $calledFrom;
    public int $sector;
    public ?string $response;
    public protected(set) int $totalSectorFighters;
    public array $playerinfo;

    #[\Override]
    public function serve(): void
    {
        global $fighter_price;

        $defencesBySector = SectorDefencesByCriteriaDAO::call($this->container, [
            'sector_id' => $this->sector,
            'defence_type' => 'F',
        ])->defences;

        $this->totalSectorFighters = 0;
        $owner = true;
        $fightersOwnerId = null;

        foreach ($defencesBySector as $defence) {
            $this->totalSectorFighters += $defence['quantity'];

            if ($defence['ship_id'] != $this->playerinfo['ship_id']) {
                $owner = false;
                $fightersOwnerId ??= $defence['ship_id'];
            }
        }

        $isProblem = !empty($defencesBySector) && !empty($this->totalSectorFighters) && !$owner;

        if (!$isProblem) {
            return;
        }

        // find out if the fighter owner and player are on the same team
        // All sector defences must be owned by members of the same team
        $fightersOwner = ShipByIdDAO::call($this->container, $fightersOwnerId)->ship;
        $isProblem2 = $fightersOwner['team'] != $this->playerinfo['team'] || empty($this->playerinfo['team']);

        if (!$isProblem2) {
            return;
        }

        switch ($this->response) {
            case 'fight':
                $this->playerinfo['cleared_defences'] = '';
                $this->playerinfoUpdate();

                throw new SectorFightException();
            case 'retreat':
                $this->playerinfo['cleared_defences'] = '';
                $this->playerinfo['turns'] -= 2;
                $this->playerinfo['turns_used'] += 2;
                $this->playerinfo['sector'] = $this->playerinfo['sector'];
                $this->playerinfoUpdate();

                throw new SectorRetreatException('l_chf_youretreatback');
            case 'pay':
                $this->playerinfo['cleared_defences'] = '';
                $this->playerinfoUpdate();

                $fightersToll = intval($this->totalSectorFighters * $fighter_price * 0.6);

                if ($this->playerinfo['credits'] < $fightersToll) {
                    $this->playerinfo['sector'] = $this->playerinfo['sector'];
                    $this->playerinfoUpdate();

                    throw new SectorNotEnoghtCreditsTollException()->t(['l_chf_notenoughcreditstoll', 'l_chf_movefailed']);
                }

                $this->playerinfo['credits'] -= $fightersToll;
                $this->playerinfoUpdate();

                $distributeToll = GameDistributeTollServant::new($this->container);
                $distributeToll->sector = $this->sector;
                $distributeToll->toll = $fightersToll;
                $distributeToll->totalFighters = $this->totalSectorFighters;
                $distributeToll->serve();

                LogPlayerDAO::call($this->container, $this->playerinfo['ship_id'], LogTypeConstants::LOG_TOLL_PAID, [$tollstring, $sector]);
                break;
            case 'sneak':
                $this->playerinfo['cleared_defences'] = '';
                $this->playerinfoUpdate();

                if (rand(1, 100) < sensorsCloakSuccess($fightersOwner['sensors'], $this->playerinfo['cloak'])) {
                    throw new SectorFightException('l_chf_thefightersdetectyou');
                }
                break;
            default:
                $fightersToll = $this->totalSectorFighters * $fighter_price * 0.6;

                $this->playerinfo['cleared_defences'] = $this->calledFrom;
                $this->playerinfoUpdate();

                throw new SectorChooseMoveException();
        }

        SectorDefencesCleanUpDAO::call($this->container);
    }

    protected function playerinfoUpdate(): void
    {
        ShipUpdateDAO::call($this->container, $this->playerinfo, $this->playerinfo['ship_id']);
    }
}

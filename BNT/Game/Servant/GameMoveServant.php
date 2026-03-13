<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Game\Servant\GameSectorFightersServant;
use BNT\SectorDefence\DAO\SectorDefencesCleanUpDAO;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;

class GameMoveServant extends \UUA\Servant
{

    public array $fightersOwner;
    public string $response;
    public int $sector;
    public array $playerinfo;
    public int $totalSectorFighters;
    public protected(set) array $messages = [];
    public protected(set) bool $ok = true;

    #[\Override]
    public function serve(): void
    {
        global $fighter_price;

        $this->playerinfo['cleared_defences'] = '';
        $this->playerinfoUpdate();

        switch ($this->response) {
            case 'fight':
                $sectorFighters = GameSectorFightersServant::new($this->container);
                $sectorFighters->sector = $this->sector;
                $sectorFighters->totalSectorFighters = $this->totalSectorFighters;
                $sectorFighters->playerinfo = $this->playerinfo;
                $sectorFighters->serve();

                $this->messages = $sectorFighters->messages;
                break;
            case 'retreat':
                $this->playerinfo['turns'] -= 2;
                $this->playerinfo['turns_used'] += 2;
                $this->playerinfo['sector'] = $this->playerinfo['sector'];
                $this->playerinfoUpdate();
                break;
            case 'pay':
                $fightersToll = intval($this->totalSectorFighters * $fighter_price * 0.6);

                if ($this->playerinfo['credits'] < $fightersToll) {
                    $this->playerinfo['sector'] = $this->playerinfo['sector'];
                    $this->playerinfoUpdate();

                    $this->ok = false;
                    return;
                }

                $this->playerinfo['credits'] -= $fightersToll;
                $this->playerinfoUpdate();

                $distributeToll = GameDistributeTollServant::new($this->container);
                $distributeToll->sector = $this->sector;
                $distributeToll->toll = $fightersToll;
                $distributeToll->totalFighters = $this->totalSectorFighters;
                $distributeToll->serve();

                LogPlayerDAO::call($this->container, $this->playerinfo['ship_id'], LogTypeConstants::LOG_TOLL_PAID, [$fightersToll, $this->sector]);
                break;
            case 'sneak':
                if (rand(1, 100) <= sensorsCloakSuccess($this->fightersOwner['sensors'], $this->playerinfo['cloak'])) {
                    $this->messages[] = $this->t('l_chf_thefightersdetectyou');

                    $sectorFighters = GameSectorFightersServant::new($this->container);
                    $sectorFighters->playerinfo = $this->playerinfo;
                    $sectorFighters->serve();

                    $this->messages = array_merge($this->messages, $sectorFighters->messages);
                }
                break;
            default:
                throw new Exception('fail');
        }

        SectorDefencesCleanUpDAO::call($this->container);
    }

    protected function playerinfoUpdate(): void
    {
        ShipUpdateDAO::call($this->container, $this->playerinfo, $this->playerinfo['ship_id']);
    }
}

<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use Psr\Container\ContainerInterface;
use BNT\Bounty\DAO\BountyDeleteByCriteriaDAO;
use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Ship\DAO\ShipByIdDAO;

class GameCollectBountyServitor extends \UUA\Servant
{

    public int $attacker;
    public int $bountyOn;

    #[\Override]
    public function serve(): void
    {
        $bounties = db()->fetchAll("SELECT * FROM bounty,ships WHERE bounty_on = :bounty_on AND bounty_on = ship_id and placed_by <> 0", [
            'bounty_on' => $this->bountyOn,
        ]);

        foreach ($bounties as $bounty) {
            if ($bounty['placed_by'] == 0) {
                $placed = 'l_by_thefeds';
            } else {
                $placed = ShipByIdDAO::call($this->container, $bounty['placed_by'])->ship['ship_name'];
            }

            $attacker = ShipByIdDAO::call($this->container, $this->attacker)->ship;
            $attacker['credits'] += $bounty['amount'];

            ShipUpdateDAO::call($this->container, $attacker, $this->attacker);

            BountyDeleteByCriteriaDAO::call($this->container, [
                'bounty_id' => $bounty['bounty_id'],
            ]);

            LogPlayerDAO::call($this->container, $this->attacker, LogTypeConstants::LOG_BOUNTY_CLAIMED, [
                $bounty['amount'],
                $bounty['ship_name'],
                $placed
            ]);
            LogPlayerDAO::call($this->container, $bounty['placed_by'], LogTypeConstants::LOG_BOUNTY_PAID, [
                $bounty['amount'],
                $bounty['ship_name']
            ]);
        }

        BountyDeleteByCriteriaDAO::call($this->container, [
            'bounty_on' => $this->bountyOn,
        ]);
    }

    public static function call(ContainerInterface $container, int $attacker, int $bountyOn): self
    {
        $self = self::new($container);
        $self->attacker = $attacker;
        $self->bountyOn = $bountyOn;
        $self->serve();

        return $self;
    }
}

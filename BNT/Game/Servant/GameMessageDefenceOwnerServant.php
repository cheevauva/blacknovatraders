<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use Psr\Container\ContainerInterface;
use BNT\SectorDefence\DAO\SectorDefencesByCriteriaDAO;
use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Ship\DAO\ShipByIdDAO;
use BNT\User\DAO\UserByIdDAO;
use BNT\Translate;
use BNT\Language;

class GameMessageDefenceOwnerServant extends \UUA\Servant
{

    public int $sector;
    public int $ship;
    public string|Translate $message;

    #[\Override]
    public function serve(): void
    {
        $defences = SectorDefencesByCriteriaDAO::call($this->container, [
            'sector_id' => $this->sector,
        ])->defences;

        foreach ($defences as $defence) {
            if ($this->message instanceof Translate) {
                $ship = ShipByIdDAO::call($this->container, $defence['ship_id'])->ship;
                $user = UserByIdDAO::call($this->container, $ship['user_id'])->user;

                $translate = Translate::as($this->message);
                $translate->language(Language::get($user['lang']));

                $message = (string) $translate;
            } else {
                $message = $this->message;
            }

            LogPlayerDAO::call($this->container, $defence['ship_id'], LogTypeConstants::LOG_RAW, $message);
        }
    }

    public static function call(ContainerInterface $container, int $sector, string|Translate $message): self
    {
        $self = self::new($container);
        $self->sector = $sector;
        $self->message = $message;
        $self->serve();

        return $self;
    }
}

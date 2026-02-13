<?php

declare(strict_types=1);

namespace BNT\EntryPoint\Servant;

use BNT\Scheduler\DAO\SchedulerGetLastRunDAO;
use BNT\Ship\DAO\ShipGetOnlinePlayersCountDAO;
use BNT\Message\DAO\MessagesCountByShipDAO;
use BNT\Message\DAO\MessagesNotifiedByShipDAO;

class EntryPointStatusServant extends \UUA\Servant
{

    public $ship;
    public $schedulerLastRun;
    public $online;
    public $messages = 0;

    #[\Override]
    public function serve(): void
    {
        $this->messages = 0;

        $getLastRun = SchedulerGetLastRunDAO::new($this->container);
        $getLastRun->serve();

        $this->schedulerLastRun = $getLastRun->lastRun;

        $getOnlineCount = ShipGetOnlinePlayersCountDAO::new($this->container);
        $getOnlineCount->serve();

        $this->online = $getOnlineCount->count;

        if ($this->ship) {
            $getMessagesCount = MessagesCountByShipDAO::new($this->container);
            $getMessagesCount->serve();

            $this->messages = $getMessagesCount->count;

            if ($this->messages > 0) {
                $messagesNotified = MessagesNotifiedByShipDAO::new($this->container);
                $messagesNotified->ship = $this->ship['ship_id'];
                $messagesNotified->serve();
            }
        }
    }
}

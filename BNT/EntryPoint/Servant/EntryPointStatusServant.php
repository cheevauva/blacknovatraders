<?php

//declare(strict_types=1);

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

    public function serve()
    {
        $this->messages = 0;

        $getLastRun = SchedulerGetLastRunDAO::_new($this->container);
        $getLastRun->serve();

        $this->schedulerLastRun = $getLastRun->lastRun;

        $getOnlineCount = ShipGetOnlinePlayersCountDAO::_new($this->container);
        $getOnlineCount->serve();

        $this->online = $getOnlineCount->count;

        if ($this->ship) {
            $getMessagesCount = MessagesCountByShipDAO::_new($this->container);
            $getMessagesCount->serve();

            $this->messages = $getMessagesCount->count;

            if ($this->messages > 0) {
                $messagesNotified = MessagesNotifiedByShipDAO::_new($this->container);
                $messagesNotified->ship = $this->ship['ship_id'];
                $messagesNotified->serve();
            }
        }
    }
}

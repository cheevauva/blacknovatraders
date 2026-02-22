<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Scheduler\DAO\SchedulerGetLastRunDAO;
use BNT\Ship\DAO\ShipGetOnlinePlayersCountDAO;
use BNT\Message\DAO\MessagesCountByShipDAO;
use BNT\Message\DAO\MessagesNotifiedByShipDAO;

class StatusController extends BaseController
{

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->enableCheckAuth = false;
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        global $sched_ticks;
        
        $messages = 0;

        $getLastRun = SchedulerGetLastRunDAO::new($this->container);
        $getLastRun->serve();

        $schedulerLastRun = $getLastRun->lastRun;

        $getOnlineCount = ShipGetOnlinePlayersCountDAO::new($this->container);
        $getOnlineCount->serve();

        $online = $getOnlineCount->count;

        if ($this->playerinfo) {
            $getMessagesCount = MessagesCountByShipDAO::new($this->container);
            $getMessagesCount->serve();

            $messages = $getMessagesCount->count;

            if ($messages > 0) {
                $messagesNotified = MessagesNotifiedByShipDAO::new($this->container);
                $messagesNotified->ship = $this->playerinfo['ship_id'];
                $messagesNotified->serve();
            }
        }



        $mySEC = 0;

        if ($schedulerLastRun) {
            $mySEC = ($sched_ticks * 60) - (time() - $schedulerLastRun);
        }
        if ($mySEC < 0) {
            $mySEC = ($sched_ticks * 60);
        }


        $this->responseJson = [
            'online' => $online,
            'schedTicks' => $sched_ticks,
            'myx' => $mySEC,
            'unreadMessages' => $messages ? $this->l->youhave . $messages . $$this->l->messages_wait : null,
            'M' => sprintf('%.2f', memory_get_peak_usage() / 1024 / 1024, 2),
            'E' => sprintf('%.3f', microtime(true) - MICROTIME_START),
        ];
    }
}

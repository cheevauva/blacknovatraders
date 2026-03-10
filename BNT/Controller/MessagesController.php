<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Ship\DAO\ShipsByCriteriaDAO;
use BNT\Exception\WarningException;
use BNT\Message\DAO\MessageCreateDAO;
use BNT\Message\DAO\MessagesByCriteriaDAO;
use BNT\Message\DAO\MessagesDeleteByCriteriaDAO;
use BNT\Message\DAO\MessageByIdDAO;
use BNT\Team\DAO\TeamsByCriteriaDAO;
use BNT\Exception\SuccessException;

class MessagesController extends BaseController
{

    public array $ships;
    public array $teams;
    public ?array $reply = null;
    public array $messages = [];
    public int $ship = 0;
    public int $team = 0;
    public int $replyId = 0;
    public float $messageCharCost = 0.1;
    public bool $send = false;
    public bool $read = false;

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->t('l_messages_title');
        $this->send = $this->fromQueryParams('send')->default(false)->asBool();
        $this->read = $this->fromQueryParams('read')->default(false)->asBool();
        $this->replyId = $this->fromQueryParams('reply_id')->default(0)->asInt();
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->ship = $this->fromQueryParams('ship')->default(0)->asInt();
        $this->team = $this->fromQueryParams('team')->default(0)->asInt();
        $this->reply = MessageByIdDAO::call($this->container, $this->replyId)->message;

        if (!empty($this->reply)) {
            if (in_array($this->playerinfo['ship_id'], [$this->reply['recp_id'], $this->reply['sender_id']], true)) {
                $this->reply['sender'] = ShipByIdDAO::call($this->container, $this->reply['sender_id'])->ship;
            } else {
                $this->reply = null;
            }
        }

        if ($this->send) {
            $this->ships = ShipsByCriteriaDAO::call($this->container, ['ship_destroyed' => 'N'])->ships;
            $this->teams = TeamsByCriteriaDAO::call($this->container)->teams;
        }

        if ($this->read) {
            $this->messages = MessagesByCriteriaDAO::call($this->container, ['recp_id' => $this->playerinfo['ship_id']])->messages;

            foreach ($this->messages as $idxMesssage => $message) {
                $message['sender'] = ShipByIdDAO::call($this->container, $message['sender_id'])->ship;
                $this->messages[$idxMesssage] = $message;
            }
        }

        $this->render('tpls/messages/messages.tpl.php');
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        $this->checkTurns();

        $action = $this->fromParsedBody('action')->notEmpty()->asString();

        if (!in_array($action, ['send', 'delete', 'delete_all'], true)) {
            throw new WarningException('l_messages_action_not_allow');
        }

        switch ($action) {
            case 'send':
                $content = $this->fromParsedBody('content')->label('l_messages_content')->trim()->notEmpty()->asString();
                $team = $this->fromParsedBody('team')->default(0)->asInt();
                $ship = $this->fromParsedBody('ship')->default(0)->asInt();
                $reply = $this->fromParsedBody('reply_id')->default(0)->asInt();
                $messageCost = mb_strlen($content) * $this->messageCharCost;

                if (mb_strlen($content) > 140) {
                    throw new WarningException('l_messages_content_so_big_lenght');
                }

                $ships = [];

                if ($ship) {
                    array_push($ships, ShipByIdDAO::call($this->container, $ship)->ship['ship_id'] ?? throw new WarningException('l_messages_ship_not_found'));
                }

                if ($team) {
                    array_push($ships, ...array_column(ShipsByCriteriaDAO::call($this->container, ['team' => $team])->ships, 'ship_id'));
                }

                $ships = array_unique($ships);

                $messagesCost = intval(count($ships) * $messageCost);
                $turns = count($ships);

                if ($this->playerinfo['turns'] < count($ships)) {
                    throw new WarningException()->t('l_messages_you_nothaveturns', [
                        'turns' => $turns,
                    ]);
                }

                if ($this->playerinfo['credits'] < $messagesCost) {
                    throw new WarningException()->t('l_messages_you_nothavecredis', [
                        'ships' => count($ships),
                        'message_cost' => $messageCost,
                        'messages_cost' => $messagesCost,
                    ]);
                }

                if (empty($ships)) {
                    throw new WarningException('l_messages_ship_list_is_empty');
                }

                foreach ($ships as $ship) {
                    MessageCreateDAO::call($this->container, [
                        'sender_id' => $this->playerinfo['ship_id'],
                        'recp_id' => $ship,
                        'sent' => date('Y-m-d H:i:s'),
                        'message' => $content,
                        'reply_id' => $reply,
                    ]);
                }

                $this->playerinfo['credits'] -= $messagesCost;
                $this->playerinfoTurn($turns);
                $this->playerinfoUpdate();

                throw new SuccessException('l_messages_sent');
            case 'delete':
                MessagesDeleteByCriteriaDAO::call($this->container, [
                    'id' => $this->fromParsedBody('id')->notEmpty()->asInt(),
                    'recp_id' => $this->playerinfo['ship_id']
                ]);
                break;
            case 'delete_all':
                MessagesDeleteByCriteriaDAO::call($this->container, [
                    'recp_id' => $this->playerinfo['ship_id']
                ]);
                break;
        }

        $this->redirectTo('messages', [
            'read' => $this->read,
            'send' => $this->send,
        ]);
    }
}

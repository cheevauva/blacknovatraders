<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Ship\DAO\ShipByIdDAO;

class MessagesController extends BaseController
{

    public array $ships;
    public array $messages = [];
    public int $to = 1;

    #[\Override]
    protected function init(): void
    {
        global $l;

        parent::init();

        $this->title = $l->readm_title;
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {

        $this->ships = db()->fetchAll("SELECT * FROM ships WHERE ship_destroyed = 'N' ORDER BY ship_name ASC");
        $this->to = intval($this->queryParams['to'] ?? 0);
        $this->messages = db()->fetchAll("SELECT * FROM messages WHERE recp_id='" . $this->playerinfo['ship_id'] . "' ORDER BY sent DESC");
        $this->messages[] = [
            'recp_id' => 1,
            'sender_id' => 1,
            'sender' => ShipByIdDAO::call($this->container, 1)->ship,
        ];

        $this->render('tpls/messages/messages.tpl.php');
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        switch ($this->parsedBody['action'] ?? null) {
            case 'delete':
                $id = intval($this->parsedBody['id'] ?? null);
                db()->q("DELETE FROM messages WHERE ID=:id AND recp_id=:recp_id", [
                    'id' => $id,
                    'recp_id' => $this->playerinfo['ship_id'],
                ]);
                break;
            case 'delete_all':
                db()->q("DELETE FROM messages WHERE recp_id=:recp_id", [
                    'recp_id' => $this->playerinfo['ship_id'],
                ]);
                break;
            case 'send':
                db()->q("INSERT INTO messages (sender_id, recp_id, subject, message) VALUES (:sender_id, :recp_id, :subject, :message)", [
                    'sender_id' => $this->playerinfo['ship_id'],
                    'recp_id' => $this->parsedBody['to'],
                    'subject' => 'subject',
                    'message' => $this->parsedBody['content'] ?? '',
                ]);
                break;
        }

        $this->redirectTo('messages.php');
    }
}

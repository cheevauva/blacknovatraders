<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\User\DAO\UserByIdDAO;
use BNT\User\DAO\UserUpdateDAO;
use BNT\User\DAO\UsersByCriteriaDAO;
use BNT\Ship\DAO\ShipsByCriteriaDAO;

class AdminUserController extends BaseController
{

    public array $user = [];
    public array $users = [];
    public array $ships = [];
    public string $operation;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->enableCheckShip = false;
    }

    #[\Override]
    protected function preProcess(): void
    {
        $this->isAdmin() ?: throw new ErrorException('You not admin');
        $this->operation = $this->fromQueryParams('operation')->asString();

        if (in_array($this->operation, ['save', 'edit'])) {
            $user = $this->fromQueryParams('user')->notEmpty()->asInt();
            $this->user = UserByIdDAO::call($this->container, $user)->user;
        }
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        if ($this->operation === 'list') {
            $this->users = array_column(UsersByCriteriaDAO::call($this->container)->users, 'character_name', 'id');
            $this->render('tpls/admin/userlist.tpl.php');
            return;
        }

        if ($this->operation === 'edit') {
            $this->ships = array_column(ShipsByCriteriaDAO::call($this->container)->ships, 'ship_name', 'ship_id');
            $this->ships[''] = '';
            $this->render('tpls/admin/useredit.tpl.php');
            return;
        }

        parent::processGetAsHtml();
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        if ($this->operation === 'save') {
            $password = $this->fromParsedBody('password')->trim()->asString();

            UserUpdateDAO::call($this->container, [
                'character_name' => $this->fromParsedBody('character_name')->trim()->notEmpty()->asString(),
                'role' => $this->fromParsedBody('role')->trim()->notEmpty()->asString(),
                'ship_id' => $this->fromParsedBody('ship_id')->notEmpty()->asInt(),
                'password' => $password ? md5($password) : $this->user['password'],
            ], $this->user['id']);

            $this->redirectTo('admin', [
                'module' => 'user',
                'operation' => 'list',
            ]);
            return;
        }

        parent::processPostAsJson();
    }
}

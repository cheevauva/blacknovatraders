<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\User\DAO\UserByIdDAO;
use BNT\User\DAO\UserUpdateDAO;

class AdminUserController extends BaseController
{

    public array $user = [];
    public array $users = [];
    public array $ships = [];
    public string $operation;

    #[\Override]
    protected function preProcess(): void
    {
        $this->isAdmin() ?: throw new ErrorException('You not admin');
        $this->operation = (string) $this->fromQueryParams('operation');
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        global $l;

        if ($this->operation === 'list') {
            $this->users = db()->fetchAllKeyValue("SELECT id, character_name FROM users");
            $this->render('tpls/admin/userlist.tpl.php');
            return;
        }

        if ($this->operation === 'edit') {
            $user = (int) $this->fromQueryParams('user', 'user ' . $l->is_required);
            $this->user = UserByIdDAO::call($this->container, $user)->user;
            $this->ships = db()->fetchAllKeyValue("SELECT ship_id, ship_name FROM ships ORDER BY ship_name");
            $this->ships[''] = '';
            $this->render('tpls/admin/useredit.tpl.php');
            return;
        }

        parent::processGetAsHtml();
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        global $l;

        if ($this->operation === 'save') {
            $user = (int) $this->fromQueryParams('user', 'user ' . $l->is_required);
            $password = $this->fromParsedBody('password');

            $userinfo = [
                'character_name' => $this->fromParsedBody('character_name', 'character_name ' . $l->is_required),
                'role' => $this->fromParsedBody('role', 'role ' . $l->is_required),
                'ship_id' => $this->fromParsedBody('ship_id'),
            ];

            if (!empty($password)) {
                $userinfo['password'] = md5($password);
            }
  
            UserUpdateDAO::call($this->container, $userinfo, $user);
            $this->redirectTo('admin.php?module=user&operation=list');
            return;
        }

        parent::processPostAsJson();
    }
}

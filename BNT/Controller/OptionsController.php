<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\User\DAO\UserUpdateDAO;
use BNT\Exception\WarningException;

class OptionsController extends BaseController
{

    public array $user;

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/options.tpl.php');
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        global $l;

        $oldpass = strval($this->parsedBody['oldpass'] ?? '');
        $newpass1 = strval($this->parsedBody['newpass1'] ?? '');
        $newpass2 = strval($this->parsedBody['newpass2'] ?? '');
        $newlang = strval($this->parsedBody['newlang'] ?? $this->user['lang']);

        if (in_array($newlang, array_keys(languages()), true)) {
            $this->userinfo['lang'] = $newlang;
        }

        if (!empty($newpass1) || !empty($newpass2)) {
            if (empty($oldpass)) {
                throw new WarningException($l->opt2_srcpassfalse);
            } elseif ($newpass1 != $newpass2) {
                throw new WarningException($l->opt2_newpassnomatch);
            } else {
                if (md5($oldpass) != $this->userinfo['password']) {
                    throw new WarningException($l->opt2_srcpassfalse);
                }

                $this->userinfo['password'] = md5($newpass1);
            }
        }
        
        $this->userinfoUpdate();
        $this->redirectTo('index.php');
    }
}

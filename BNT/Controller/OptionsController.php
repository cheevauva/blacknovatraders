<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\User\DAO\UserUpdateDAO;
use Exception;

class OptionsController extends BaseController
{

    #[\Override]
    protected function processGet(): void
    {
        $this->render('tpls/options.tpl.php');
    }

    #[\Override]
    protected function processPost(): void
    {
        global $l;
        global $language;

        try {
            $oldpass = strval($this->parsedBody['oldpass'] ?? '');
            $newpass1 = strval($this->parsedBody['newpass1'] ?? '');
            $newpass2 = strval($this->parsedBody['newpass2'] ?? '');
            $newlang = strval($this->parsedBody['newlang'] ?? $language);

            if (in_array($newlang, array_keys(languages()), true)) {
                $this->userinfo['lang'] = $newlang;

                UserUpdateDAO::call($this->container, $this->userinfo, $this->userinfo['id']);
            }

            if (!empty($newpass1) || !empty($newpass2)) {
                if (empty($oldpass)) {
                    throw new Exception($l->opt2_srcpassfalse);
                } elseif ($newpass1 != $newpass2) {
                    throw new Exception($l->opt2_newpassnomatch);
                } else {
                    if (md5($oldpass) != $this->userinfo['password']) {
                        throw new Exception($l->opt2_srcpassfalse);
                    }

                    $this->userinfo['password'] = md5($newpass1);

                    UserUpdateDAO::call($this->container, $this->userinfo, $this->userinfo['id']);
                }
            }

            $this->redirectTo('index.php');
        } catch (\Exception $ex) {
            echo responseJsonByException($ex);
        }
    }
}

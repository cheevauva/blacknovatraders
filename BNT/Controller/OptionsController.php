<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Exception\WarningException;

class OptionsController extends BaseController
{

    public array $user;

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->l->opt_title;
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/options.tpl.php');
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        global $l;

        $oldpass = (string) $this->fromParsedBody('oldpass');
        $newpass1 = (string) $this->fromParsedBody('newpass1');
        $newpass2 = (string) $this->fromParsedBody('newpass2');
        $newlang = strval($this->fromParsedBody('newlang') ?? $this->user['lang']);
        $theme = (string) $this->fromParsedBody('theme');

        if (in_array($theme, ['dark', 'light'], true)) {
            $this->userinfo['theme'] = $theme;
        }

        if (in_array($newlang, array_keys(languages()), true)) {
            $this->userinfo['lang'] = $newlang;
        }

        if (!empty($newpass1) || !empty($newpass2)) {
            if (empty($oldpass)) {
                throw new WarningException($l->opt2_srcpassfalse);
            }

            if ($newpass1 != $newpass2) {
                throw new WarningException($l->opt2_newpassnomatch);
            }

            if (md5($oldpass) != $this->userinfo['password']) {
                throw new WarningException($l->opt2_srcpassfalse);
            }

            $this->userinfo['password'] = md5($newpass1);
        }

        $this->userinfoUpdate();
        $this->redirectTo('index');
    }
}

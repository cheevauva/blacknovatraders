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
        $this->title = $this->t('l_opt_title');
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/options.tpl.php');
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        $oldpass = $this->fromParsedBody('oldpass')->trim()->asString();
        $newpass1 = $this->fromParsedBody('newpass1')->trim()->asString();
        $newpass2 = $this->fromParsedBody('newpass2')->trim()->asString();
        $newlang = $this->fromParsedBody('newlang')->trim()->default($this->userinfo['lang'])->asString();
        $theme = $this->fromParsedBody('theme')->trim()->default($this->userinfo['theme'])->asString();

        if (in_array($theme, ['dark', 'light'], true)) {
            $this->userinfo['theme'] = $theme;
        }

        if (in_array($newlang, array_keys(languages()), true)) {
            $this->userinfo['lang'] = $newlang;
        }

        if (!empty($newpass1) || !empty($newpass2)) {
            if (empty($oldpass)) {
                throw new WarningException('l_opt2_srcpassfalse');
            }

            if ($newpass1 != $newpass2) {
                throw new WarningException('l_opt2_newpassnomatch');
            }

            if (md5($oldpass) != $this->userinfo['password']) {
                throw new WarningException('l_opt2_srcpassfalse');
            }

            $this->userinfo['password'] = md5($newpass1);
        }

        $this->userinfoUpdate();
        $this->redirectTo('index');
    }
}

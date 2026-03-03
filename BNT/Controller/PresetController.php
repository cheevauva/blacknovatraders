<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Exception\SuccessException;
use BNT\Exception\WarningException;

class PresetController extends BaseController
{

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->l->pre_title;
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/preset.tpl.php');
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        global $sector_max;
        
        $preset1 = abs($this->fetch($this->parsedBody, 'preset1')->label($this->l->pre_set_1)->asInt());
        $preset2 = abs($this->fetch($this->parsedBody, 'preset2')->label($this->l->pre_set_2)->asInt());
        $preset3 = abs($this->fetch($this->parsedBody, 'preset3')->label($this->l->pre_set_3)->asInt());

        if ($preset1 > $sector_max) {
            throw new WarningException(strtr($this->l->pre_exceed, [
                '[preset]' => '31',
                '[sector_max]' => $sector_max
            ]));
        }

        if ($preset2 > $sector_max) {
            throw new WarningException(strtr($this->l->pre_exceed, [
                '[preset]' => '2',
                '[sector_max]' => $sector_max
            ]));
        }

        if ($preset3 > $sector_max) {
            throw new WarningException(strtr($this->l->pre_exceed, [
                '[preset]' => '3',
                '[sector_max]' => $sector_max
            ]));
        }

        $this->playerinfo['preset1'] = $preset1;
        $this->playerinfo['preset2'] = $preset2;
        $this->playerinfo['preset3'] = $preset3;
        $this->playerinfoUpdate();

        throw new SuccessException($this->l->pre_set);
    }
}

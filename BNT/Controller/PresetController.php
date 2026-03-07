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
        $this->title = $this->t('l_pre_title');
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

        $preset1 = abs($this->fromParsedBody('preset1')->label($this->l->pre_set_1)->asInt());
        $preset2 = abs($this->fromParsedBody('preset2')->label($this->l->pre_set_2)->asInt());
        $preset3 = abs($this->fromParsedBody('preset3')->label($this->l->pre_set_3)->asInt());

        if ($preset1 > $sector_max) {
            throw new WarningException()->translate('l_pre_exceed', [
                'preset' => '1',
                'sector_max' => $sector_max
            ]);
        }

        if ($preset2 > $sector_max) {
            throw new WarningException()->translate('l_pre_exceed', [
                'preset' => '2',
                'sector_max' => $sector_max
            ]);
        }

        if ($preset3 > $sector_max) {
            throw new WarningException()->translate('l_pre_exceed', [
                'preset' => '3',
                'sector_max' => $sector_max
            ]);
        }

        $this->playerinfo['preset1'] = $preset1;
        $this->playerinfo['preset2'] = $preset2;
        $this->playerinfo['preset3'] = $preset3;
        $this->playerinfoUpdate();

        throw new SuccessException('l_pre_set');
    }
}

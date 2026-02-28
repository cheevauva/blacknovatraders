<?php

declare(strict_types=1);

namespace BNT\Controller;

class DeviceController extends BaseController
{

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->l->device_title;
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/device.tpl.php');
    }
}

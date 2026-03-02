<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Exception\ErrorException;

class NotFoundController extends BaseController
{
    #[\Override]
    protected function init(): void
    {
        parent::init();
        
        $this->enableCheckAuth = false;
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        throw new ErrorException('Not found');
    }
}

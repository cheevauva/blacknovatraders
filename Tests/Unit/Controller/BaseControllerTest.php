<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use BNT\Controller\BaseController;

class BaseControllerTest extends \Tests\UnitTestCase
{

    protected function baseController(): BaseController
    {
        $controller = BaseController::new(self::$container);
        $controller->enableThrowExceptionOnProcess = true;
        $controller->enableCheckAuth = false;

        return $controller;
    }

    public function testProcessPOSTJson(): void
    {
        $this->expectExceptionMessage('Processing of the POST method as JSON is not implemented');
        
        $controller = $this->baseController();
        $controller->requestMethod = 'POST';
        $controller->acceptType = $controller::ACCEPT_TYPE_JSON;
        $controller->serve();
    }

    #[\Override]
    protected function stubs(): array
    {
        return [
            BaseController::class => fn($c) => new class($c) extends BaseController {

                #[\Override]
                protected function processGetAsHtml(): void
                {
                    if ($this->fromQueryParams('testRedirectToSetsLocation')) {
                        $this->redirectTo('main');
                    }

                    if ($this->fromQueryParams('testSetCookieAddsToResponseCookies')) {
                        $this->setCookie('token', 'abc123', 3600, '/', 'example.com', true, true);
                    }

                    if ($this->fromQueryParams('testResponseJsonByExceptionSetsExceptionAndJson')) {
                        $this->responseJsonByException(new \Exception('Test error', 500));
                    }

                    if ($this->fromQueryParams('testRenderSetsTemplate')) {
                        $this->render('test.tpl.php');
                    }

                    parent::processGetAsHtml();
                }
            },
        ];
    }
}

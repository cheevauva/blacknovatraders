<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use BNT\Controller\BaseController;

class BaseControllerTest extends \Tests\UnitTestCase
{

    protected BaseController $controller;
    public static bool $processGetCalled;
    public static bool $processPostCalled;
    public static bool $prepareResponseCalled;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = BaseController::new(self::$container);
        $this->controller->disablePrepareResponse = true;

        self::$prepareResponseCalled = false;
        self::$processGetCalled = false;
        self::$processPostCalled = false;
    }

    public function testServeSetsDefaultRequestMethod(): void
    {
        unset($_SERVER['REQUEST_METHOD']);

        $this->controller->serve();

        self::assertEquals('GET', $this->controller->requestMethod);
    }

    public function testServeUsesServerRequestMethod(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $this->controller->serve();

        self::assertEquals('POST', $this->controller->requestMethod);
    }

    public function testServeSetsQueryParamsFromGet(): void
    {
        $_GET = [
            'page' => '1',
            'sort' => 'asc'
        ];

        $this->controller->serve();

        self::assertEquals($_GET, $this->controller->queryParams);
    }

    public function testServeSetsParsedBodyFromPost(): void
    {
        $_POST = [
            'username' => 'test',
            'password' => '123'
        ];

        $this->controller->serve();

        self::assertEquals($_POST, $this->controller->parsedBody);
    }

    public function testServeCallsProcessGetForGetRequest(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->controller->serve();

        self::assertTrue(self::$processGetCalled);
        self::assertFalse(self::$processPostCalled);
    }

    public function testServeCallsProcessPostForPostRequest(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->controller->serve();

        self::assertFalse(self::$processGetCalled);
        self::assertTrue(self::$processPostCalled);
    }

    public function testServeDoesNotCallPrepareResponseWhenDisabled(): void
    {
        $this->controller->disablePrepareResponse = true;
        $this->controller->serve();

        self::assertFalse(self::$prepareResponseCalled);
    }

    public function testServeCallsPrepareResponseWhenEnabled(): void
    {
        $this->controller->disablePrepareResponse = false;
        $this->controller->serve();

        self::assertTrue(self::$prepareResponseCalled);
    }

    public function testRedirectToSetsLocation(): void
    {
        $this->controller->requestMethod = 'GET';
        $this->controller->queryParams = [
            'testRedirectToSetsLocation' => true
        ];
        $this->controller->serve();

        self::assertEquals('main.php', $this->controller->location);
    }

    public function testSetCookieAddsToResponseCookies(): void
    {
        $this->controller->requestMethod = 'GET';
        $this->controller->queryParams = [
            'testSetCookieAddsToResponseCookies' => true
        ];
        $this->controller->serve();

        self::assertArrayHasKey('token', $this->controller->responseCookies);
        self::assertEquals(['abc123', 3600, '/', 'example.com', true, true], $this->controller->responseCookies['token']);
    }

    public function testResponseJsonByExceptionSetsExceptionAndJson(): void
    {
        $this->controller->requestMethod = 'GET';
        $this->controller->queryParams = [
            'testResponseJsonByExceptionSetsExceptionAndJson' => true
        ];
        $this->controller->serve();

        self::assertNotNull($this->controller->exception);
        self::assertNotNull($this->controller->responseJson);

        $json = $this->controller->responseJson->getArrayCopy();

        self::assertFalse($json['success']);
        self::assertEquals('exception', $json['type']);
        self::assertEquals('Test error', $json['error']);
        self::assertEquals(500, $json['code']);
    }

    public function testRenderSetsTemplate(): void
    {
        $this->controller->requestMethod = 'GET';
        $this->controller->queryParams = [
            'testRenderSetsTemplate' => true
        ];
        $this->controller->serve();

        self::assertEquals('test.tpl.php', $this->controller->template);
    }

    public function testPrepareResponse(): void
    {
        $this->controller->disablePrepareResponse = false;
        $this->controller->serve();

        self::assertTrue(self::$prepareResponseCalled);
    }
    
    #[\Override]
    protected function stubs(): array
    {
        return [
            BaseController::class => fn($c) => new class($c) extends BaseController {

                public bool $processGetCalled = false;
                public bool $processPostCalled = false;
                public bool $prepareResponseCalled = false;

                #[\Override]
                protected function processGet(): void
                {
                    BaseControllerTest::$processGetCalled = true;

                    if (!empty($this->queryParams['testRedirectToSetsLocation'])) {
                        $this->redirectTo('main.php');
                    }

                    if (!empty($this->queryParams['testSetCookieAddsToResponseCookies'])) {
                        $this->setCookie('token', 'abc123', 3600, '/', 'example.com', true, true);
                    }

                    if (!empty($this->queryParams['testResponseJsonByExceptionSetsExceptionAndJson'])) {
                        $this->responseJsonByException(new \Exception('Test error', 500));
                    }

                    if (!empty($this->queryParams['testRenderSetsTemplate'])) {
                        $this->render('test.tpl.php');
                    }

                    parent::processGet();
                }

                #[\Override]
                protected function processPost(): void
                {
                    BaseControllerTest::$processPostCalled = true;

                    parent::processPost();
                }

                #[\Override]
                protected function prepareResponse(): void
                {
                    BaseControllerTest::$prepareResponseCalled = true;
                }
            },
        ];
    }
}

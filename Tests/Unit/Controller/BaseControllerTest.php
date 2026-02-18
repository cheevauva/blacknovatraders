<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use BNT\Controller\BaseController;

class BaseControllerTest extends \Tests\UnitTestCase
{

    public static BaseController $controller;
    public static bool $processGetCalled;
    public static bool $processPostCalled;
    public static bool $prepareResponseCalled;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        self::$controller = BaseController::new(self::$container);
        self::$controller->enableCheckAuth = false;
        self::$controller->disablePrepareResponse = true;
    }

    public function testServeSetsDefaultRequestMethod(): void
    {
        unset($_SERVER['REQUEST_METHOD']);

        self::$controller->serve();

        self::assertEquals('GET', self::$controller->requestMethod);
    }

    public function testServeUsesServerRequestMethod(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        self::$controller->serve();

        self::assertEquals('POST', self::$controller->requestMethod);
    }

    public function testServeSetsQueryParamsFromGet(): void
    {
        $_GET = [
            'page' => '1',
            'sort' => 'asc'
        ];

        self::$controller->serve();

        self::assertEquals($_GET, self::$controller->queryParams);
    }

    public function testServeSetsParsedBodyFromPost(): void
    {
        $_POST = [
            'username' => 'test',
            'password' => '123'
        ];

        self::$controller->serve();

        self::assertEquals($_POST, self::$controller->parsedBody);
    }

    public function testServeCallsProcessGetForGetRequest(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        self::$controller->serve();

        self::assertTrue(self::$controller->processedGet);
        self::assertFalse(self::$controller->processedPost);
    }

    public function testServeCallsProcessPostForPostRequest(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        self::$controller->serve();

        self::assertFalse(self::$controller->processedGet);
        self::assertTrue(self::$controller->processedPost);
    }

    public function testServeDoesNotCallPrepareResponseWhenDisabled(): void
    {
        self::$controller->disablePrepareResponse = true;
        self::$controller->serve();

        self::assertFalse(self::$controller->processedPrepareResponse);
    }

    public function testServeCallsPrepareResponseWhenEnabled(): void
    {
        self::$controller->disablePrepareResponse = false;
        self::$controller->serve();

        self::assertTrue(self::$controller->processedPrepareResponse);
    }

    public function testRedirectToSetsLocation(): void
    {
        self::$controller->requestMethod = 'GET';
        self::$controller->queryParams = [
            'testRedirectToSetsLocation' => true
        ];
        self::$controller->serve();

        self::assertEquals('main.php', self::$controller->location);
    }

    public function testSetCookieAddsToResponseCookies(): void
    {
        self::$controller->requestMethod = 'GET';
        self::$controller->queryParams = [
            'testSetCookieAddsToResponseCookies' => true
        ];
        self::$controller->serve();

        self::assertArrayHasKey('token', self::$controller->responseCookies);
        self::assertEquals(['abc123', 3600, '/', 'example.com', true, true], self::$controller->responseCookies['token']);
    }

    public function testResponseJsonByExceptionSetsExceptionAndJson(): void
    {
        self::$controller->requestMethod = 'GET';
        self::$controller->queryParams = [
            'testResponseJsonByExceptionSetsExceptionAndJson' => true
        ];
        self::$controller->serve();

        self::assertNotNull(self::$controller->exception);
        self::assertNotNull(self::$controller->responseJson);

        $json = self::$controller->responseJson->getArrayCopy();

        self::assertFalse($json['success']);
        self::assertEquals('exception', $json['type']);
        self::assertEquals('Test error', $json['error']);
        self::assertEquals(500, $json['code']);
    }

    public function testRenderSetsTemplate(): void
    {
        self::$controller->requestMethod = 'GET';
        self::$controller->queryParams = [
            'testRenderSetsTemplate' => true
        ];
        self::$controller->serve();

        self::assertEquals('test.tpl.php', self::$controller->template);
    }

    public function testPrepareResponse(): void
    {
        self::$controller->disablePrepareResponse = false;
        self::$controller->serve();

        self::assertTrue(self::$controller->processedPrepareResponse);
    }

    #[\Override]
    protected function stubs(): array
    {
        return [
            BaseController::class => fn($c) => new class($c) extends BaseController {

                #[\Override]
                protected function processGet(): void
                {
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
            },
        ];
    }
}

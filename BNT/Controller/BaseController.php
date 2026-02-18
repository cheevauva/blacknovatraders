<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Language;

abstract class BaseController extends \UUA\Unit
{

    use \UUA\Traits\ContainerTrait;
    use \UUA\Traits\BuildTrait;

    public string $template;
    public string $requestMethod;
    public array $parsedBody;
    public array $queryParams;
    public protected(set) ?string $location = null;
    public string $title = 'BlackNova Traders';
    public array $responseCookies = [];
    public ?\ArrayObject $responseJson = null;
    public bool $disablePrepareResponse = false;
    public bool $processedPost = false;
    public bool $processedGet = false;
    public ?\Throwable $exception = null;

    protected function redirectTo($location): void
    {
        $this->location = $location;
    }

    protected function setCookie(string $name, string $value = "", int $expires_or_options = 0, string $path = "", string $domain = "", bool $secure = false, bool $httponly = false): void
    {
        $this->responseCookies[$name] = [$value, $expires_or_options, $path, $domain, $secure, $httponly];
    }

    protected function responseJsonByException(\Throwable $ex): void
    {
        $this->exception = $ex;
        $this->responseJson = new \ArrayObject([
            'success' => false,
            'type' => 'exception',
            'error' => $ex->getMessage(),
            'code' => $ex->getCode(),
        ]);
    }

    protected function processPost(): void
    {
        
    }

    protected function processGet(): void
    {
        
    }

    protected function render(string $template): void
    {
        $this->template = $template;
    }

    #[\Override]
    public function serve(): void
    {
        $this->requestMethod ??= $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->queryParams ??= $_GET ?? [];
        $this->parsedBody ??= $_POST ?? [];

        switch ($this->requestMethod) {
            case 'GET':
                $this->processGet();
                $this->processedGet = true;
                break;
            case 'POST':
                $this->processPost();
                $this->processedPost = false;
                break;
        }

        if (!$this->disablePrepareResponse) {
            $this->prepareResponse();
        }
    }

    protected function prepareResponse(): void
    {
        if (!empty($this->template)) {
            global $title;

            $l = new Language();
            $title = $this->title;

            include $this->template;
        }

        foreach ($this->responseCookies as $name => $cookies) {
            setcookie($name, ...$cookies);
        }

        if (!empty($this->location)) {
            header('Location: ' . $this->location, true, 302);
        }

        if (!empty($this->responseJson)) {
            header('Content-Type: application/json');
            echo json_encode($this->responseJson, JSON_UNESCAPED_UNICODE);
        }
    }
}

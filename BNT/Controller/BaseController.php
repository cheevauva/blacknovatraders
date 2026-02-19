<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Language;

abstract class BaseController extends \UUA\Unit
{

    use \UUA\Traits\ContainerTrait;
    use \UUA\Traits\BuildTrait;

    public ?string $template = null;
    public string $requestMethod;
    public array $parsedBody;
    public array $queryParams;
    public protected(set) ?string $location = null;
    public string $title = 'BlackNova Traders';
    public array $responseCookies = [];
    public ?\ArrayObject $responseJson = null;
    public bool $disablePrepareResponse = false;
    public bool $enableCheckAuth = true;
    public bool $enableCheckShip = true;
    public bool $enableCheckUser = true;
    public bool $processedPost = false;
    public bool $processedGet = false;
    public bool $processedPrepareResponse = false;
    public ?\Throwable $exception = null;
    public ?array $userinfo = null;
    public ?array $playerinfo = null;

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
        global $userinfo;
        global $playerinfo;

        $this->playerinfo ??= $playerinfo;
        $this->userinfo ??= $userinfo;
        $this->requestMethod ??= $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->queryParams ??= $_GET ?? [];
        $this->parsedBody ??= $_POST ?? [];

        if (!$this->auth()) {
            $this->prepareResponse();
            return;
        }

        switch ($this->requestMethod) {
            case 'GET':
                $this->processGet();
                $this->processedGet = true;
                break;
            case 'POST':
                $this->processPost();
                $this->processedPost = true;
                break;
        }

        $this->prepareResponse();
    }

    protected function auth(): bool
    {
        if (!$this->enableCheckAuth) {
            return true;
        }

        if ($this->enableCheckUser) {
            if (empty($this->userinfo)) {
                $this->redirectTo('login.php');
                return false;
            }
        }

        if ($this->enableCheckShip) {
            if (empty($this->playerinfo)) {
                $this->redirectTo('ships.php');
                return false;
            }
        }

        return true;
    }

    protected function prepareResponse(): void
    {
        if ($this->disablePrepareResponse) {
            return;
        }
        
        $this->processedPrepareResponse = true;

        if (!empty($this->template)) {
            global $title;
            global $l;

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

<?php

declare(strict_types=1);

namespace BNT\Controller;

use Exception;
use BNT\Exception\WarningException;
use BNT\Exception\InfoException;
use BNT\Exception\ErrorException;
use BNT\Exception\SuccessException;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\User\DAO\UserUpdateDAO;

abstract class BaseController extends \UUA\Unit
{

    use \UUA\Traits\ContainerTrait;
    use \UUA\Traits\BuildTrait;

    public ?array $headers = null;
    public ?string $acceptType = null;
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

    protected function responseHtml(string $template): void
    {
        global $l;
        global $title;

        $title = $this->title;

        include $template;

        $title . $l->yes;
    }

    protected function responseJsonByException(\Throwable $ex): array
    {
        return [
            'success' => match (true) {
                $ex instanceof InfoException => true,
                $ex instanceof SuccessException => true,
                default => false,
            },
            'type' => match (true) {
                $ex instanceof ErrorException => 'danger',
                $ex instanceof WarningException => 'warning',
                $ex instanceof InfoException => 'info',
                $ex instanceof SuccessException => 'success',
                default => 'primary',
            },
            'message' => $ex->getMessage(),
            'code' => $ex->getCode(),
        ];
    }

    protected function preProcess(): void
    {
        
    }

    protected function process(): void
    {
        try {
            $this->preProcess();

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
        } catch (Exception $ex) {
            $this->exception = $ex;
        }
    }

    protected function processPost(): void
    {
        try {
            if ($this->isAcceptType('html')) {
                $this->processPostAsHtml();
            }

            if ($this->isAcceptType('json')) {
                $this->processPostAsJson();
            }
        } catch (Exception $ex) {
            $this->exception = $ex;
        }
    }

    protected function processGet(): void
    {

        if ($this->isAcceptType('html')) {
            $this->processGetAsHtml();
        }

        if ($this->isAcceptType('json')) {
            $this->processGetAsJson();
        }
    }

    protected function processPostAsHtml(): void
    {
        throw new WarningException('Not implemented');
    }

    protected function processPostAsJson(): void
    {
        throw new WarningException('Not implemented');
    }

    protected function processGetAsHtml(): void
    {
        throw new WarningException('Not implemented');
    }

    protected function processGetAsJson(): void
    {
        throw new WarningException('Not implemented');
    }

    protected function render(string $template): void
    {
        $this->template = $template;
    }

    protected function fromParsedBody(string $name, ?string $requiredText = null): mixed
    {
        return ($this->parsedBody[$name] ?? null) ?: ($requiredText ? throw new WarningException($requiredText) : null);
    }

    protected function fromQueryParams(string $name, ?string $requiredText = null): mixed
    {
        return ($this->queryParams[$name] ?? null) ?: ($requiredText ? throw new WarningException($requiredText) : null);
    }

    #[\Override]
    public function serve(): void
    {
        global $userinfo;
        global $playerinfo;

        $this->headers ??= getallheaders();
        $this->acceptType ??= $_SERVER['HTTP_ACCEPT'] ?? (getallheaders()['Accept'] ?? 'text/html');
        $this->playerinfo ??= $playerinfo;
        $this->userinfo ??= $userinfo;
        $this->requestMethod ??= $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->queryParams ??= $_GET ?? [];
        $this->parsedBody ??= $_POST ?? [];

        if (!$this->auth()) {
            $this->prepareResponse();
            return;
        }

        $this->process();
        $this->prepareResponse();
    }

    protected function isAcceptType(string $type): bool
    {
        return str_contains($this->acceptType, match ($type) {
            'html' => 'text/html',
            'json' => 'application/json',
            default => $type,
        });
    }

    protected function checkTurns(): void
    {
        global $l;

        if (empty($this->playerinfo)) {
            return;
        }

        if ($this->playerinfo['turns'] < 1) {
            throw new WarningException($l->move_turn);
        }
    }

    protected function playerinfoUpdate(): void
    {
        if (empty($this->playerinfo)) {
            throw new ErrorException('playerinfo not define');
        }

        ShipUpdateDAO::call($this->container, $this->playerinfo, $this->playerinfo['ship_id']);
    }

    protected function userinfoUpdate(): void
    {
        if (empty($this->userinfo)) {
            throw new ErrorException('userinfo not define');
        }
        
        UserUpdateDAO::call($this->container, $this->userinfo, $this->userinfo['id']);
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

        if (count(array_filter([$this->template, $this->exception, $this->location])) != 1) {
            throw new \Exception('must be one action');
        }

        foreach ($this->responseCookies as $name => $cookies) {
            setcookie($name, ...$cookies);
        }

        if (!empty($this->template)) {
            $this->responseHtml($this->template);
        }

        if (!empty($this->location)) {
            header('Location: ' . $this->location, true, 302);
        }

        if (!empty($this->exception)) {
            if ($this->isAcceptType('json')) {
                header('Content-Type: application/json');
                echo json_encode($this->responseJsonByException($this->exception), JSON_UNESCAPED_UNICODE);
            }

            if ($this->isAcceptType('html')) {
                header('Content-Type: text/html');
                $this->responseHtml('tpls/error.tpl.php');
            }
        }
    }
}

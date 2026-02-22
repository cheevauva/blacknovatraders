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
use BNT\Language;

abstract class BaseController extends \UUA\Unit
{

    use \UUA\Traits\ContainerTrait;
    use \UUA\Traits\BuildTrait;

    public const ACCEPT_TYPE_JSON = 'application/json';
    public const ACCEPT_TYPE_HTML = 'text/html';

    public Language $l;
    public ?string $acceptType;
    public ?string $template = null;
    public string $requestMethod;
    public array $parsedBody;
    public array $queryParams;
    public protected(set) ?string $location = null;
    public string $title = 'BlackNova Traders';
    public array $responseCookies = [];
    public ?\ArrayObject $responseJson = null;
    public bool $enableThrowExceptionOnProcess = false;
    public bool $enableCheckAuth = true;
    public bool $enableCheckShip = true;
    public bool $enableCheckUser = true;
    public bool $processedPost = false;
    public bool $processedGet = false;
    public ?\Throwable $exception = null;
    public ?array $userinfo = null;
    public ?array $playerinfo = null;

    protected function init(): void
    {
        global $l;

        $this->l = $l;
    }

    protected function redirectTo($location): void
    {
        $this->location = $location;
    }

    protected function setCookie(string $name, string $value = "", int $expires_or_options = 0, string $path = "", string $domain = "", bool $secure = false, bool $httponly = false): void
    {
        $this->responseCookies[$name] = [$value, $expires_or_options, $path, $domain, $secure, $httponly];
    }

    protected function preProcess(): void
    {
        
    }

    protected function process(): void
    {
        $this->acceptType ?? throw new WarningException('acceptType is required');
        $this->requestMethod ?? throw new WarningException('requestMethod is required');
        
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
                default:
                    throw new WarningException(sprintf('%s method processing is not implemented', $this->requestMethod));
            }
        } catch (Exception $ex) {
            $this->exception = $ex;

            if ($this->enableThrowExceptionOnProcess) {
                throw $ex;
            }
        }
    }

    protected function processPost(): void
    {
        if ($this->isAcceptType('html')) {
            $this->processPostAsHtml();
            return;
        }

        if ($this->isAcceptType('json')) {
            $this->processPostAsJson();
            return;
        }

        throw new WarningException(sprintf('POST method processing for %s is not implemented', $this->acceptType));
    }

    protected function processGet(): void
    {
        if ($this->isAcceptType('html')) {
            $this->processGetAsHtml();
            return;
        }

        if ($this->isAcceptType('json')) {
            $this->processGetAsJson();
            return;
        }
        throw new WarningException(sprintf('GET method processing for %s is not implemented', $this->acceptType));
    }

    protected function processPostAsHtml(): void
    {
        throw new WarningException('Processing of the POST method as HTML is not implemented');
    }

    protected function processPostAsJson(): void
    {
        throw new WarningException('Processing of the POST method as JSON is not implemented');
    }

    protected function processGetAsHtml(): void
    {
        throw new WarningException('Processing of the GET method as HTML is not implemented');
    }

    protected function processGetAsJson(): void
    {
        throw new WarningException('Processing of the GET method as JSON is not implemented');
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
        if (!$this->auth()) {
            return;
        }

        $this->process();
    }

    protected function isAcceptType(string $type): bool
    {
        return str_contains($this->acceptType, match ($type) {
            'html' => self::ACCEPT_TYPE_HTML,
            'json' => self::ACCEPT_TYPE_JSON,
            default => $type,
        });
    }

    protected function checkTurns(): void
    {
        if (empty($this->playerinfo)) {
            return;
        }

        if ($this->playerinfo['turns'] < 1) {
            throw new WarningException($this->l->move_turn);
        }
    }

    public function isAdmin(): bool
    {
        if (empty($this->userinfo)) {
            return false;
        }

        return $this->userinfo['role'] == 'admin';
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
}

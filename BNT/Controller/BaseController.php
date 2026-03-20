<?php

declare(strict_types=1);

namespace BNT\Controller;

use Exception;
use BNT\Translate;
use BNT\Exception\CommonException;
use BNT\Exception\WarningException;
use BNT\Exception\ErrorException;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\User\DAO\UserUpdateDAO;
use BNT\Language;
use BNT\Fetch;

abstract class BaseController extends \UUA\Unit
{

    use \UUA\Traits\ContainerTrait;
    use \UUA\Traits\BuildTrait;
    use \BNT\Traits\MessagesTrait;

    public const ACCEPT_TYPE_JSON = 'application/json';
    public const ACCEPT_TYPE_HTML = 'text/html';

    public Language $l;
    public ?string $acceptType;
    public ?string $template = null;
    public string $requestMethod;
    public array $parsedBody;
    public array $queryParams;
    public protected(set) ?string $location = null;
    public ?Translate $title = null;
    public array $responseCookies = [];
    public null|array|\ArrayObject $responseJson = null;
    public bool $enableThrowExceptionOnProcess = false;
    public bool $enableCheckAuth = true;
    public bool $enableCheckShip = true;
    public bool $enableCheckUser = true;
    public bool $processedPost = false;
    public bool $processedGet = false;
    public ?\Throwable $exception = null;
    public ?array $userinfo = null;
    public ?array $playerinfo = null;

    protected function redirectTo($route, array|string $params = []): void
    {
        if (!empty($this->messages)) {
            $this->responseJson = [
                'success' => true,
                'type' => 'redirectAfterMessages',
                'redirectTo' => route($route, $params),
                'messages' => array_map(fn(Translate $t) => (string) $t->l($this->l), $this->messages),
            ];
        } else {
            $this->location = route($route, $params);
        }
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
        try {
            $this->acceptType ?? throw new WarningException('acceptType is required');
            $this->requestMethod ?? throw new WarningException('requestMethod is required');

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
                    throw new WarningException()->t('[method] method processing is not implemented', ['method' => $this->requestMethod]);
            }
        } catch (Exception $ex) {
            if ($ex instanceof CommonException) {
                $ex->language($this->l);

                $translatedMessage = strval($ex);

                if (!$ex->getMessage() && $translatedMessage) {
                    $reflection = new \ReflectionObject($ex);
                    $property = $reflection->getProperty('message');
                    $property->setAccessible(true);
                    $property->setValue($ex, $translatedMessage);
                }
            }

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

    protected function fromParsedBody(string $name): Fetch
    {
        return $this->fetch($this->parsedBody, $name);
    }

    protected function fromQueryParams(string $name): Fetch
    {
        return $this->fetch($this->queryParams, $name);
    }

    protected function t(array|string $tag, array $replace = []): Translate
    {
        return $this->l->t($tag, $replace);
    }

    protected function fetch(array $data, ?string $path = null): Fetch
    {
        $fetch = new Fetch($data);
        $fetch->language($this->l);
        $fetch->messageTemplate('required', $this->t(['[label]', 'l_is_required']));
        $fetch->messageTemplate('not_empty', $this->t(['[label]', 'l_is_not_empty']));
        $fetch->messageTemplate('filter_is_invalid', $this->t(['[label]', 'l_is_invalid']));
        $fetch->messageTemplate('not_allow_value', $this->t(['[label]', 'l_is_contains_not_allow_value']));

        if ($path) {
            return $fetch->path($path);
        }

        return $fetch;
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
            throw new WarningException('l_move_turn');
        }
    }

    public function isAdmin(): bool
    {
        if (empty($this->userinfo)) {
            return false;
        }

        return $this->userinfo['role'] == 'admin';
    }

    protected function playerinfoTurn(int $turns = 1): void
    {
        $this->playerinfo ?? throw new ErrorException('playerinfo not define');
        $this->playerinfo['turns'] -= $turns;
        $this->playerinfo['turns_used'] += $turns;
    }

    protected function playerinfoUpdate(): void
    {
        $this->playerinfo ?? throw new ErrorException('playerinfo not define');

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
                $this->redirectTo('login');
                return false;
            }
        }

        if ($this->enableCheckShip) {
            if (empty($this->playerinfo)) {
                $this->redirectTo('ships');
                return false;
            }

            if ($this->playerinfo['ship_destroyed'] === 'Y') {
                $this->redirectTo('ships');
                return false;
            }
        }

        return true;
    }
}

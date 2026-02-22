<?php

declare(strict_types=1);

namespace BNT;

use Psr\Container\ContainerInterface;
use BNT\Controller\BaseController;

class FrontController extends \UUA\Unit
{

    use \UUA\Traits\ContainerTrait;
    use \UUA\Traits\BuildTrait;

    public BaseController $controller;

    #[\Override]
    public function serve(): void
    {
        global $userinfo;
        global $playerinfo;

        $controller = $this->controller;
        $controller->acceptType ??= $_SERVER['HTTP_ACCEPT'] ?? 'text/html';
        $controller->playerinfo ??= $playerinfo ?? null;
        $controller->userinfo ??= $userinfo ?? null;
        $controller->requestMethod ??= $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $controller->queryParams ??= $_GET ?? [];
        $controller->parsedBody ??= $_POST ?? [];
        $controller->serve();

        if (count(array_filter([$controller->template, $controller->exception, $controller->location])) != 1) {
            throw new \Exception('must be one action');
        }

        foreach ($controller->responseCookies as $name => $cookies) {
            setcookie($name, ...$cookies);
        }

        if (!empty($controller->template)) {
            $this->responseHtml($controller->template);
        }

        if (!empty($controller->location)) {
            header('Location: ' . $controller->location, true, 302);
        }

        if (!empty($controller->exception)) {
            if (str_contains($controller->acceptType, 'application/json')) {
                header('Content-Type: application/json');
                echo json_encode($this->responseJsonByException($controller->exception), JSON_UNESCAPED_UNICODE);
            }

            if (str_contains($controller->acceptType, 'text/html')) {
                header('Content-Type: text/html');
                $this->responseHtml('tpls/error.tpl.php');
            }
        }
    }

    protected function responseHtml(string $template): void
    {
        global $title;
        global $l;

        $self = $this->controller;
        $title = $this->controller->title;

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

    public static function call(ContainerInterface $container, string|BaseController $controller): self
    {
        if (is_string($controller)) {
            $controller = $controller::new($container);
        }

        $self = self::new($container);
        $self->controller = $controller;
        $self->serve();

        return $self;
    }
}

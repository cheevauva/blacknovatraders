<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Language;

abstract class BaseController extends \UUA\Unit
{

    use \UUA\Traits\ContainerTrait;
    use \UUA\Traits\BuildTrait;

    public array $_POST;
    public array $_GET;
    public protected(set) string $location;

    protected function fromRequest($name, $default = null)
    {
        $fromGet = fromGET($name);

        if ($fromGet) {
            return $fromGet;
        }

        return fromPOST($name, $default);
    }

    protected function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    protected function redirectTo($location)
    {
        $this->location = $location;
    }

    protected function processPost(): void
    {
        
    }

    protected function processGet(): void
    {
        
    }

    protected function render(string $template): void
    {
        $l = new Language();
        
        include $template;
    }

    #[\Override]
    public function serve(): void
    {
        $this->_GET ??= $_GET;
        $this->_POST ??= $_POST;

        if ($this->method() === 'GET') {
            $this->processGet();
        }

        if ($this->method() === 'POST') {
            $this->processPost();
        }
        if (!empty($this->location)) {
            header('Location: ' . $this->location, true, 302);
        }
    }
}

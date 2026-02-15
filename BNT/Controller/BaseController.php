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
    public protected(set) string $location;
    public string $title = 'BlackNova Traders';

    protected function fromRequest($name, $default = null)
    {
        $fromGet = fromGET($name);

        if ($fromGet) {
            return $fromGet;
        }

        return fromPOST($name, $default);
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
        $this->template = $template;
    }

    #[\Override]
    public function serve(): void
    {
        $this->requestMethod ??= $_SERVER['REQUEST_METHOD'];
        $this->queryParams ??= $_GET;
        $this->parsedBody ??= $_POST;

        switch ($this->requestMethod) {
            case 'GET':
                $this->processGet();
                break;
            case 'POST':
                $this->processPost();
                break;
        }

        $this->prepareResponse();
    }

    protected function prepareResponse(): void
    {
        if (!empty($this->template)) {
            global $title;

            $l = new Language();
            $title = $this->title;

            include $this->template;
        }

        if (!empty($this->location)) {
            header('Location: ' . $this->location, true, 302);
        }
    }
}

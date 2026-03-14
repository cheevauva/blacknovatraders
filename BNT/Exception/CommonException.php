<?php

declare(strict_types=1);

namespace BNT\Exception;

use BNT\Language;
use BNT\Translate;

class CommonException extends \Exception
{

    protected ?Language $language = null;
    public Translate $translate;

    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        $this->translate = new Translate()->translate($message);

        parent::__construct($message, $code, $previous);
    }

    public function language(Language $language): void
    {
        $this->language = $language;
    }

    /**
     * @return static
     */
    public function t(array|string $tag, array $replace = [], ?string $format = null)
    {
        $this->translate = new Translate()->translate($tag, $replace, $format);

        return $this;
    }

    /**
     * @return static
     */
    public function tt(Translate $translate)
    {
        $this->translate = $translate;

        return $this;
    }

    #[\Override]
    public function __toString(): string
    {
        if (!empty($this->language)) {
            $this->message = (string) $this->translate->l($this->language);
        }

        return $this->getMessage();
    }
}

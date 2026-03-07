<?php

declare(strict_types=1);

namespace BNT\Exception;

use BNT\Language;

class CommonException extends \Exception
{

    protected ?Language $language = null;
    public array $tags = [];
    public ?string $format = null;
    protected array $replace = [];

    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        if (strpos($message, 'l_') === 0) {
            $this->tags[] = $message;
        }

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
        if (is_array($tag)) {
            $this->tags = $tag;
        } else {
            $this->tags[] = $tag;
        }

        $this->replace = $replace;
        $this->format = $format;

        return $this;
    }

    #[\Override]
    public function __toString(): string
    {
        if (!empty($this->tags) && !empty($this->language)) {
            $this->message = $this->language->t($this->tags, $this->replace, $this->format);
        }

        return $this->getMessage();
    }
}

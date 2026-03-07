<?php

declare(strict_types=1);

namespace BNT;

use BNT\Language;

class Translate
{

    protected ?Language $language = null;
    public array $tags = [];
    public ?string $format = null;
    protected array $replace = [];

    public function language(Language $language): void
    {
        $this->language = $language;
    }

    /**
     * @return static
     */
    public function translate(array|string $tag, array $replace = [], ?string $format = null)
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

    public function __toString(): string
    {
        $tags = $this->tags;

        foreach ($tags as $idxTag => $tag) {
            if ($this->language && strpos($tag, 'l_') === 0) {
                $tag = $this->language->$tag;
            }

            foreach ($this->replace as $search => $replace) {
                $tag = str_replace('[' . $search . ']', (string) $replace, $tag);
            }

            $tags[$idxTag] = $tag;
        }
    
        return $this->format ? vsprintf($this->format, $tags) : implode(' ', $tags);
    }
}

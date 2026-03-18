<?php

declare(strict_types=1);

namespace BNT;

use BNT\Language;

class Translate
{

    use \UUA\Traits\AsTrait;

    protected ?Language $language = null;
    protected array $tags = [];
    protected ?string $format = null;
    protected array $replace = [];

    public function l(Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function replace(string $slug, mixed $value): self
    {
        $this->replace[$slug] = $value;
        
        return $this;
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
            if ($this->language && strpos(strval($tag), 'l_') === 0) {
                $tag = $this->language->$tag;
            }

            foreach ($this->replace as $search => $replace) {
                if ($this->language && $replace instanceof self) {
                    $replace->l($this->language);
                }
                
                $tag = str_replace('[' . $search . ']', (string) $replace, $tag);
            }

            $tags[$idxTag] = $tag;
        }

        return $this->format ? vsprintf($this->format, $tags) : implode(' ', $tags);
    }
}

<?php

declare(strict_types=1);

namespace BNT\Traits;

use BNT\Translate;

trait MessagesTrait
{

    /**
     * @var array<Translate>
     */
    public protected(set) array $messages = [];

    protected function messagesAppend(string|array|Translate $messages): void
    {
        if (is_string($messages)) {
            $messages = new Translate($messages);
        }
        
        if ($messages instanceof Translate) {
            $messages = [$messages];
        }

        foreach ($messages as $message) {
            $this->messages[] = $message;
        }
    }
}

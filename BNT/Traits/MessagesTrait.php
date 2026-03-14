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

    protected function messagesAppend(array|Translate $messages): void
    {
        if ($messages instanceof Translate) {
            $messages = [$messages];
        }

        foreach ($messages as $message) {
            $this->messages[] = $message;
        }
    }
}

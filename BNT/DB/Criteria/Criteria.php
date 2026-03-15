<?php

declare(strict_types=1);

namespace BNT\DB\Criteria;

abstract class Criteria
{

    public string $field;
    public mixed $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return sprintf('%s = :%s', $this->field, $this->field);
    }
}

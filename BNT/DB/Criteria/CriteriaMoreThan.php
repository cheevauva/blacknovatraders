<?php

declare(strict_types=1);

namespace BNT\DB\Criteria;

class CriteriaMoreThan extends Criteria
{

    #[\Override]
    public function __toString(): string
    {
        return sprintf('%s > :%s', $this->field, $this->field);
    }
}

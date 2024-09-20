<?php

declare(strict_types=1);

namespace BNT\ADODB;

use Doctrine\DBAL\Result;

class ADODBResult
{
    private Result $result;
    private \Iterator $iterator;

    public function __construct(Result $result)
    {
        $this->result = $result;
        $this->iterator = $result->iterateAssociative();
    }

    public function __get($name)
    {
        if ($name === 'fields') {
            return $this->iterator->current();
        }
        if ($name === 'EOF') {
            return !$this->iterator->valid();
        }
    }

    public function RecordCount(): int
    {
        return $this->result->rowCount();
    }

    public function Movenext(): void
    {
        $this->iterator->next();
    }
}

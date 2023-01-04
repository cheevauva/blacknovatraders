<?php

declare(strict_types=1);

namespace BNT\Log\DAO;

use BNT\ServantInterface;
use BNT\TableEnum;
use BNT\Log\Log;
use BNT\Log\Mapper\LogMapper;
use BNT\DatabaseTrait;

abstract class LogDAO implements ServantInterface
{

    use DatabaseTrait;

    protected function table(): string
    {
        return TableEnum::Logs->toDb();
    }

    protected function mapper(): LogMapper
    {
        return new LogMapper;
    }

    public function asLog(array $row): Log
    {
        $self = new LogMapper;
        $self->row = $row;
        $self->serve();

        return $self->log;
    }

    public function asLogs(array $rows): array
    {
        $logs = [];

        foreach ($rows as $row) {
            $logs[] = $this->asLog($row);
        }

        return $logs;
    }

}

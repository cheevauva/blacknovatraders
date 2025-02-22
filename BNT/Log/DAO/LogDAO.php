<?php

declare(strict_types=1);

namespace BNT\Log\DAO;

use BNT\DAO;
use BNT\Enum\TableEnum;
use BNT\Log\Entity\Log;
use BNT\Log\Mapper\LogMapper;


abstract class LogDAO extends DAO
{
    

    protected function table(): string
    {
        return TableEnum::Logs->toDb();
    }

    protected function mapper(): LogMapper
    {
        return new LogMapper;
    }

    protected function asLog(array $row): Log
    {
        $mapper = $this->mapper();
        $mapper->row = $row;
        $mapper->serve();

        return $mapper->log;
    }

    protected function asLogs(array $rows): array
    {
        $logs = [];

        foreach ($rows as $row) {
            $logs[] = $this->asLog($row);
        }

        return $logs;
    }
}

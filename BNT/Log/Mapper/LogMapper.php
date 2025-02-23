<?php

declare(strict_types=1);

namespace BNT\Log\Mapper;

use BNT\Mapper;
use BNT\Log\Enum\LogTypeEnum;
use BNT\Log\Entity\Log;

class LogMapper extends Mapper
{

    public Log $log;
    public array $row;

    public function serve(): void
    {
        if (!empty($this->log) && empty($this->row)) {
            $this->row = $this->log2row($this->log);
        }

        if (!empty($this->row) && empty($this->log)) {
            $this->log = $this->row2log($this->row);
        }
    }

    protected function row2log(array $row): Log
    {
        $log = new Log();
        $log->type = LogTypeEnum::from($row['type']);
        $log->ship_id = intval($row['ship_id']);
        $log->time = !empty($row['time']) ? new \DateTimeImmutable($row['time']) : null;
        $log->payload = json_decode($row['data'] ?? '{}', true);

        return $log;
    }

    protected function log2row(Log $log): array
    {
        $row = [];
        $row['ship_id'] = $log->ship_id;
        $row['type'] = $log->type->value;
        $row['time'] = !empty($log->time) ? $log->time->format('Y-m-d H:i:s') : null;
        $row['data'] = json_encode($log->payload, JSON_UNESCAPED_UNICODE);

        return $row;
    }

}

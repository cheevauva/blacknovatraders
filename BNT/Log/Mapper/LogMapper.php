<?php

declare(strict_types=1);

namespace BNT\Log\Mapper;

use BNT\Mapper;
use BNT\Log\Enum\LogTypeEnum;
use BNT\Log\Entity\Log;
use BNT\Log\Entity\LogWithIP;
use BNT\Log\Entity\LogWithPlayer;
use BNT\Log\Entity\LogRaw;
use BNT\Log\Entity\LogDefenceDestroyedFighters;
use BNT\Log\Entity\LogBadLogin;
use BNT\Log\Entity\LogLogin;
use BNT\Log\Entity\LogLogout;

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

    protected function getClassByType(LogTypeEnum $type): string
    {
        return match ($type) {
            LogTypeEnum::BADLOGIN => LogBadLogin::class,
            LogTypeEnum::LOGIN => LogLogin::class,
            LogTypeEnum::LOGOUT => LogLogout::class,
            default => var_dump($type),
        };
    }

    protected function row2log(array $row): Log
    {
        $data = json_decode($row['data'] ?? '{}', true);

        $log = new ($this->getClassByType(LogTypeEnum::from($row['type'])));
        $log->ship_id = intval($row['ship_id']);
        $log->time = !empty($row['time']) ? new \DateTime($row['time']) : null;

        if ($log instanceof LogWithIP) {
            $log->ip = $data['ip'];
        }

        if ($log instanceof LogWithPlayer) {
            $log->player = $data['player'];
        }

        if ($log instanceof LogRaw) {
            $log->message = $data['message'];
        }

        if ($log instanceof LogDefenceDestroyedFighters) {
            $log->fighterslost = $data['fighterslost'];
            $log->sector = $data['sector'];
        }

        return $log;
    }

    protected function log2row(Log $log): array
    {
        $row = [];
        $row['ship_id'] = $log->ship_id;
        $row['type'] = $log->type->value;
        $row['time'] = !empty($log->time) ? $log->time->format('Y-m-d H:i:s') : null;

        $data = [];

        if ($log instanceof LogWithIP) {
            $data['ip'] = $log->ip;
        }

        if ($log instanceof LogWithPlayer) {
            $data['player'] = $log->player;
        }

        if ($log instanceof LogRaw) {
            $data['message'] = $log->message;
        }

        if ($log instanceof LogDefenceDestroyedFighters) {
            $data['sector'] = $log->sector;
            $data['fighterslost'] = $log->fighterslost;
        }

        $data['entity_type'] = get_class($log);

        $row['data'] = json_encode($data);

        return $row;
    }
}

<?php

declare(strict_types=1);

namespace BNT\Log\DAO;

use BNT\Log\Entity\Log;

class LogCreateDAO extends LogDAO
{
    public Log $log;

    public function serve(): void
    {
        $mapper = $this->mapper();
        $mapper->log = $this->log;
        $mapper->serve();

        $this->db()->insert($this->table(), $mapper->row);

        $this->log->log_id = intval($this->db()->lastInsertId());
    }

    public static function call(\Psr\Container\ContainerInterface $container, Log $log): self
    {
        $self = static::new($container);
        $self->log = $log;
        $self->serve();

        return $self;
    }
}

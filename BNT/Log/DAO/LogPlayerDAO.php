<?php

declare(strict_types=1);

namespace BNT\Log\DAO;

use Psr\Container\ContainerInterface;

class LogPlayerDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $ship;
    public int $type;
    public mixed $data;

    public function serve(): void
    {
        if (empty($this->ship) || empty($this->type)) {
            return;
        }

        if (is_array($this->data)) {
            $this->data = implode('|', $this->data);
        }

        $this->db()->q("INSERT INTO logs VALUES(NULL, :sid, :log_type, NOW(), :data)", [
            'sid' => $this->ship,
            'log_type' => $this->type,
            'data' => $this->data,
        ]);
    }

    public static function call(ContainerInterface $container, int $ship, int $type, mixed $data = null): self
    {
        $self = self::new($container);
        $self->ship = $ship;
        $self->type = $type;
        $self->data = $data;
        $self->serve();

        return $self;
    }
}

<?php

declare(strict_types=1);

namespace BNT\Config\DAO;

use Psr\Container\ContainerInterface;

class ConfigUpdateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    /**
     * @var array<string, mixed>
     */
    public array $config;

    #[\Override]
    public function serve(): void
    {
        foreach ($this->config as $name => $value) {
            $this->db()->q('REPLACE INTO config SET value = :value , name = :name', [
                'name' => $name,
                'value' => $value,
            ]);
        }
    }

    /**
     * @param ContainerInterface $container
     * @param array<string, mixed> $config
     * @return static
     */
    public static function call(ContainerInterface $container, array $config): self
    {
        $self = self::new($container);
        $self->config = $config;
        $self->serve();

        return $self;
    }
}

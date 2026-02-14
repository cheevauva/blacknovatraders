<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

use Psr\Container\ContainerInterface;

class PlanetByIdDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $id;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $planet;

    #[\Override]
    public function serve(): void
    {
        $this->planet = $this->db()->fetch('SELECT * FROM planets WHERE planet_id = :id', [
            'id' => $this->id,
        ]);
    }

    public static function call(ContainerInterface $container, int $planet): self
    {
        $self = self::new($container);
        $self->planet = $planet;
        $self->serve();

        return $self;
    }
}

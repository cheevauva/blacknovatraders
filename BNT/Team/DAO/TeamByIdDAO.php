<?php

declare(strict_types=1);

namespace BNT\Team\DAO;

use Psr\Container\ContainerInterface;

class TeamByIdDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $id;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $team;

    #[\Override]
    public function serve(): void
    {
        $this->team = $this->db()->fetch('SELECT * FROM teams WHERE id= :team LIMIT 1', [
            'team' => $this->id,
        ]);
    }

    public static function call(ContainerInterface $container, int $id): self
    {
        $self = self::new($container);
        $self->id = $id;
        $self->serve();

        return $self;
    }
}

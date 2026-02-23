<?php

declare(strict_types=1);

namespace BNT\Team\DAO;

use Psr\Container\ContainerInterface;

class TeamByIdDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowSelectByIdTrait;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $team;

    #[\Override]
    public function serve(): void
    {
        $this->team = $this->selectRow('teams', 'id');
    }
}

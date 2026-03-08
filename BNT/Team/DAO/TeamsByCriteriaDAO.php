<?php

declare(strict_types=1);

namespace BNT\Team\DAO;

class TeamsByCriteriaDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowsSelectByCriteriaTrait;
    
    public array $teams;

    #[\Override]
    public function serve(): void
    {
        $this->teams = $this->selectRows('teams');
    }
}

<?php

declare(strict_types=1);

namespace BNT\Sector\DAO;

class SectorsByCriteriaDAO extends \UUA\DAO
{
    use \BNT\Traits\DatabaseRowsSelectByCriteriaTrait;
    
    public array $sectors;
    
    
    #[\Override]
    public function serve(): void
    {
        $this->sectors = $this->selectRows('universe');
    }
}

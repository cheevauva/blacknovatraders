<?php

declare(strict_types=1);

namespace BNT\Migration\DAO;

class MigrationExecuteDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public $migration;

    #[\Override]
    public function serve(): void
    {
        $this->db()->q($this->migration['query']);
        $this->db()->q('INSERT INTO migrations SET migration = :migration, date_execution = NOW()', [
            'migration' => $this->migration['migration'],
        ]);
    }
}

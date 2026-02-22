<?php

declare(strict_types=1);

namespace BNT\Migration\DAO;

class MigrationsCreateTableDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;
    use \BNT\Traits\UnitSimpleCallTrait;

    #[\Override]
    public function serve(): void
    {
        $this->db()->q('CREATE TABLE IF NOT EXISTS migrations (migration text, date_execution datetime)');
    }
}

<?php

declare(strict_types=1);

namespace BNT\Traits;

use Doctrine\DBAL\Connection;

trait DatabaseTrait
{

    protected function db(): Connection
    {
        global $db;

        $db = $db ?? connectdb();

        return $db->getConnection();
    }
}

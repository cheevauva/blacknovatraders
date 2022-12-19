<?php

declare(strict_types=1);

namespace BNT;

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

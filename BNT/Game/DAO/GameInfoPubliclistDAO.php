<?php

declare(strict_types=1);

namespace BNT\Game\DAO;

class GameInfoPubliclistDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;
    use \BNT\Traits\UnitSimpleCallTrait;
    
    public array $info;

    #[\Override]
    public function serve(): void
    {
        $sql = "
        SELECT 'START-DATE' AS name, UNIX_TIMESTAMP(time) AS value FROM movement_log WHERE event_id = 1
        UNION
        SELECT 'P-ALL' AS name, COUNT(*) AS value  FROM  ships
        UNION
        SELECT 'P-ACTIVE' AS name, COUNT(*) AS value FROM ships WHERE ship_destroyed = 'N'
        UNION
        SELECT 'P-HUMAN' AS name, COUNT(*) AS value FROM ships WHERE ship_destroyed = 'N' AND ship_name NOT LIKE '%@xenobe'
        UNION 
        SELECT 'P-ONLINE' AS name, COUNT(*) AS value FROM users WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(last_login)) / 60 <= 5 and email NOT LIKE '%@xenobe'
        UNION
        SELECT 
            'P-AI-LVL' AS name, 
            (
                SELECT 
                    (
                        IFNULL(AVG(hull), 0) +
                        IFNULL(AVG(engines), 0) +
                        IFNULL(AVG(power), 0) +
                        IFNULL(AVG(computer), 0) +
                        IFNULL(AVG(sensors), 0) +
                        IFNULL(AVG(beams), 0) +
                        IFNULL(AVG(torp_launchers), 0) +
                        IFNULL(AVG(shields), 0) +
                        IFNULL(AVG(armor), 0) +
                        IFNULL(AVG(cloak), 0) 
                    ) / 10
                FROM 
                    ships 
                WHERE 
                    ship_destroyed='N' AND
                    ship_name LIKE '%@xenobe'
            ) AS value
        UNION
        SELECT 'P-TOP' AS name, (SELECT GROUP_CONCAT(CONCAT(ship_name, ':', score) SEPARATOR ';') FROM ships WHERE ship_destroyed = 'N' ORDER BY score DESC LIMIT 3) AS value
        ";
        
        $this->info = $this->db()->fetchAllKeyValue($sql);
    }
}

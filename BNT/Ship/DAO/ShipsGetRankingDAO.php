<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use PDO;

class ShipsGetRankingDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public $ranking;
    public $sort;
    public $max_rank;

    #[\Override]
    public function serve(): void
    {
        $sort = $this->sort;

        if ($sort == "turns") {
            $by = "turns_used DESC,character_name ASC";
        } elseif ($sort == "login") {
            $by = "last_login DESC,character_name ASC";
        } elseif ($sort == "good") {
            $by = "rating DESC,character_name ASC";
        } elseif ($sort == "bad") {
            $by = "rating ASC,character_name ASC";
        } elseif ($sort == "alliance") {
            $by = "teams.team_name DESC, character_name ASC";
        } elseif ($sort == "efficiency") {
            $by = "efficiency DESC";
        } elseif ($sort == "online") {
            $by = "online DESC";
        } else {
            $by = "score DESC,character_name ASC";
        }

        $query = "
        SELECT 
            ships.ship_id,
            ships.email,
            ships.score,
            ships.character_name,
            ships.turns_used,
            ships.last_login,
            UNIX_TIMESTAMP(ships.last_login) as online,
            ships.rating, 
            teams.team_name, 
            IF(ships.turns_used<150,0,ROUND(ships.score/ships.turns_used)) AS efficiency 
        FROM 
            ships 
        LEFT JOIN 
            teams 
        ON 
            ships.team = teams.id  
        WHERE 
            ship_destroyed='N' AND 
            email NOT LIKE '%@xenobe' 
        ORDER BY $by 
        LIMIT :limit
        ";

        $this->ranking = $this->db()->fetchAll($query, [
            'limit' => $this->max_rank,
        ], [
            'limit' => PDO::PARAM_INT,
        ]);
    }
}

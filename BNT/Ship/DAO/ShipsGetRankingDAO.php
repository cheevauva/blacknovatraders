<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use PDO;

class ShipsGetRankingDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public array $ships;
    public $sort;
    public $max_rank;

    #[\Override]
    public function serve(): void
    {
        $sort = $this->sort;

        if ($sort == "turns") {
            $by = "turns_used DESC,ship_name ASC";
        } elseif ($sort == "good") {
            $by = "rating DESC,ship_name ASC";
        } elseif ($sort == "bad") {
            $by = "rating ASC,ship_name ASC";
        } elseif ($sort == "alliance") {
            $by = "teams.team_name DESC, ship_name ASC";
        } elseif ($sort == "efficiency") {
            $by = "efficiency DESC";
        } elseif ($sort == "online") {
            $by = "online DESC";
        } else {
            $by = "score DESC,ship_name ASC";
        }

        $query = "
        SELECT 
            ships.ship_id,
            ships.score,
            ships.ship_name,
            ships.turns_used,
            ships.rating, 
            teams.team_name, 
            IF(ships.turns_used < 150, 0, ROUND(ships.score/ships.turns_used)) AS efficiency 
        FROM 
            ships 
        LEFT JOIN 
            teams 
        ON 
            ships.team = teams.id  
        WHERE 
            ship_destroyed='N'
        ORDER BY $by 
        LIMIT :limit
        ";

        $this->ships = $this->db()->fetchAll($query, [
            'limit' => $this->max_rank,
        ], [
            'limit' => PDO::PARAM_INT,
        ]);
    }
}

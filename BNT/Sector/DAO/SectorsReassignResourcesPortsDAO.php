<?php

declare(strict_types=1);

namespace BNT\Sector\DAO;

class SectorsReassignResourcesPortsDAO extends \UUA\DAO
{
    use \BNT\Traits\DatabaseMainTrait;

    public int $oreSectorsCount;
    public int $organicsSectorsCount;
    public int $goodsSectorsCount;
    public int $energySectorsCount;
    public int $buyOre;
    public int $buyOrganics;
    public int $buyGoods;
    public int $buyEnergy;

    #[\Override]
    public function serve(): void
    {
        $portTypes = [
            'ore' => $this->oreSectorsCount,
            'organics' => $this->organicsSectorsCount,
            'goods' => $this->goodsSectorsCount,
            'energy' => $this->energySectorsCount,
        ];

        foreach ($portTypes as $portType => $limit) {
            $sql = "
            UPDATE 
                universe 
            SET 
                port_ore = :port_ore,
                port_organics = :port_organics,
                port_goods = :port_goods,
                port_energy = :port_energy,
                port_type = :port_type
            WHERE 
                port_type = 'none' AND 
                sector_id IN (
                    SELECT 
                        s.sector_id 
                    FROM 
                        (
                            SELECT 
                                sector_id 
                            FROM 
                                universe 
                            WHERE 
                                port_type = 'none' 
                            ORDER BY 
                                rand() DESC 
                            LIMIT :limit
                        ) AS s
                )
            ";
            $this->db()->q($sql, [
                'limit' => (int) $limit,
                'port_type' => $portType,
                'port_ore' => $this->buyOre,
                'port_organics' => $this->buyOrganics,
                'port_goods' => $this->buyGoods,
                'port_energy' => $this->buyEnergy,
            ], [
                'limit' => \PDO::PARAM_INT,
            ]);
        }
    }
}

<?php

declare(strict_types=1);

namespace BNT\Sector\DAO;

class SectorsReassignResourcesPortsDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $oreSectorsLimit;
    public int $organicsSectorsLimit;
    public int $goodsSectorsLimit;
    public int $energySectorsLimit;
    public int $buyOre;
    public int $buyOrganics;
    public int $buyGoods;
    public int $buyEnergy;
    public int $sellOre;
    public int $sellOrganics;
    public int $sellGoods;
    public int $sellEnergy;

    #[\Override]
    public function serve(): void
    {
        $portTypes = [
            'ore' => [$this->oreSectorsLimit, $this->sellOre, $this->buyOrganics, $this->buyGoods, $this->buyEnergy],
            'organics' => [$this->organicsSectorsLimit, $this->buyOre, $this->sellOrganics, $this->buyGoods, $this->buyEnergy],
            'goods' => [$this->goodsSectorsLimit, $this->buyOre, $this->buyOrganics, $this->sellGoods, $this->buyEnergy],
            'energy' => [$this->energySectorsLimit, $this->buyOre, $this->buyOrganics, $this->buyGoods, $this->sellEnergy],
        ];

        foreach ($portTypes as $portType => $resources) {
            $limit = $resources[0];
            $portOre = $resources[1];
            $portOrganics = $resources[2];
            $portGoods = $resources[3];
            $portEnergy = $resources[4];

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
                'port_ore' => $portOre,
                'port_organics' => $portOrganics,
                'port_goods' => $portGoods,
                'port_energy' => $portEnergy,
            ], [
                'limit' => \PDO::PARAM_INT,
            ]);
        }
    }
}

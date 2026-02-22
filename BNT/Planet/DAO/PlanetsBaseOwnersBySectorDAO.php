<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

use Psr\Container\ContainerInterface;

class PlanetsBaseOwnersBySectorDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $sector;
    public array $owners;

    #[\Override]
    public function serve(): void
    {
        $sql = "
        SELECT
            'S' AS type,
            owner AS id,
            COUNT(*) AS num
        FROM 
            planets
        WHERE 
            base = 'Y' AND 
            sector_id = :sector AND 
            owner != 0
        GROUP BY 
            owner 

        UNION

        SELECT
           'C' AS type, 
           corp AS id, 
           COUNT(*) AS num
        FROM 
            planets
        WHERE 
            base = 'Y' AND 
            sector_id = :sector AND 
            corp != 0
        GROUP BY 
            corp 
        ";

        $this->owners = $this->db()->fetchAll($sql, [
            'sector' => $this->sector,
        ]);
    }

    public static function call(ContainerInterface $container, int $sector): self
    {
        $self = self::new($container);
        $self->sector = $sector;
        $self->serve();

        return $self;
    }
}

<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

class ShipGetNotDestroyedExcludeXenobeCountDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public $count;

    public function serve(): void
    {
        $this->count = $this->db()->column("SELECT COUNT(*) AS num_players FROM ships WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe'");
    }

    /**
     * @param type $container
     * @param type $ship
     * @param type $id
     * @return self
     */
    public static function call($container)
    {
        $self = self::new($container);
        $self->serve();
        
        return $serve;
    }
}

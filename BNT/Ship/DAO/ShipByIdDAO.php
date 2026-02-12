<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

class ShipByIdDAO extends \UUA\DAO
{

    /**
     * @var int
     */
    public $id;

    /**
     * @var array
     */
    public $ship;

    public function serve(): void
    {
        $this->ship = db()->fetch("SELECT * FROM ships WHERE ship_id = :id LIMIT 1", [
            'id' => $this->id,
        ]);
    }
    
    /**
     * 
     * @param type $container
     * @param id $id
     * @return self
     */
    public static function call($container, $id)
    {
        $self = self::new($container);
        $self->id = $id;
        $self->serve();
        
        return $self;
    }
    
}

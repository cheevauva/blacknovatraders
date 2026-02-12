<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

class ShipByTokenDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    /**
     * @var string
     */
    public $token;

    /**
     * @var array
     */
    public $ship;

    public function serve(): void
    {
        $this->ship = $this->db()->fetch("SELECT * FROM ships WHERE token = :token LIMIT 1", [
            'token' => $this->token,
        ]);
    }

    /**
     * @param type $container
     * @param string $token
     * @return self
     */
    public static function call($container, $token)
    {
        $self = self::new($container);
        $self->token = $token;
        $self->serve();

        return $self;
    }
}

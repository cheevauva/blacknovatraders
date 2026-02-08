<?php

//declare(strict_types=1);

namespace BNT\Log\DAO;

class LogPlayerDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public $ship;
    public $type;
    public $data;

    public function serve()
    {
        if (empty($this->ship) || empty($this->type)) {
            return;
        }

        if (is_array($this->data)) {
            $this->data = implode('|', $this->data);
        }

        db()->q("INSERT INTO logs VALUES(NULL, :sid, :log_type, NOW(), :data)", [
            'sid' => $this->ship,
            'log_type' => $this->type,
            'data' => $this->data,
        ]);
    }
}

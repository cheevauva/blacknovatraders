<?php

//declare(strict_types=1);

namespace BNT\ADODB;

use PDOStatement;
use PDO;

class ADOPDOStatement
{

    /**
     * @var PDOStatement
     */
    protected $stmt;
    private $fields;
    public $EOF;

    public function __construct(PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }

    public function RecordCount()
    {
        return $this->stmt->rowCount();
    }

    public function MoveNext()
    {
        $this->fields = $this->stmt->fetch(PDO::FETCH_ASSOC);
        $this->EOF = empty($this->fields);
    }

    public function __get($name)
    {
        if ($name == 'fields') {
            $this->MoveNext();
            return $this->fields;
        }
    }
}

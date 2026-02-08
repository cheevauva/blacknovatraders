<?php

//declare(strict_types=1);

namespace BNT\IBankAccount\DAO;

class IBankAccountCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    public $ibackAccount;

    public function serve()
    {
        $this->rowCreate('ibank_accounts', $this->ibackAccount);
    }
}

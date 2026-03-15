<?php

declare(strict_types=1);

namespace BNT\IBankAccount\DAO;

class IBankAccountExpointerDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $multiplier;

    #[\Override]
    public function serve(): void
    {
        global $ibank_interest;
        global $ibank_loaninterest;

        $exponinter = mypw($ibank_interest + 1, $this->multiplier);
        $expoloan = mypw($ibank_loaninterest + 1, $this->multiplier);

        $this->db()->q('UPDATE ibank_accounts SET balance = balance * :exponinter, loan=loan * :expoloan', [
            'exponinter' => $exponinter,
            'expoloan' => $expoloan,
        ]);
    }
}

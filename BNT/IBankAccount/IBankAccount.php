<?php

declare(strict_types=1);

namespace BNT\IBankAccount;

class IBankAccount
{

    public int $ship_id;
    public int $balance = 0;
    public int $loan = 0;
    public ?\DateTime $loantime = null;

}

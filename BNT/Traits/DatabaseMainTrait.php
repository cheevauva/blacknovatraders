<?php

declare(strict_types=1);

namespace BNT\Traits;

trait DatabaseMainTrait
{

    use \UUA\Traits\ContainerTrait;

    /**
     * @return \BNT\ADODB\ADOPDO
     */
    protected function db()
    {
        return $this->container->get('db');
    }
}

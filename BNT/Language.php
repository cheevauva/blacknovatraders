<?php

declare(strict_types=1);

namespace BNT;

class Language
{

    public function __get(string $name): mixed
    {
        global ${'l_' . $name};

        if (!isset(${'l_' . $name})) {
            return 'l_' . $name;
        }

        return ${'l_' . $name};
        ;
    }
}

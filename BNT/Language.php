<?php

declare(strict_types=1);

namespace BNT;

class Language
{

    protected static Language $instance;

    use \UUA\Traits\AsTrait;

    protected array $mapping = [
        'l_zi_' => 'zoneinfo.php',
        'l_ze_' => 'zoneedit.php',
        'l_login_' => 'login.php',
        'l_ships_' => 'ships.php',
        'warp_' => 'warpedit.php',
        'new_' => 'new.php',
    ];

    public static function instance(): Language
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __get(string $name): mixed
    {
        global $language;

        if (isset($GLOBALS['l_' . $name])) {
            return $GLOBALS['l_' . $name];
        }

        $slug = implode('_', array_slice(explode('_', 'l_' . $name, 3), 0, 2)) . '_';

        if (!empty($this->mapping[$slug])) {
            $languageSubFile = sprintf('languages/%s/%s', $language, $this->mapping[$slug]);

            if (file_exists($languageSubFile)) {
                include $languageSubFile;

                foreach (get_defined_vars() as $var => $val) {
                    $GLOBALS[$var] ??= $val;
                }
            }
        }

        if (isset($GLOBALS['l_' . $name])) {
            return $GLOBALS['l_' . $name];
        }

        return 'l_' . $name;
    }
}

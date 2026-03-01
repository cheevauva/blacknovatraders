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
        'l_warp_' => 'warpedit.php',
        'l_new_' => 'new.php',
        'l_planet_' => 'planet.php',
        'l_main_' => 'main.php',
        'l_log_' => 'log.php',
        'l_news_' => 'news.php',
        'l_ranks_' => 'ranking.php',
        'l_team_' => 'teams.php',
        'l_settings_' => 'settings.php',
        'l_device_' => 'device.php',
        'l_beacon_' => 'beacon.php',
        'l_opt_' => 'options.php',
        'l_gns_' => 'genesis.php',
        'l_ewd_' => 'emerwarp.php',
        'l_mines_' => 'mines.php',
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

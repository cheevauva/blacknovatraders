<?php

declare(strict_types=1);

namespace BNT;

use BNT\Translate;

class Language
{

    protected array $vars;

    use \UUA\Traits\AsTrait;

    protected static array $languages = [];
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
        'l_corpm_' => 'corp.php',
        'l_pre_' => 'preset.php',
        'l_sdf_' => 'defence_report.php',
        'l_pr_' => 'planet_report.php',
        'l_md_' => 'modify_defences.php',
        'l_die_' => 'self_destruct.php',
        'l_by_' => 'bounty.php',
        'l_ship_' => 'ship.php',
        'l_dump_' => 'dump.php',
        'l_map_' => 'galaxy.php',
        'l_nav_' => 'navcomp.php',
        'l_messages_' => 'messages.php',
        'l_create_universe_' => 'create_universe.php',
        'l_scan_' => 'scan.php',
        'l_lrs_' => 'lrscan.php',
        'l_chf_' => 'check_fighters.php',
        'l_chm_' => 'check_mines.php',
        'l_move_' => 'move.php',
    ];

    public function __construct(protected string $languageName)
    {
        include sprintf('languages/%s.php', $this->languageName);

        $this->setVars(get_defined_vars());
    }

    public static function get(string $language): Language
    {
        return self::$languages[$language] ??= new Language($language);
    }

    public function languageName(): string
    {
        return $this->languageName;
    }

    protected function setVars(array $vars): void
    {
        foreach ($vars as $var => $val) {
            if (strpos($var, 'l_') === 0) {
                $this->vars[$var] ??= $val;
            }
        }
    }

    public function t(array|string $tag, array $replace = [], ?string $format = null): string
    {
        $translate = new Translate;
        $translate->language($this);
        $translate->translate($tag, $replace, $format);

        return (string) $translate;
    }

    public function __get(string $var): mixed
    {
        if (isset($this->vars[$var])) {
            return $this->vars[$var];
        }

        $slug = implode('_', array_slice(explode('_', $var, 3), 0, 2)) . '_';

        if (!empty($this->mapping[$slug])) {
            $languageSubFile = sprintf('languages/%s/%s', $this->languageName, $this->mapping[$slug]);

            if (file_exists($languageSubFile)) {
                include $languageSubFile;

                $this->setVars(get_defined_vars());
            }
        }

        if (isset($this->vars[$var])) {
            return $this->vars[$var];
        }

        return $var;
    }
}

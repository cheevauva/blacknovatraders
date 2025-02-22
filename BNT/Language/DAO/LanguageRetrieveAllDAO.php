<?php

declare(strict_types=1);

namespace BNT\Language\DAO;

use BNT\DAO;
use BNT\Language\Language;

class LanguageRetrieveAllDAO extends DAO
{
    /**
     * @var array<Language>
     */
    public ?array $languages = null;

    public function serve(): void
    {
        global $avail_lang;

        $this->languages = [];

        foreach ($avail_lang as $key => $value) {
            $lang = new Language();
            $lang->file = $value['file'];
            $lang->name = $value['name'];

            $this->languages[] = $lang;
        }
    }
}

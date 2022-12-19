<?php

declare(strict_types=1);

namespace BNT\Language\DAO;

use BNT\ServantInterface;
use BNT\Language\Language;
use BNT\Language\DAO\LanguageRetrieveAllDAO;

class LangaugeRetrieveByFileDAO implements ServantInterface
{
    public string $file;
    public ?Language $language;

    public function serve(): void
    {
        $retrieveAll = new LanguageRetrieveAllDAO;
        $retrieveAll->serve();
        
        foreach ($retrieveAll->languages as $language) {
            if ($language->file === $this->file) {
                $this->language = $language;
                break;
            }
        }
    }

}

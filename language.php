<?php
declare(strict_types=1);

use BNT\Language\DAO\LanguageRetrieveAllDAO;
use BNT\Language\DAO\LangaugeRetrieveByFileDAO;

include 'config.php';

$lang = $lang ?? $default_lang;

$retrieveAll = new LanguageRetrieveAllDAO;
$retrieveAll->serve();

$languages = $retrieveAll->languages;
$newlang = $_POST['newlang'] ?? null;

if (!empty($newlang)) {
    $retrieveByFile = new LangaugeRetrieveByFileDAO;
    $retrieveByFile->file = $newlang;
    $retrieveByFile->serve();

    if (!empty($retrieveByFile->language)) {
        $lang = $newlang;
        SetCookie("lang", $lang, time() + (3600 * 24) * 365, $gamepath, $gamedomain);
    }
}
?>
<form action="language.php" method=POST>
    <?php echo $l_login_lang; ?>
    &nbsp;&nbsp;
    <select name=newlang>
        <?php foreach ($languages as $language) : ?>
            <?php assert($language instanceof \BNT\Language\Language); ?>
            <option value="<?php echo $language->file; ?>" <?php if ($language->file === $lang): ?>selected="selected"<?php endif; ?>>
                <?php echo $language->name; ?>
            </option>
        <?php endforeach; ?>
    </select>&nbsp;&nbsp;
    <input type=submit value="<?php echo $l_login_change; ?>">
</form>

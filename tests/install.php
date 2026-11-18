<?php
/** Verify the same archive installer used by DokuWiki's Extension Manager. */
$root = realpath($argv[1] ?? '');
$archive = realpath($argv[2] ?? '');
if (!$root || !is_file($root . '/inc/init.php') || !$archive) {
    fwrite(STDERR, "Usage: php tests/install.php /path/to/test-dokuwiki /path/to/plugin.zip\n");
    exit(2);
}
define('DOKU_INC', str_replace('\\', '/', $root) . '/');
define('NOSESSION', 1);
require DOKU_INC . 'inc/init.php';
set_exception_handler(function ($error) {
    fwrite(STDERR, $error->getMessage() . "\n");
    exit(1);
});

$checks = 0;
function fsArchiveCheck($condition, $message)
{
    global $checks;
    $checks++;
    if (!$condition) throw new RuntimeException($message);
}

$target = DOKU_PLUGIN . 'foldedsection';
fsArchiveCheck(!file_exists($target), 'Use a disposable test wiki without foldedsection installed.');

$installer = new \dokuwiki\plugin\extension\Installer();
$installer->installFromArchive($archive);
fsArchiveCheck(($installer->getProcessed()['foldedsection'] ?? '') === 'installed', 'Installer must identify and install foldedsection.');

foreach (['plugin.info.txt', 'helper.php', 'syntax/section.php', 'syntax/label.php', 'script.js', 'style.css', 'LICENSE'] as $path) {
    fsArchiveCheck(is_file($target . '/' . $path), 'Missing installed file: ' . $path);
}
foreach (['tests', 'docs', '.github', '.git'] as $path) {
    fsArchiveCheck(!file_exists($target . '/' . $path), 'Development content must not be installed: ' . $path);
}

$expected = confToHash(__DIR__ . '/../plugin.info.txt', true);
$extension = \dokuwiki\plugin\extension\Extension::createFromDirectory($target);
fsArchiveCheck($extension->getBase() === 'foldedsection', 'plugin.info.txt must determine the installation directory.');
fsArchiveCheck($extension->getInstalledVersion() === $expected['date'], 'Extension Manager must read the release date.');
fsArchiveCheck($extension->getMinimumPHPVersion() === '8.2', 'Extension Manager must read the PHP minimum.');
fsArchiveCheck($extension->getAuthor() === 'Leonard Heyman', 'Extension Manager must read the author.');

$updater = new \dokuwiki\plugin\extension\Installer(true);
$updater->installFromArchive($archive);
fsArchiveCheck(($updater->getProcessed()['foldedsection'] ?? '') === 'updated', 'Native archive updates must work.');

echo "Passed $checks native Extension Manager archive checks.\n";

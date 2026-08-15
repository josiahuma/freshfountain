<?php

/**
 * Fresh Fountain Transport module route installer.
 * Run once from the Laravel project root:
 *   php install-transport.php
 */

$webRoutes = __DIR__ . '/routes/web.php';

if (! is_file($webRoutes)) {
    fwrite(STDERR, "routes/web.php was not found. Run this installer from the Laravel project root.\n");
    exit(1);
}

$contents = file_get_contents($webRoutes);
$requireLine = "require __DIR__.'/transport.php';";

if (str_contains($contents, $requireLine)) {
    echo "Transport routes are already installed.\n";
    exit(0);
}

$backup = $webRoutes . '.before-transport';
copy($webRoutes, $backup);

$attendanceLine = "require __DIR__.'/attendance.php';";

if (str_contains($contents, $attendanceLine)) {
    $contents = str_replace(
        $attendanceLine,
        $requireLine . PHP_EOL . PHP_EOL . $attendanceLine,
        $contents,
        $count
    );
} else {
    $marker = 'Catch-all CMS pages';
    $position = strpos($contents, $marker);

    if ($position === false) {
        fwrite(STDERR, "Could not safely find the attendance route include or CMS catch-all marker.\n");
        fwrite(STDERR, "Add this line manually before the catch-all CMS route:\n{$requireLine}\n");
        exit(1);
    }

    $commentStart = strrpos(substr($contents, 0, $position), '/*');
    if ($commentStart === false) {
        fwrite(STDERR, "Could not safely determine where to insert the transport routes.\n");
        exit(1);
    }

    $contents = substr($contents, 0, $commentStart)
        . $requireLine . PHP_EOL . PHP_EOL
        . substr($contents, $commentStart);
}

file_put_contents($webRoutes, $contents);

echo "Transport routes installed successfully.\n";
echo "Backup created: routes/web.php.before-transport\n";

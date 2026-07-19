<?php

declare(strict_types=1);

$root = __DIR__;
$webRoutes = $root.'/routes/web.php';
$attendanceRequire = "require __DIR__.'/attendance.php';";

if (! is_file($webRoutes)) {
    fwrite(STDERR, "routes/web.php was not found. Run this installer from the Laravel project root.\n");
    exit(1);
}

$content = file_get_contents($webRoutes);
if ($content === false) {
    fwrite(STDERR, "Unable to read routes/web.php.\n");
    exit(1);
}

if (! str_contains($content, $attendanceRequire)) {
    $backup = $webRoutes.'.before-attendance';
    copy($webRoutes, $backup);
    file_put_contents($webRoutes, rtrim($content).PHP_EOL.PHP_EOL.$attendanceRequire.PHP_EOL);
    echo "Attendance routes added. Backup created at routes/web.php.before-attendance\n";
} else {
    echo "Attendance routes are already registered.\n";
}

echo "Installer complete. Run composer dump-autoload, migrate, and permissions:sync-backend.\n";

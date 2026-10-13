<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

$file=__DIR__.'/database/database.sql';
if(!is_file($file)){
    fwrite(STDERR,"Canonical database.sql is missing.\n");
    exit(1);
}

echo "NetVera now uses one canonical SQL file:\n";
echo $file."\n";
echo "Import database/database.sql into an EMPTY MySQL/MariaDB database via phpMyAdmin or mysql CLI.\n";
echo "Incremental runtime installers read their table definitions from the same file.\n";

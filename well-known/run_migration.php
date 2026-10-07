<?php
require 'bootstrap.php';

try {
    $db = App\Core\Database::getInstance();
    $sql = file_get_contents('database/migration_advanced_blog.sql');
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if ($statement !== '') {
            $db->query($statement);
        }
    }

    echo 'Migration successful.';
} catch (Exception $e) {
    echo 'Migration failed: ' . $e->getMessage();
}

<?php
require 'bootstrap.php';
try {
    \ = App\Core\Database::getInstance();
    \ = file_get_contents('database/migration_advanced_blog.sql');
    \ = array_filter(array_map('trim', explode(';', \)));
    foreach (\ as \) {
        if (!empty(\)) {
            \->query(\);
        }
    }
    echo 'Migration successful.';
} catch (Exception \) {
    echo 'Migration failed: ' . \->getMessage();
}

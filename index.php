<?php
// cPanel root shim — forwards all requests to Laravel's public/ directory
// This file should not normally be reached when .htaccess mod_rewrite is active

chdir(__DIR__ . '/public');
require __DIR__ . '/public/index.php';

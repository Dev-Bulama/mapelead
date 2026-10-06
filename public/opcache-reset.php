<?php
// Temporary OPcache reset — delete this file after use
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo 'OPcache cleared. Delete this file now.';
} else {
    echo 'OPcache not active or not available.';
}

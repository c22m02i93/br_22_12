<?php
$dir = __DIR__ . '/cache_mitropolia';
foreach (glob("$dir/*.xml") as $file) {
    unlink($file);
}
echo "Cache cleared.\n";

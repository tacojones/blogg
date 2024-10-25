<?php
function parseYAML($string) {
    $lines = explode("\n", $string);
    $yaml = [];
    foreach ($lines as $line) {
        if (trim($line) === '') continue;
        $parts = explode(':', $line, 2);
        if (count($parts) == 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            $yaml[$key] = $value;
        }
    }
    return $yaml;
}


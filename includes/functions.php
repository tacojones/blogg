<?php
function autolink($text) {
    $text = preg_replace('/(https?:\/\/[^\s]+)/', '<a href="$1" target="_blank">$1</a>', $text);
    return $text;
}
?>

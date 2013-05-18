<?php

function w3_get_notification_box($message) {
    return sprintf('<div class="updated fade">%s</div>', $message);
}

function w3_e_notification_box($message) {
    echo w3_get_notification_box($message);
}

function w3_get_error_box($message) {
    return sprintf('<div class="error">%s</div>', $message);
}

function w3_e_error_box($message) {
    echo w3_get_error_box($message);
}

function w3_format_bytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    // Uncomment one of the following alternatives
    $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ' ' . $units[$pow];
}
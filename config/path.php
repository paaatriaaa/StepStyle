<?php
// Path configuration for clean URLs
define('BASE_URL', 'http://localhost/stepstyle');

function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset($path) {
    return url('assets/' . ltrim($path, '/'));
}

function redirect($path) {
    header('Location: ' . url($path));
    exit();
}

// Auto-include this file in all pages
?>
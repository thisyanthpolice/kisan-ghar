<?php
session_start();

// Handle language switching from URL parameter
if (isset($_GET['lang'])) {
    setLanguage($_GET['lang']);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

function translate($key) {
    global $lang;
    return isset($lang[$key]) ? $lang[$key] : $key;
}

function setLanguage($lang_code) {
    $allowed_languages = ['en', 'ta', 'te', 'ml', 'kn'];
    if (in_array($lang_code, $allowed_languages)) {
        $_SESSION['lang'] = $lang_code;
    }
}

function getLanguage() {
    $default_lang = 'en';
    $allowed_languages = ['en', 'ta', 'te', 'ml', 'kn'];
    
    // First check URL parameter
    if (isset($_GET['lang']) && in_array($_GET['lang'], $allowed_languages)) {
        return $_GET['lang'];
    }
    
    // Then check session
    if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $allowed_languages)) {
        return $_SESSION['lang'];
    }
    
    return $default_lang;
}
?>
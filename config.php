<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ulinkaciamis');
define('APP_NAME', 'UlinKa Ciamis');

define('MAP_CENTER_LAT', -7.3276);
define('MAP_CENTER_LNG', 108.3625);
define('MAP_ZOOM', 12);

function getDB()
{
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conn->set_charset('utf8mb4');
        if ($conn->connect_error) {
            exit(json_encode(['error' => 'Koneksi database gagal']));
        }
    }

    return $conn;
}

function jsonResponse($data, $code = 200)
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn()
{
    return isset($_SESSION['admin_id']);
}

function requireLogin()
{
    if (! isLoggedIn()) {
        header('Location: /ulinkaciamis/admin/login.php');
        exit;
    }
}

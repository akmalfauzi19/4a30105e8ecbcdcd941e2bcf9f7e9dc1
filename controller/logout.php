<?php
include_once './config/database.php';
require "./vendor/autoload.php";
// require __DIR__ . './vendor/autoload.php';
require_once('./cors.php');

use Firebase\JWT\JWT;
use Dotenv\Dotenv;
use \Firebase\JWT\Key;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
$dotenv->load();

// Atur jenis response
header('Content-Type: application/json');

// Cek method request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit();
}

$headers = getallheaders();

// Periksa apakah header authorization-nya ada
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(array(
        "status" => 401,
        "message" => "User harus login"
    ));
    exit();
}

list(, $token) = explode(' ', $headers['Authorization']);

try {

    // cek cookie
    if (!isset($_COOKIE['refreshToken'])) {
        http_response_code(401);
        echo json_encode(
            array(
                "status" => 401,
                "message" => "harus login terlebih dahulu"
            )
        );
        exit();
    }

    $user = JWT::decode($token, new Key($_ENV['ACCESS_TOKEN_SECRET'], 'HS256'));
    $credential = $_COOKIE['refreshToken'];
    if ($credential !== $token) {
        http_response_code(401);
        echo json_encode(
            array(
                "status" => 401,
                "message" => "token tidak valid"
            )
        );
        exit();
    }
    // delete cookie
    setcookie('refreshToken', $token, time() - 3600, '', '', false, true);

    echo json_encode(array(
        "status" => 200,
        "message" => $user->email . " " . "Berhasil Logout"
    ));
} catch (Exception $e) {
    // Bagian ini akan jalan jika terdapat error saat JWT diverifikasi atau di-decode
    http_response_code(401);
    echo json_encode(array(
        "status" => 401,
        "message" => $e->getMessage()
    ));
    exit();
}

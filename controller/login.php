<?php
include_once './config/database.php';
require "./vendor/autoload.php";
// require __DIR__ . './vendor/autoload.php';
require_once('./cors.php');

use \Firebase\JWT\JWT;
use \Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
$dotenv->load();

// Atur jenis response
header('Content-Type: application/json');

// Cek method request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit();
}

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

// Ambil data json yang dikirim user
$input = json_decode(file_get_contents("php://input"));

if (!$input) {
    http_response_code(400);
    echo json_encode(array(
        "status" => 400,
        "message" => "Tidak ada data yang dikirim"
    ));
    exit();
}

$email = $input->email;
$password = $input->password;

// Jika tidak ada data email atau password
if (!isset($email) || !isset($password)) {
    http_response_code(400);
    echo json_encode(array(
        "status" => 400,
        "message" => "Email atau password kosong"
    ));
    exit();
}
// echo json_encode($input);

// Atur jenis response
header('Content-Type: application/json');


$table_name = 'Users';
$query = "SELECT id, first_name, last_name, password FROM " . $table_name . " WHERE email = ? LIMIT 0,1";

$stmt = $conn->prepare($query);
$stmt->bindParam(1, $email);
$stmt->execute();
$num = $stmt->rowCount();

if (!$num > 0) {
    http_response_code(401);
    echo json_encode(array("message" => "email tidak terdaftar"));
    exit();
}

// get data user dari database
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$id = $row['id'];
$firstname_db = $row['first_name'];
$lastname_db = $row['last_name'];
$password_db = $row['password'];

// echo json_encode($row);

// Jika email atau password tidak sesuai
if (!password_verify($password, $password_db)) {
    http_response_code(401);
    echo json_encode([
        'status' => 401,
        'data' => null,
        'message' => 'Password tidak sesuai'
    ]);
    exit();
}

// Menghitung waktu kadaluarsa token. Dalam kasus ini akan terjadi setelah 60 menit
$expired_time = time() + (60 * 60);

// Buat payload dan access token
$payload = [
    'email' => $email,
    "firstname" => $firstname_db,
    "lastname" => $lastname_db,
    // Di library ini wajib menambah key exp untuk mengatur masa berlaku token
    'exp' => $expired_time
];

// Men-generate access token
$access_token = JWT::encode($payload, $_ENV['ACCESS_TOKEN_SECRET'], 'HS256');
// echo json_encode($access_token);

// Simpan refresh token di http-only cookie
setcookie('refreshToken', $access_token, $expired_time, '', '', false, true);

// Kirim kembali ke user
echo json_encode([
    'success' => 200,
    'data' => [
        'accessToken' => $access_token,
        'expiry' => date(DATE_ISO8601, $expired_time)
    ],
    'message' => 'Login berhasil!'
]);

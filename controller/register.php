<?php
include_once './config/database.php';
require_once('./cors.php');


$firstName = '';
$lastName = '';
$email = '';
$password = '';
$conn = null;

$inputbaseService = new DatabaseService();
$conn = $inputbaseService->getConnection();



// Cek method request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array(
        "status" => 405,
        "message" => "Methode harus POST"
    ));
    exit();
}

$input = json_decode(file_get_contents("php://input"));

// cek json jik null
if (!$input) {
    http_response_code(400);
    echo json_encode(array(
        "status" => 400,
        "message" => "Tidak ada data yang dikirim"
    ));
    exit();
}

$query_duplicate_email = "SELECT * FROM Users WHERE email = '$input->email'";
$dbcek = $conn->prepare($query_duplicate_email);
$dbcek->execute();
$num = $dbcek->rowCount();
if ($num > 0) {
    http_response_code(409);
    echo json_encode(array(
        "status" => 409,
        "message" => "email sudah terdaftar"
    ));
    exit();
} else {
    if (!empty($input->first_name) && !empty($input->last_name) && !empty($input->email) && !empty($input->password)) {

        $firstName = $input->first_name;
        $lastName = $input->last_name;
        $email = $input->email;
        $password = $input->password;

        $table_user = 'Users';
        $query = "INSERT INTO " . $table_user . "
                SET first_name = :firstname,
                last_name = :lastname,
                email = :email,
                password = :password";
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':firstname', $firstName);
        $stmt->bindParam(':lastname', $lastName);
        $stmt->bindParam(':email', $email);
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(':password', $password_hash);
        if ($stmt->execute()) {

            http_response_code(200);
            echo json_encode(array(
                "status" => 200,
                "message" => "User berhasil di daftarkan.",
                "data" => [
                    "name" => $firstName . " " . $lastName,
                    "email" => $email
                ]
            ));
        } else {
            http_response_code(401);
            echo json_encode(array(
                "status" => 401,
                "message" => "Tidak dapat mendaftarkan user."
            ));
            exit();
        }
    } else {
        http_response_code(428);
        echo json_encode(array(
            "status" => 428,
            "message" => "Semua data user harus di isi."
        ));
        exit();
    }
}

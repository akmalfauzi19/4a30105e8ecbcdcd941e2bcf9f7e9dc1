<?php
include_once './config/database.php';
require "./vendor/autoload.php";
require_once('./cors.php');

use Firebase\JWT\JWT;
use Dotenv\Dotenv;
use \Firebase\JWT\Key;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
$dotenv->load();

// set database
$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();
$table_name = 'Employee_email';
$query = "SELECT * FROM " . $table_name;

$stmt = $conn->prepare($query);
$stmt->execute();
$data_employee = $stmt->fetchAll(PDO::FETCH_ASSOC);
// echo json_encode($data_employee[0]['email']);


// Atur jenis response
header('Content-Type: application/json');

// Cek method request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(array(
    "status" => 405,
    "message" => "Methode harus post"
  ));
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

// Mengambil token
list(, $token) = explode(' ', $headers['Authorization']);
// $data = JWT::decode($token, new Key($_ENV['ACCESS_TOKEN_SECRET'], 'HS256'));

// echo json_encode($data);

$input = json_decode(file_get_contents("php://input"));

if (!$input) {
  http_response_code(400);
  echo json_encode(array(
    "status" => 400,
    "message" => "Tidak ada data yang dikirim"
  ));
  exit();
}

$subject = $input->subject;
$message = $input->message;

try {

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

  // Men-decode token. Dalam library ini juga sudah sekaligus memverfikasinya
  $data = JWT::decode($token, new Key($_ENV['ACCESS_TOKEN_SECRET'], 'HS256'));
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

  $email_from = $data->email;
  $email_from_name = $data->firstname . " " . $data->lastname;

  // echo json_encode($data);
  // echo json_encode(array($_COOKIE['refreshToken'], $token));

  // phpmailer setting
  $mail = new PHPMailer(true);

  $mail->SMTPDebug = 3;

  $mail->isSMTP();
  $mail->Host = $_ENV['MAIL_HOST'];
  $mail->SMTPAuth = true;
  $mail->Port = $_ENV['MAIL_PORT'];
  $mail->SMTPSecure =  $_ENV['MAIL_ENCRYPTION'];
  $mail->Username = $_ENV['MAIL_USERNAME'];
  $mail->Password = $_ENV['MAIL_PASSWORD'];

  $mail->From = $email_from;
  $mail->FromName = $email_from_name;

  if (!$subject && !$message) {
    http_response_code(400);
    echo json_encode(
      array(
        "status" => 400,
        "message" => "Field subject dan message harus diisi"
      )
    );
    exit();
  } else {

    $table_mail = 'Mail ';
    $query_mail = "INSERT INTO " . $table_mail . "
                SET email_from = :email_from,
                email_from_name = :email_from_name,
                email_recipient = :email_recipient,
                email_recipient_name = :email_recipient_name,
                message = :message";
    $stmt_mail = $conn->prepare($query_mail);

    foreach ($data_employee as $employee) {
      // save to db
      $stmt_mail->bindParam(':email_from', $email_from);
      $stmt_mail->bindParam(':email_from_name', $email_from_name);
      $stmt_mail->bindParam(':email_recipient', $employee['email']);
      $stmt_mail->bindParam(':email_recipient_name', $employee['name']);
      $stmt_mail->bindParam(':message', $message);
      $stmt_mail->execute();

      // send message to email
      $mail->addAddress($employee['email'], $employee['name']);
      $mail->isHTML(true);

      $mail->Subject = $subject;
      $mail->Body = "<p>" . $message . "</p>";
      $mail->AltBody = "This is the plain text version of the email content";
      $mail->send();
    }
    try {
      // $mail->send();
      echo json_encode(array(
        "status" => 200,
        "message" => "kirim email berhasil"
      ));
    } catch (Exception $e) {
      http_response_code(401);
      echo json_encode(array(
        "status" => 401,
        "message" => $mail->ErrorInfo
      ));
    }
  }
} catch (Exception $e) {
  // Bagian ini akan jalan jika terdapat error saat JWT diverifikasi atau di-decode
  http_response_code(401);
  echo json_encode(array(
    "status" => 401,
    "message" => $e->getMessage()
  ));
  exit();
}

<?php

$request = $_SERVER['REQUEST_URI'];

// routes
switch ($request) {
    case '/':
        require __DIR__ . '/index.php';
        break;
    case '/login':
        require __DIR__ . '/controller/login.php';
        break;
    case '/register':
        require __DIR__ . '/controller/register.php';
        break;
    case '/logout':
        require __DIR__ . '/controller/logout.php';
        break;
    case '/mail':
        require __DIR__ . '/controller/mail.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(array(
            "status" => 404,
            "message" => "function not found"
        ));
        break;
}

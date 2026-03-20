<?php
require_once __DIR__ . '/inc/data.php';

// 简单的留言存储逻辑，存到 data/messages.json
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $subject === '') {
        header('Location: index.php?sent=0');
        exit;
    }

    $messages = loadMessages();

    $messages[] = [
        'id' => nextId($messages),
        'time' => time(),
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'subject' => $subject,
        'message' => $message,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    ];

    saveMessages($messages);

    header('Location: index.php?sent=1');
    exit;
}

header('Location: index.php');
exit;

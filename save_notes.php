<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $content = $_POST['content'];
    $username = $_POST['username'];
    $date = date('Y-m-d H:i:s');

    $data = [
        'content' => $content,
        'username' => $username,
        'date' => $date
    ];

    $file = $type === 'emba' ? 'emba_notes.json' : 'ib_notes.json';
    $result = file_put_contents($file, json_encode($data));

    header('Content-Type: application/json');
    echo json_encode(['success' => $result !== false]);
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo 'Method Not Allowed';
}

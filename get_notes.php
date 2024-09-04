<?php
function getNotes($file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        return $data ? $data : ['content' => '', 'username' => 'N/A', 'date' => 'N/A'];
    }
    return ['content' => '', 'username' => 'N/A', 'date' => 'N/A'];
}

$emba = getNotes('emba_notes.json');
$ib = getNotes('ib_notes.json');

header('Content-Type: application/json');
echo json_encode(['emba' => $emba, 'ib' => $ib]);

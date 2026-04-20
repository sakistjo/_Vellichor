<?php
header('Content-Type: application/json');

function readJsonFile($path) {
    if (!file_exists($path)) {
        return [];
    }

    $content = file_get_contents($path);
    $data = json_decode($content, true);

    return is_array($data) ? $data : [];
}

$libraryId = trim($_POST['libraryId'] ?? '');
$loanId = (int) ($_POST['loanId'] ?? 0);

if ($libraryId === '' || $loanId <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);
    exit;
}

$users = readJsonFile("../data/users.json");
$loans = readJsonFile("../data/loans.json");
$fineNotificationsPath = "../data/fine-notifications.json";
$fineNotifications = readJsonFile($fineNotificationsPath);

$member = null;
foreach ($users as $user) {
    if (($user['libraryId'] ?? '') === $libraryId) {
        $member = $user;
        break;
    }
}

if (!$member) {
    echo json_encode([
        "success" => false,
        "message" => "Member not found."
    ]);
    exit;
}

$memberId = (int) ($member['id'] ?? 0);
$loanFound = false;

foreach ($loans as $loan) {
    if ((int) ($loan['id'] ?? 0) !== $loanId) {
        continue;
    }

    if ((int) ($loan['userId'] ?? 0) !== $memberId) {
        echo json_encode([
            "success" => false,
            "message" => "Loan does not belong to you."
        ]);
        exit;
    }

    if (!empty($loan['returned'])) {
        echo json_encode([
            "success" => false,
            "message" => "Book is already returned. No fine due."
        ]);
        exit;
    }

    $loanFound = true;
    break;
}

if (!$loanFound) {
    echo json_encode([
        "success" => false,
        "message" => "Loan not found."
    ]);
    exit;
}

$updatedNotifications = [];
$removed = false;
foreach ($fineNotifications as $notification) {
    if ((int) ($notification['loanId'] ?? 0) === $loanId) {
        $removed = true;
        continue;
    }
    $updatedNotifications[] = $notification;
}

if (!$removed) {
    echo json_encode([
        "success" => false,
        "message" => "No active fine notification found for this loan."
    ]);
    exit;
}

$saved = file_put_contents(
    $fineNotificationsPath,
    json_encode(array_values($updatedNotifications), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

if ($saved === false) {
    echo json_encode([
        "success" => false,
        "message" => "Could not complete payment."
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Fine payment recorded successfully."
]);
?>

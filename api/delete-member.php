<?php
header('Content-Type: application/json');

function readJsonFile($path) {
    if (!file_exists($path)) {
        return [];
    }

    $data = json_decode(file_get_contents($path), true);
    return is_array($data) ? $data : [];
}

function writeJsonFile($path, $data) {
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false;
}

$input = json_decode(file_get_contents('php://input'), true);
$userId = isset($input['userId']) ? (int)$input['userId'] : 0;

if ($userId <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid member id."]);
    exit;
}

$usersPath = "../data/users.json";
$loansPath = "../data/loans.json";
$ratingsPath = "../data/ratings.json";
$favoritesPath = "../data/favorites.json";
$waitlistPath = "../data/waitlist.json";
$finesPath = "../data/fine-notifications.json";

$users = readJsonFile($usersPath);
$loans = readJsonFile($loansPath);

$member = null;
foreach ($users as $user) {
    if ((int)($user['id'] ?? 0) === $userId && ($user['role'] ?? '') === 'member') {
        $member = $user;
        break;
    }
}

if ($member === null) {
    echo json_encode(["success" => false, "message" => "Member not found."]);
    exit;
}

$users = array_values(array_filter($users, function ($user) use ($userId) {
    return (int)($user['id'] ?? 0) !== $userId;
}));

$loans = array_values(array_filter($loans, function ($loan) use ($userId) {
    return (int)($loan['userId'] ?? 0) !== $userId;
}));

$ratings = array_values(array_filter(readJsonFile($ratingsPath), function ($item) use ($userId) {
    return (int)($item['userId'] ?? 0) !== $userId;
}));

$favorites = array_values(array_filter(readJsonFile($favoritesPath), function ($item) use ($userId) {
    return (int)($item['userId'] ?? 0) !== $userId;
}));

$waitlist = array_values(array_filter(readJsonFile($waitlistPath), function ($item) use ($userId) {
    return (int)($item['userId'] ?? 0) !== $userId;
}));

$fines = array_values(array_filter(readJsonFile($finesPath), function ($item) use ($userId) {
    return (int)($item['userId'] ?? 0) !== $userId;
}));

$ok = true;
$ok = $ok && writeJsonFile($usersPath, $users);
$ok = $ok && writeJsonFile($loansPath, $loans);
$ok = $ok && writeJsonFile($ratingsPath, $ratings);
$ok = $ok && writeJsonFile($favoritesPath, $favorites);
$ok = $ok && writeJsonFile($waitlistPath, $waitlist);
$ok = $ok && writeJsonFile($finesPath, $fines);

if (!$ok) {
    echo json_encode(["success" => false, "message" => "Failed to delete member."]);
    exit;
}

echo json_encode(["success" => true, "message" => "Member deleted successfully."]);
?>

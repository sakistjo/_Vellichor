<?php
header('Content-Type: application/json');

$usersPath = "../data/users.json";
$booksPath = "../data/books.json";
$waitlistPath = "../data/waitlist.json";

$libraryId = trim($_POST['libraryId'] ?? '');
$bookId = (int) ($_POST['bookId'] ?? 0);

if ($libraryId === '' || $bookId <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);
    exit;
}

$users = file_exists($usersPath) ? json_decode(file_get_contents($usersPath), true) : [];
$books = file_exists($booksPath) ? json_decode(file_get_contents($booksPath), true) : [];
$waitlist = file_exists($waitlistPath) ? json_decode(file_get_contents($waitlistPath), true) : [];

if (!is_array($users) || !is_array($books)) {
    echo json_encode([
        "success" => false,
        "message" => "Could not load data."
    ]);
    exit;
}

if (!is_array($waitlist)) {
    $waitlist = [];
}

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

$targetBook = null;
foreach ($books as $book) {
    if (($book['id'] ?? 0) == $bookId) {
        $targetBook = $book;
        break;
    }
}

if (!$targetBook) {
    echo json_encode([
        "success" => false,
        "message" => "Book not found."
    ]);
    exit;
}

if ($targetBook['available'] ?? false) {
    echo json_encode([
        "success" => false,
        "message" => "This book is available, so you can borrow it."
    ]);
    exit;
}

foreach ($waitlist as $entry) {
    if (($entry['bookId'] ?? 0) == $bookId && ($entry['userId'] ?? 0) == ($member['id'] ?? 0)) {
        echo json_encode([
            "success" => false,
            "message" => "You are already on the waiting list for this book."
        ]);
        exit;
    }
}

$nextWaitlistId = 1;
foreach ($waitlist as $entry) {
    if (($entry['id'] ?? 0) >= $nextWaitlistId) {
        $nextWaitlistId = $entry['id'] + 1;
    }
}

$waitlist[] = [
    "id" => $nextWaitlistId,
    "bookId" => $bookId,
    "userId" => $member['id']
];

file_put_contents($waitlistPath, json_encode($waitlist, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode([
    "success" => true,
    "message" => "Book added to waiting list."
]);
?>

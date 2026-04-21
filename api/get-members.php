<?php
header('Content-Type: application/json');

$usersPath = "../data/users.json";
$booksPath = "../data/books.json";
$loansPath = "../data/loans.json";

$users = file_exists($usersPath) ? json_decode(file_get_contents($usersPath), true) : [];
$books = file_exists($booksPath) ? json_decode(file_get_contents($booksPath), true) : [];
$loans = file_exists($loansPath) ? json_decode(file_get_contents($loansPath), true) : [];

if (!is_array($users)) {
    $users = [];
}

if (!is_array($books)) {
    $books = [];
}

if (!is_array($loans)) {
    $loans = [];
}

$bookMap = [];

foreach ($books as $book) {
    $bookMap[$book['id']] = $book['name'] ?? 'Unknown Book';
}

$members = [];

foreach ($users as $user) {
    if (($user['role'] ?? '') !== 'member') {
        continue;
    }

    $returnedBooks = [];
    $notReturnedBooks = [];

    foreach ($loans as $loan) {
        if (($loan['userId'] ?? 0) != ($user['id'] ?? 0)) {
            continue;
        }

        $bookTitle = $bookMap[$loan['bookId']] ?? 'Unknown Book';

        if (!empty($loan['returned'])) {
            $returnedBooks[] = $bookTitle;
        } else {
            $notReturnedBooks[] = $bookTitle;
        }
    }

    $members[] = [
        "id" => $user['id'],
        "libraryId" => $user['libraryId'] ?? '',
        "name" => $user['name'] ?? '',
        "surname" => $user['surname'] ?? '',
        "email" => $user['email'] ?? '',
        "username" => $user['username'] ?? '',
        "booksRead" => $returnedBooks,
        "booksReturned" => $returnedBooks,
        "booksNotReturned" => $notReturnedBooks,
        "isPassive" => count($notReturnedBooks) === 0
    ];
}

echo json_encode($members);
?>

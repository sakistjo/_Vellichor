<?php
header('Content-Type: application/json');

date_default_timezone_set('Europe/Tirane');
$feePerDay = 0.5;

function readJsonFile($path) {
    if (!file_exists($path)) {
        return [];
    }

    $content = file_get_contents($path);
    $data = json_decode($content, true);

    return is_array($data) ? $data : [];
}

$users = readJsonFile("../data/users.json");
$books = readJsonFile("../data/books.json");
$loans = readJsonFile("../data/loans.json");
$notifications = readJsonFile("../data/fine-notifications.json");

$userMap = [];
foreach ($users as $user) {
    $userMap[$user['id']] = [
        "name" => trim(($user['name'] ?? '') . ' ' . ($user['surname'] ?? '')),
        "libraryId" => $user['libraryId'] ?? ''
    ];
}

$bookMap = [];
foreach ($books as $book) {
    $bookMap[$book['id']] = $book['name'] ?? 'Unknown Book';
}

$notificationMap = [];
foreach ($notifications as $notification) {
    $notificationMap[$notification['loanId']] = $notification;
}

$today = new DateTime(date('Y-m-d'));
$activeFines = [];

foreach ($loans as $loan) {
    if (!empty($loan['returned'])) {
        continue;
    }

    $dueDateString = trim((string) ($loan['dueDate'] ?? ''));
    if ($dueDateString === '') {
        $borrowDateString = trim((string) ($loan['borrowDate'] ?? ''));
        if ($borrowDateString !== '') {
            $borrowDate = DateTime::createFromFormat('Y-m-d', $borrowDateString);
            if ($borrowDate) {
                $borrowDate->modify('+14 days');
                $dueDateString = $borrowDate->format('Y-m-d');
            }
        }
    }

    $dueDate = DateTime::createFromFormat('Y-m-d', $dueDateString);
    if (!$dueDate) {
        continue;
    }

    if ($today <= $dueDate) {
        continue;
    }

    $daysLate = (int) $dueDate->diff($today)->format('%a');
    $amount = number_format($daysLate * $feePerDay, 2, '.', '');

    $userId = $loan['userId'] ?? 0;
    $bookId = $loan['bookId'] ?? 0;
    $loanId = $loan['id'] ?? 0;
    $notification = $notificationMap[$loanId] ?? null;

    $activeFines[] = [
        "loanId" => $loanId,
        "memberName" => $userMap[$userId]['name'] ?? 'Unknown Member',
        "libraryId" => $userMap[$userId]['libraryId'] ?? '',
        "bookName" => $bookMap[$bookId] ?? 'Unknown Book',
        "dueDate" => $dueDateString,
        "daysLate" => $daysLate,
        "feePerDay" => number_format($feePerDay, 2, '.', ''),
        "amount" => $amount,
        "notified" => !empty($notification['notified'])
    ];
}

echo json_encode([
    "activeFines" => $activeFines
]);
?>

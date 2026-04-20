<?php
header('Content-Type: application/json');
date_default_timezone_set('Europe/Tirane');

function readJsonFile($path) {
    if (!file_exists($path)) {
        return [];
    }

    $content = file_get_contents($path);
    $data = json_decode($content, true);

    return is_array($data) ? $data : [];
}

$notificationsPath = "../data/fine-notifications.json";
$loansPath = "../data/loans.json";
$notifications = readJsonFile($notificationsPath);
$loans = readJsonFile($loansPath);

$loanId = (int) ($_POST['loanId'] ?? 0);
$feePerDay = 0.5;

if ($loanId === 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid loan."
    ]);
    exit;
}

$targetLoan = null;
foreach ($loans as $loan) {
    if ((int) ($loan['id'] ?? 0) === $loanId) {
        $targetLoan = $loan;
        break;
    }
}

if (!$targetLoan) {
    echo json_encode([
        "success" => false,
        "message" => "Loan not found."
    ]);
    exit;
}

if (!empty($targetLoan['returned'])) {
    echo json_encode([
        "success" => false,
        "message" => "Book already returned. No fine notification needed."
    ]);
    exit;
}

$today = new DateTime(date('Y-m-d'));
$dueDateString = trim((string) ($targetLoan['dueDate'] ?? ''));

if ($dueDateString === '') {
    $borrowDateString = trim((string) ($targetLoan['borrowDate'] ?? ''));
    if ($borrowDateString !== '') {
        $borrowDate = DateTime::createFromFormat('Y-m-d', $borrowDateString);
        if ($borrowDate) {
            $borrowDate->modify('+14 days');
            $dueDateString = $borrowDate->format('Y-m-d');
        }
    }
}

$dueDate = DateTime::createFromFormat('Y-m-d', $dueDateString);
if (!$dueDate || $today <= $dueDate) {
    echo json_encode([
        "success" => false,
        "message" => "This loan is not overdue yet."
    ]);
    exit;
}

$daysLate = (int) $dueDate->diff($today)->format('%a');
$currentAmount = number_format($daysLate * $feePerDay, 2, '.', '');
$found = false;

foreach ($notifications as $index => $notification) {
    if ((int) ($notification['loanId'] ?? 0) === $loanId) {
        $notifications[$index]['notified'] = true;
        $notifications[$index]['notificationDate'] = date('Y-m-d');
        $notifications[$index]['userId'] = (int) ($targetLoan['userId'] ?? 0);
        $notifications[$index]['bookId'] = (int) ($targetLoan['bookId'] ?? 0);
        $notifications[$index]['feePerDay'] = number_format($feePerDay, 2, '.', '');
        $notifications[$index]['daysLateAtNotification'] = $daysLate;
        $notifications[$index]['amountAtNotification'] = $currentAmount;
        $found = true;
        break;
    }
}

if (!$found) {
    $notifications[] = [
        "loanId" => $loanId,
        "userId" => (int) ($targetLoan['userId'] ?? 0),
        "bookId" => (int) ($targetLoan['bookId'] ?? 0),
        "notified" => true,
        "notificationDate" => date('Y-m-d'),
        "feePerDay" => number_format($feePerDay, 2, '.', ''),
        "daysLateAtNotification" => $daysLate,
        "amountAtNotification" => $currentAmount
    ];
}

$saved = file_put_contents(
    $notificationsPath,
    json_encode($notifications, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

if ($saved === false) {
    echo json_encode([
        "success" => false,
        "message" => "Could not send notification."
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Notification sent successfully."
]);
exit;
?>

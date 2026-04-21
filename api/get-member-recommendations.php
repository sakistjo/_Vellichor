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

$libraryId = trim($_GET['libraryId'] ?? '');

if ($libraryId === '') {
    echo json_encode([
        "genres" => [],
        "recommendations" => []
    ]);
    exit;
}

$users = readJsonFile("../data/users.json");
$books = readJsonFile("../data/books.json");
$loans = readJsonFile("../data/loans.json");
$waitlist = readJsonFile("../data/waitlist.json");
$favorites = readJsonFile("../data/favorites.json");

$member = null;
foreach ($users as $user) {
    if (($user['libraryId'] ?? '') === $libraryId) {
        $member = $user;
        break;
    }
}

if (!$member) {
    echo json_encode([
        "genres" => [],
        "recommendations" => []
    ]);
    exit;
}

$memberId = (int) ($member['id'] ?? 0);

$bookMap = [];
foreach ($books as $book) {
    $bookId = (int) ($book['id'] ?? 0);
    if ($bookId > 0) {
        $bookMap[$bookId] = $book;
    }
}

$genreCounts = [];
$authorCounts = [];
$borrowedGenreAuthorPairs = [];
$memberLoanedBookIds = [];
$memberActiveLoanBookIds = [];

foreach ($loans as $loan) {
    if ((int) ($loan['userId'] ?? 0) !== $memberId) {
        continue;
    }

    $bookId = (int) ($loan['bookId'] ?? 0);
    if ($bookId <= 0) {
        continue;
    }

    $memberLoanedBookIds[$bookId] = true;

    if (!($loan['returned'] ?? false)) {
        $memberActiveLoanBookIds[$bookId] = true;
    }

    $book = $bookMap[$bookId] ?? null;
    if (!$book) {
        continue;
    }

    $genre = trim((string) ($book['genre'] ?? ''));
    $author = trim((string) ($book['author'] ?? ''));

    if ($genre === '') {
        continue;
    }

    if (!isset($genreCounts[$genre])) {
        $genreCounts[$genre] = 0;
    }
    $genreCounts[$genre] += 1;

    if ($author !== '') {
        if (!isset($authorCounts[$author])) {
            $authorCounts[$author] = 0;
        }
        $authorCounts[$author] += 1;
        $borrowedGenreAuthorPairs[strtolower($genre) . "|" . strtolower($author)] = true;
    }
}

arsort($genreCounts);
$preferredGenres = array_keys($genreCounts);
arsort($authorCounts);
$preferredAuthors = array_keys($authorCounts);

$memberFavoriteBookIds = [];
foreach ($favorites as $favorite) {
    if ((int) ($favorite['userId'] ?? 0) === $memberId) {
        $memberFavoriteBookIds[(int) ($favorite['bookId'] ?? 0)] = true;
    }
}

$memberWaitlistedBookIds = [];
foreach ($waitlist as $entry) {
    if ((int) ($entry['userId'] ?? 0) === $memberId) {
        $memberWaitlistedBookIds[(int) ($entry['bookId'] ?? 0)] = true;
    }
}

$recommendations = [];
if (!empty($preferredGenres)) {
    foreach ($books as $book) {
        $bookId = (int) ($book['id'] ?? 0);
        if ($bookId <= 0) {
            continue;
        }

        if (isset($memberLoanedBookIds[$bookId])) {
            continue;
        }

        $genre = trim((string) ($book['genre'] ?? ''));
        $author = trim((string) ($book['author'] ?? ''));

        if ($genre === '' || $author === '') {
            continue;
        }

        $pairKey = strtolower($genre) . "|" . strtolower($author);
        if (!isset($borrowedGenreAuthorPairs[$pairKey])) {
            continue;
        }

        $score = (int) $genreCounts[$genre];
        $book['recommendationScore'] = $score;
        $book['recommendationReason'] = "Matches books you borrowed in " . $genre . " by " . $author;
        $book['favorited'] = isset($memberFavoriteBookIds[$bookId]);
        $book['waitlisted'] = isset($memberWaitlistedBookIds[$bookId]);
        $book['borrowedByMember'] = isset($memberActiveLoanBookIds[$bookId]);

        $recommendations[] = $book;
    }
}

usort($recommendations, function ($a, $b) {
    $scoreDiff = ((int) ($b['recommendationScore'] ?? 0)) - ((int) ($a['recommendationScore'] ?? 0));
    if ($scoreDiff !== 0) {
        return $scoreDiff;
    }

    $availabilityA = !empty($a['available']) ? 1 : 0;
    $availabilityB = !empty($b['available']) ? 1 : 0;
    $availabilityDiff = $availabilityB - $availabilityA;
    if ($availabilityDiff !== 0) {
        return $availabilityDiff;
    }

    return strcmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
});

echo json_encode([
    "genres" => $preferredGenres,
    "authors" => $preferredAuthors,
    "recommendations" => $recommendations
]);
?>

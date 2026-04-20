<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Achievements</title>
  <?php $assetVersion = "20260419a5"; ?>
  <link rel="stylesheet" href="../css/style.css?v=<?php echo $assetVersion; ?>">
</head>
<body class="member-achievements-vintage-page">
  <div class="books-page-shell">
    <div class="books-page-container member-books-vintage-layout">
      <a class="member-books-top-back" href="member-dashboard.php" aria-label="Back to Dashboard">&#8592; Back to Dashboard</a>

      <div class="books-page-header member-books-vintage-header">
        <h1>Achievements</h1>
        <p class="member-books-vintage-quote" id="memberAchievementsQuoteText">Success is the sum of small efforts, repeated day in and day out.</p>
        <p class="member-books-vintage-author" id="memberAchievementsQuoteAuthor">Robert Collier</p>
      </div>

      <div class="books-page-card">
        <div class="books-page-list-header">
          <h2>Quiz Trophies</h2>
        </div>

        <p class="success-message" id="achievementsSummary"></p>
        <div id="achievementsList"></div>

        <div class="member-achievements-extra">
          <h3>Books Read</h3>
          <p class="success-message" id="achievementsBooksSummary"></p>
          <div id="achievementsBooksList" class="books-list"></div>
        </div>
      </div>
    </div>
  </div>

  <script src="../js/quotes.js?v=<?php echo $assetVersion; ?>"></script>
  <script src="../js/member-achievements.js?v=<?php echo $assetVersion; ?>"></script>
</body>
</html>

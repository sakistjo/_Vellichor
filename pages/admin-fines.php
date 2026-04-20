<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fines & Payments</title>
  <?php $assetVersion = "20260418s8"; ?>
  <link rel="stylesheet" href="../css/style.css?v=<?php echo $assetVersion; ?>">
</head>
<body class="admin-fines-vintage-page">
  <div class="books-page-shell">
    <div class="books-page-container">
      <a class="member-books-top-back" href="admin-dashboard.php">&larr; Back to Dashboard</a>

      <div class="books-page-header">
        <h1>Fines & Payments</h1>
        <p class="member-books-vintage-quote" id="quoteText">Books are a uniquely portable magic.</p>
        <p class="member-books-vintage-author" id="quoteAuthor">Stephen King</p>
      </div>

      <div class="books-page-card">
        <div class="books-page-list-header admin-fines-head">
          <h2>Active Fines</h2>
          <input type="search" id="fineSearchInput" class="books-search" placeholder="Search member, library ID, or book...">
        </div>

        <div id="activeFinesList" class="fines-list"></div>
      </div>
    </div>
  </div>

  <script src="../js/quotes.js?v=<?php echo $assetVersion; ?>"></script>
  <script src="../js/admin-fines.js?v=<?php echo $assetVersion; ?>"></script>
</body>
</html>

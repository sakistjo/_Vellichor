<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Facts</title>
  <?php $assetVersion = "20260419q1"; ?>
  <link rel="stylesheet" href="../css/style.css?v=<?php echo $assetVersion; ?>">
</head>
<body class="member-facts-vintage-page">
  <div class="books-page-shell">
    <div class="books-page-container member-books-vintage-layout">
      <a class="member-books-top-back" href="member-dashboard.php" aria-label="Back to Dashboard">&#8592; Back to Dashboard</a>

      <div class="books-page-header member-books-vintage-header">
        <h1>Facts</h1>
        <p class="member-books-vintage-quote" id="memberFactsQuoteText">Books are a uniquely portable magic.</p>
        <p class="member-books-vintage-author" id="memberFactsQuoteAuthor">Stephen King</p>
      </div>

      <div class="books-page-card">
        <div class="books-page-list-header member-facts-split-head">
          <h2>Literary Facts</h2>
        </div>

        <div class="member-facts-split">
          <section class="member-facts-column">
            <h3>Book Facts</h3>
            <div class="member-facts-tags" data-type="book"></div>
          </section>

          <section class="member-facts-column">
            <h3>Writers Facts</h3>
            <div class="member-facts-tags" data-type="writer"></div>
          </section>
        </div>
      </div>
    </div>
  </div>

  <div id="memberFactModal" class="member-fact-modal" aria-hidden="true">
    <div class="member-fact-modal-box">
      <button type="button" id="memberFactClose" class="member-fact-close" aria-label="Close">&times;</button>
      <p id="memberFactType" class="member-fact-type"></p>
      <h2 id="memberFactTitle" class="member-fact-title"></h2>
      <p id="memberFactText" class="member-fact-text"></p>
    </div>
  </div>

  <script src="../js/quotes.js?v=<?php echo $assetVersion; ?>"></script>
  <script src="../js/member-facts.js?v=<?php echo $assetVersion; ?>"></script>
</body>
</html>

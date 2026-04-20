const achievementsSummary = document.getElementById("achievementsSummary");
const achievementsList = document.getElementById("achievementsList");
const achievementsBooksSummary = document.getElementById("achievementsBooksSummary");
const achievementsBooksList = document.getElementById("achievementsBooksList");
const memberAchievementsQuoteText = document.getElementById("memberAchievementsQuoteText");
const memberAchievementsQuoteAuthor = document.getElementById("memberAchievementsQuoteAuthor");

const TOTAL_LEVELS = 100;
const memberLibraryId = sessionStorage.getItem("memberLibraryId");

if (!memberLibraryId) {
  window.location.href = "member-login.php";
}

loadRandomQuoteInto(memberAchievementsQuoteText, memberAchievementsQuoteAuthor);

function loadQuizProgress() {
  const storageKey = "vellichorQuizProgress_" + memberLibraryId;
  const raw = localStorage.getItem(storageKey);

  if (!raw) {
    return 0;
  }

  try {
    const parsed = JSON.parse(raw);
    const highestPassed = Number(parsed.highestPassed) || 0;
    return Math.max(0, Math.min(TOTAL_LEVELS, highestPassed));
  } catch (_error) {
    return 0;
  }
}

function trophyTier(level) {
  if (level <= 30) {
    return "Bronze";
  }
  if (level <= 70) {
    return "Silver";
  }
  return "Gold";
}

function trophyClass(level) {
  return trophyTier(level).toLowerCase();
}

function renderAchievements() {
  const highestPassed = loadQuizProgress();

  achievementsList.innerHTML = "";
  achievementsSummary.textContent = "Passed levels: " + highestPassed + " / " + TOTAL_LEVELS;

  if (highestPassed <= 0) {
    achievementsList.innerHTML = "<p class='empty-text'>No trophies yet. Pass any quiz level to earn one.</p>";
    return;
  }

  const grid = document.createElement("div");
  grid.className = "member-achievements-grid";

  for (let level = 1; level <= highestPassed; level += 1) {
    const card = document.createElement("article");
    card.className = "member-achievement-card";

    card.innerHTML = `
      <span class="member-achievement-mark ${trophyClass(level)}">${trophyTier(level)}</span>
      <div class="member-achievement-copy">
        <h3>Level ${level}</h3>
        <p>Quiz distinction</p>
      </div>
    `;

    grid.appendChild(card);
  }

  achievementsList.appendChild(grid);
}

function escapeHtml(value) {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

function renderBooksRead(data) {
  const books = data.books || [];
  const count = Number(data.count) || 0;

  achievementsBooksSummary.textContent = "Books read: " + count;
  achievementsBooksList.innerHTML = "";

  if (!books.length) {
    achievementsBooksList.innerHTML = "<p class='empty-text'>No books read yet.</p>";
    return;
  }

  books.forEach(book => {
    const item = document.createElement("div");
    item.className = "book-item";
    item.innerHTML = `
      <div class="book-item-info">
        <h4>${escapeHtml(book.name)}</h4>
        <p><strong>Author:</strong> ${escapeHtml(book.author)}</p>
        <p><strong>Year:</strong> ${escapeHtml(book.year || "-")}</p>
        <p><strong>Genre:</strong> ${escapeHtml(book.genre || "-")}</p>
      </div>
    `;
    achievementsBooksList.appendChild(item);
  });
}

function loadBooksRead() {
  fetch("../api/get-member-read-books.php?libraryId=" + encodeURIComponent(memberLibraryId))
    .then(response => response.json())
    .then(data => {
      renderBooksRead(data || {});
    })
    .catch(() => {
      achievementsBooksSummary.textContent = "Books read: 0";
      achievementsBooksList.innerHTML = "<p class='empty-text'>Could not load books read.</p>";
    });
}

renderAchievements();
loadBooksRead();

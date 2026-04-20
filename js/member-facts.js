const memberFactModal = document.getElementById("memberFactModal");
const memberFactClose = document.getElementById("memberFactClose");
const memberFactType = document.getElementById("memberFactType");
const memberFactTitle = document.getElementById("memberFactTitle");
const memberFactText = document.getElementById("memberFactText");
const memberFactsQuoteText = document.getElementById("memberFactsQuoteText");
const memberFactsQuoteAuthor = document.getElementById("memberFactsQuoteAuthor");
const memberFactTags = document.querySelectorAll(".member-facts-tags");
let facts = {};

loadRandomQuoteInto(memberFactsQuoteText, memberFactsQuoteAuthor);

function openFactModal(type, fact) {
  memberFactType.textContent = type === "book" ? "Book Fact" : "Writer Fact";
  memberFactTitle.textContent = fact.title;
  memberFactText.textContent = fact.text;
  memberFactModal.classList.add("active");
  memberFactModal.setAttribute("aria-hidden", "false");
  document.body.style.overflow = "hidden";
}

function closeFactModal() {
  memberFactModal.classList.remove("active");
  memberFactModal.setAttribute("aria-hidden", "true");
  document.body.style.overflow = "";
}

function createFactButton(type, key, fact) {
  const button = document.createElement("button");
  button.type = "button";
  button.className = "member-fact-tag";
  button.dataset.type = type;
  button.dataset.key = key;
  button.textContent = fact.title;
  return button;
}

function renderFactButtons() {
  memberFactTags.forEach(function (tagContainer) {
    const type = tagContainer.dataset.type;
    const typeFacts = facts[type] || {};
    tagContainer.innerHTML = "";

    Object.keys(typeFacts).forEach(function (key) {
      tagContainer.appendChild(createFactButton(type, key, typeFacts[key]));
    });
  });
}

async function loadFacts() {
  try {
    const response = await fetch("../data/facts.json");

    if (!response.ok) {
      throw new Error("Could not load facts.");
    }

    facts = await response.json();
    renderFactButtons();
  } catch (error) {
    memberFactTags.forEach(function (tagContainer) {
      tagContainer.innerHTML = "<p class=\"empty-message\">Facts could not be loaded.</p>";
    });
  }
}

document.addEventListener("click", function (event) {
  const tag = event.target.closest(".member-fact-tag");

  if (tag) {
    const type = tag.getAttribute("data-type");
    const key = tag.getAttribute("data-key");
    const fact = facts[type] && facts[type][key];

    if (!fact) {
      return;
    }

    openFactModal(type, fact);
    return;
  }

  if (event.target === memberFactModal) {
    closeFactModal();
  }
});

memberFactClose.addEventListener("click", function () {
  closeFactModal();
});

document.addEventListener("keydown", function (event) {
  if (event.key === "Escape") {
    closeFactModal();
  }
});

loadFacts();

const activeFinesList = document.getElementById("activeFinesList");
const fineSearchInput = document.getElementById("fineSearchInput");
let allFines = [];

function loadFines() {
  fetch("../api/get-fines.php")
    .then(response => response.json())
    .then(data => {
      allFines = data.activeFines || [];
      renderActiveFines();
    })
    .catch(() => {
      activeFinesList.innerHTML = "<p class='message'>Could not load fines.</p>";
    });
}

function getFilteredFines() {
  const query = (fineSearchInput.value || "").trim().toLowerCase();

  return allFines.filter(item => {
    if (!query) {
      return true;
    }

    const searchable = [
      item.memberName,
      item.libraryId,
      item.bookName,
      item.loanId
    ].join(" ").toLowerCase();

    return searchable.includes(query);
  });
}

function renderActiveFines() {
  const items = getFilteredFines();
  activeFinesList.innerHTML = "";

  if (!items.length) {
    activeFinesList.innerHTML = "<p class='empty-text'>No active fines found.</p>";
    return;
  }

  items.forEach(item => {
    const card = document.createElement("div");
    card.className = "fine-card";

    card.innerHTML = `
      <div class="fine-card-top">
        <h3>${item.memberName}</h3>
        <span class="member-library-badge">Book: ${item.bookName}</span>
      </div>

      <div class="fine-details-grid">
        <p><strong>Library ID:</strong> ${item.libraryId}</p>
        <p><strong>Due Date:</strong> ${item.dueDate}</p>
        <p><strong>Days Late:</strong> ${item.daysLate}</p>
        <p><strong>Fee Rate:</strong> $${item.feePerDay} / day</p>
        <p><strong>Fine:</strong> $${item.amount}</p>
        <p><strong>Notification Sent:</strong> ${item.notified ? "Yes" : "No"}</p>
        <p><strong>Status:</strong> Unreturned</p>
      </div>

      <div class="request-actions">
        <button type="button" class="approve-btn notify-btn" data-loan-id="${item.loanId}" ${item.notified ? "disabled" : ""}>
          ${item.notified ? "Notification Sent" : "Send Notification"}
        </button>
      </div>
    `;

    activeFinesList.appendChild(card);
  });

  document.querySelectorAll(".notify-btn").forEach(button => {
    button.addEventListener("click", function () {
      if (this.disabled) {
        return;
      }
      sendNotification(this.getAttribute("data-loan-id"));
    });
  });
}

function sendNotification(loanId) {
  const formData = new FormData();
  formData.append("loanId", loanId);

  fetch("../api/send-fine-notifications.php", {
    method: "POST",
    body: formData
  })
    .then(response => response.text())
    .then(text => {
      let data;

      try {
        data = JSON.parse(text);
      } catch (error) {
        alert("Notification request failed.");
        return;
      }

      if (data.success) {
        alert("Notification sent successfully.");
        loadFines();
      } else {
        alert(data.message || "Could not send notification.");
      }
    })
    .catch(() => {
      alert("Something went wrong.");
    });
}

fineSearchInput.addEventListener("input", function () {
  renderActiveFines();
});

loadFines();

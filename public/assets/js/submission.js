document.addEventListener("DOMContentLoaded", function () {
  const radios = document.querySelectorAll('input[name="radioDefault"]');
  const tbody = document.querySelector("tbody.table-group-divider");

  function escapeHtml(text) {
    const map = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;",
    };
    return String(text).replace(/[&<>"']/g, function (m) {
      return map[m];
    });
  }

  function renderRows(submissions) {
    tbody.innerHTML = "";
    submissions.forEach(function (s) {
      tbody.innerHTML += `
            <tr>
              <td scope="row">${parseInt(s.id)}</td>
              <td>${escapeHtml(s.submitted_at || "")}</td>
              <td scope="col">
                <a style="display: block; width:max-content;" href="/public/pages/profileview.php?username=${s.username}">
                <h6 class="text-info">${escapeHtml(s.username || "")}</h6>
                </a>
              </td>
              <td scope="col">
                <div>
                  <a style="display: block; width:max-content;" href="/public/pages/problem.php?problem_id=${parseInt(s.pid)}">
                    <h6 class="text-info">${escapeHtml(s.title || "")}</h6>
                  </a>
                </div>
                <small>${escapeHtml(s.category || "")}</small>
              </td>
              <td>${escapeHtml(s.language_name || "")}</td>
              <td><h6 style="color :${s.color_code || "black"}">${escapeHtml(s.display_name || "")}</h6></td>
              <td>${escapeHtml(s.difficulty || "")}</td>
              <td>${escapeHtml(s.execution_time_ms || "-")}ms</td>
              <td>${escapeHtml(s.memory_used_mb || "-")}mb</td>

            </tr>
          `;
    });
  }

  function fetchAndRender(type) {
    fetch(`/backend/problemset/submissions_filter.php?type=${type}`)
      .then((r) => r.json())
      .then((data) => {
        if (data.submissions && data.submissions.length > 0) {
          renderRows(data.submissions);
          console.log(data.submissions);
        } else {
          tbody.innerHTML =
            '<tr><td colspan="9s">No submissions found.</td></tr>';
        }
      })
      .catch(() => {
        tbody.innerHTML =
          '<tr><td colspan="9s">Error loading submissions.</td></tr>';
      });
  }

  radios.forEach((radio) => {
    radio.addEventListener("change", function () {
      if (this.checked) {
        let type = "all";
        if (this.id === "radioDefault2") type = "me";
        if (this.id === "radioDefault3") type = "favourites";
        fetchAndRender(type);
      }
    });
  });

  // Initial load
  fetchAndRender("all");
});

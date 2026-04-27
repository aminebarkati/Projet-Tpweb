document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("submissionForm");
  const sideMessageDiv = document.getElementById("submissionMessage");
  const modalMessageDiv = document.getElementById("submissionModalMessage");
  const languageSelect = document.getElementById("languageSelect");
  const recentContainer = document.getElementById("recentSubmissions");
  const problemId = recentContainer
    ? parseInt(recentContainer.getAttribute("data-problem-id") || "0", 10)
    : 0;
  const copyButtons = document.querySelectorAll(".copy-testcase-btn");

  copyButtons.forEach(function (button) {
    button.addEventListener("click", function () {
      const targetId = button.getAttribute("data-copy-target");

      if (!targetId) {
        return;
      }

      const source = document.getElementById(targetId);

      if (!source) {
        return;
      }

      copyToClipboard(source.textContent || "", button);
    });
  });

  if (problemId > 0) {
    loadRecentSubmissions();
  }

  if (!form) {
    return;
  }

  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(form);
    let codeValue = (formData.get("code") || "").toString();
    const solutionFileInput = document.getElementById("solutionFile");
    const attachedFile =
      solutionFileInput && solutionFileInput.files
        ? solutionFileInput.files[0]
        : null;

    if (attachedFile && !codeValue.trim()) {
      if (!isAttachedFileMatchingLanguage(attachedFile, languageSelect)) {
        showMessage(
          "Attached file extension does not match selected language",
          "warning",
        );
        return;
      }

      try {
        codeValue = await readFileContent(attachedFile);
      } catch (error) {
        showMessage("Could not read attached file", "danger");
        return;
      }
    }

    const data = {
      problem_id: parseInt(formData.get("problem_id"), 10),
      language_id: parseInt(formData.get("language_id"), 10),
      code: codeValue,
      attached_filename: attachedFile ? attachedFile.name : "",
    };

    if (!data.language_id) {
      showMessage("Please select a programming language", "warning");
      return;
    }

    if (!data.code.trim()) {
      showMessage("Please enter code or attach a solution file", "warning");
      return;
    }

    if (data.code.length > 262144) {
      showMessage("Code exceeds maximum size of 256KB", "danger");
      return;
    }

    clearMessage(sideMessageDiv);
    clearMessage(modalMessageDiv);

    fetch("/backend/problemset/submit.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (result) {
        if (result.success) {
          showMessage(
            "Solution submitted successfully! You can check the verdict status on the submissions page.",
            "success",
          );
          form.reset();
          closeSubmitModal();
          loadRecentSubmissions();
          return;
        }

        showMessage(result.message || "Failed to submit solution", "danger");
      })
      .catch(function (error) {
        showMessage("Error: " + error.message, "danger");
      });
  });

  function showMessage(message, type) {
    const isErrorType = type === "danger" || type === "warning";
    const target = isErrorType
      ? modalMessageDiv || sideMessageDiv
      : sideMessageDiv || modalMessageDiv;

    if (!target) {
      return;
    }

    target.className = "alert alert-" + type;
    target.textContent = message;
    target.classList.remove("d-none");
    target.scrollIntoView({ behavior: "smooth", block: "nearest" });
  }

  function clearMessage(target) {
    if (!target) {
      return;
    }

    target.classList.add("d-none");
    target.textContent = "";
  }

  function closeSubmitModal() {
    const modalElement = document.getElementById("submitSolutionModal");

    if (!modalElement || !window.bootstrap || !window.bootstrap.Modal) {
      return;
    }

    const modalInstance = window.bootstrap.Modal.getInstance(modalElement);

    if (modalInstance) {
      modalInstance.hide();
    }
  }

  function readFileContent(file) {
    return new Promise(function (resolve, reject) {
      const reader = new FileReader();

      reader.onload = function () {
        resolve((reader.result || "").toString());
      };

      reader.onerror = function () {
        reject(new Error("File read failed"));
      };

      reader.readAsText(file);
    });
  }

  function isAttachedFileMatchingLanguage(file, selectElement) {
    if (!file || !selectElement || !selectElement.value) {
      return true;
    }

    const selectedOption = selectElement.options[selectElement.selectedIndex];
    if (!selectedOption) {
      return true;
    }

    const expectedExtension = (
      selectedOption.getAttribute("data-extension") || ""
    )
      .trim()
      .toLowerCase();
    if (!expectedExtension) {
      return true;
    }

    const fileExtension = getFileExtension(file.name);
    return fileExtension === expectedExtension;
  }

  function getFileExtension(fileName) {
    const lastDotIndex = fileName.lastIndexOf(".");
    if (lastDotIndex < 0) {
      return "";
    }

    return fileName.slice(lastDotIndex).toLowerCase();
  }

  function copyToClipboard(text, button) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard
        .writeText(text)
        .then(function () {
          markCopied(button);
        })
        .catch(function () {
          fallbackCopy(text, button);
        });
      return;
    }

    fallbackCopy(text, button);
  }

  function fallbackCopy(text, button) {
    const tempTextArea = document.createElement("textarea");
    tempTextArea.value = text;
    document.body.appendChild(tempTextArea);
    tempTextArea.select();
    document.execCommand("copy");
    document.body.removeChild(tempTextArea);
    markCopied(button);
  }

  function markCopied(button) {
    const previous = button.textContent;
    button.textContent = "Copied";
    setTimeout(function () {
      button.textContent = previous;
    }, 1200);
  }

  function loadRecentSubmissions() {
    const container = recentContainer;

    if (!container || problemId <= 0) {
      return;
    }

    fetch(
      "/backend/problemset/recent-submissions.php?problem_id=" +
        encodeURIComponent(problemId),
    )
      .then(function (response) {
        return response.json();
      })
      .then(function (result) {
        if (!result.success) {
          container.innerHTML =
            '<small class="text-danger">' +
            escapeHtml(result.message || "Could not load submissions") +
            "</small>";
          return;
        }

        const items = Array.isArray(result.items) ? result.items : [];
        if (items.length === 0) {
          container.innerHTML =
            '<small class="text-muted">No submissions yet for this problem.</small>';
          return;
        }

        container.innerHTML = renderRecentSubmissions(items);
      })
      .catch(function () {
        container.innerHTML =
          '<small class="text-danger">Failed to load recent submissions.</small>';
      });
  }

  function renderRecentSubmissions(items) {
    return items
      .map(function (item) {
        const verdict = item.verdict || "PENDING";
        const verdictColor = item.color_code || "black";
        const submittedAt = formatDateTime(item.submitted_at);
        const languageName = item.language_name || "Unknown";
        const execTime =
          item.execution_time_ms != null
            ? String(item.execution_time_ms) + " ms"
            : "-";
        console.log(verdictColor);
        return (
          '<div class="border rounded p-2 mb-2 bg-white">' +
          '<div class="d-flex justify-content-between align-items-center mb-1">' +
          '<span class="badge" ' +
          ' style="background-color:' +
          verdictColor +
          '">' +
          escapeHtml(verdict) +
          "</span>" +
          '<small class="text-muted">#' +
          escapeHtml(String(item.id || "")) +
          "</small>" +
          "</div>" +
          "<div><small><strong>" +
          escapeHtml(languageName) +
          "</strong></small></div>" +
          '<div><small class="text-muted">' +
          escapeHtml(submittedAt) +
          "</small></div>" +
          '<div><small class="text-muted">Time: ' +
          escapeHtml(execTime) +
          "</small></div>" +
          "</div>"
        );
      })
      .join("");
  }

  function formatDateTime(value) {
    if (!value) {
      return "Unknown time";
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
      return String(value);
    }

    return date.toLocaleString();
  }

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/\"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
});

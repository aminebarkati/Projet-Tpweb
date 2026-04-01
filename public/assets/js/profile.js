const profileMessage = document.querySelector("#profileMessage");
const avatarUploadForm = document.querySelector("#avatarUploadForm");
const avatarFileInput = document.querySelector("#avatar_file_top");
const profileDetailsForm = document.querySelector("#profileDetailsForm");
const profilePasswordForm = document.querySelector("#profilePasswordForm");
const adminRoleForm = document.querySelector("#adminRoleForm");
const adminDeductPointsForm = document.querySelector("#adminDeductPointsForm");
const adminDeleteAccountForm = document.querySelector(
  "#adminDeleteAccountForm",
);

const profileDisplayUsername = document.querySelector(
  "#profileDisplayUsername",
);
const profileDisplayEmail = document.querySelector("#profileDisplayEmail");
const profileRoleBadge = document.querySelector("#profileRoleBadge");
const profileRatingBadge = document.querySelector("#profileRatingBadge");

const accountInfoRole = document.querySelector("#accountInfoRole");
const accountInfoRating = document.querySelector("#accountInfoRating");
const accountInfoCreatedAt = document.querySelector("#accountInfoCreatedAt");
const accountInfoUpdatedAt = document.querySelector("#accountInfoUpdatedAt");

const profileAvatarImage = document.querySelector("#profileAvatarImage");
const profileAvatarFallback = document.querySelector("#profileAvatarFallback");

function showMessage(type, text) {
  if (!profileMessage) {
    return;
  }

  profileMessage.classList.remove("d-none", "alert-success", "alert-danger");
  profileMessage.classList.add(
    type === "success" ? "alert-success" : "alert-danger",
  );
  profileMessage.textContent = text;
}

function updateUserDisplay(user) {
  if (!user) {
    return;
  }

  if (profileDisplayUsername) {
    profileDisplayUsername.textContent = user.username;
  }
  if (profileDisplayEmail) {
    profileDisplayEmail.textContent = user.email;
  }

  const roleText = user.is_admin ? "Admin" : "User";
  if (profileRoleBadge) {
    profileRoleBadge.textContent = `Role: ${roleText}`;
  }
  if (profileRatingBadge) {
    profileRatingBadge.textContent = `Rating: ${user.rating}`;
  }

  if (accountInfoRole) {
    accountInfoRole.textContent = roleText;
  }
  if (accountInfoRating) {
    accountInfoRating.textContent = String(user.rating);
  }
  if (accountInfoCreatedAt) {
    accountInfoCreatedAt.textContent = user.created_at;
  }
  if (accountInfoUpdatedAt) {
    accountInfoUpdatedAt.textContent = user.updated_at;
  }
}

if (avatarUploadForm && avatarFileInput) {
  avatarUploadForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (!avatarFileInput.files || avatarFileInput.files.length === 0) {
      showMessage("error", "Please choose an image to upload.");
      return;
    }

    const formData = new FormData(avatarUploadForm);
    const response = await fetch("/backend/profile/update-avatar.php", {
      method: "POST",
      body: formData,
    });
    const data = await response.json();

    if (!data.success) {
      showMessage("error", data.message || "Avatar update failed.");
      return;
    }

    showMessage("success", data.message || "Avatar updated successfully.");
    if (profileAvatarImage && data.avatar_src) {
      profileAvatarImage.src = data.avatar_src + `?v=${Date.now()}`;
      profileAvatarImage.classList.remove("d-none");
      if (profileAvatarFallback) {
        profileAvatarFallback.classList.add("d-none");
      }
    } else {
      window.location.reload();
    }
  });

  avatarFileInput.addEventListener("change", () => {
    avatarUploadForm.requestSubmit();
  });
}

if (profileDetailsForm) {
  profileDetailsForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (!profileDetailsForm.checkValidity()) {
      profileDetailsForm.classList.add("was-validated");
      return;
    }

    const response = await fetch("/backend/profile/update-profile.php", {
      method: "POST",
      body: new FormData(profileDetailsForm),
    });
    const data = await response.json();

    if (!data.success) {
      showMessage("error", data.message || "Profile update failed.");
      return;
    }

    showMessage("success", data.message || "Profile updated successfully.");
    updateUserDisplay(data.user);
  });
}

if (profilePasswordForm) {
  profilePasswordForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const currentPassword =
      profilePasswordForm.querySelector("#current_password");
    const newPassword = profilePasswordForm.querySelector("#new_password");
    const confirmPassword =
      profilePasswordForm.querySelector("#confirm_password");

    if (!currentPassword || !newPassword || !confirmPassword) {
      return;
    }

    const validator = window.validatePassword;
    if (typeof validator === "function") {
      const errors = validator(newPassword.value);
      if (errors.length > 0) {
        const formatted = errors
          .map((entry) => entry.substring(1, entry.length))
          .join(" | ");
        showMessage("error", `Weak password: ${formatted}`);
        return;
      }
    }

    if (newPassword.value !== confirmPassword.value) {
      showMessage("error", "New password and confirmation do not match.");
      confirmPassword.classList.add("is-invalid");
      return;
    }

    confirmPassword.classList.remove("is-invalid");

    const response = await fetch("/backend/profile/update-password.php", {
      method: "POST",
      body: new FormData(profilePasswordForm),
    });
    const data = await response.json();

    if (!data.success) {
      showMessage("error", data.message || "Password update failed.");
      return;
    }

    profilePasswordForm.reset();
    showMessage("success", data.message || "Password updated successfully.");
  });
}

if (adminRoleForm) {
  adminRoleForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const response = await fetch("/backend/profile/update-role.php", {
      method: "POST",
      body: new FormData(adminRoleForm),
    });
    const data = await response.json();

    if (!data.success) {
      showMessage("error", data.message || "Role update failed.");
      return;
    }

    const isAdmin = data.user && data.user.is_admin;
    const roleText = isAdmin ? "Admin" : "User";
    if (profileRoleBadge) {
      profileRoleBadge.textContent = `Role: ${roleText}`;
    }
    if (accountInfoRole) {
      accountInfoRole.textContent = roleText;
    }

    showMessage("success", data.message || "Role updated successfully.");
  });
}

if (adminDeductPointsForm) {
  adminDeductPointsForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const response = await fetch("/backend/profile/deduct-points.php", {
      method: "POST",
      body: new FormData(adminDeductPointsForm),
    });
    const data = await response.json();

    if (!data.success) {
      showMessage("error", data.message || "Could not deduct points.");
      return;
    }

    if (data.user && typeof data.user.rating !== "undefined") {
      if (profileRatingBadge) {
        profileRatingBadge.textContent = `Rating: ${data.user.rating}`;
      }
      if (accountInfoRating) {
        accountInfoRating.textContent = String(data.user.rating);
      }
    }

    showMessage("success", data.message || "Points deducted successfully.");
  });
}

if (adminDeleteAccountForm) {
  adminDeleteAccountForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const confirmed = window.confirm(
      "Are you sure you want to permanently delete this account?",
    );
    if (!confirmed) {
      return;
    }

    const response = await fetch("/backend/profile/delete-account.php", {
      method: "POST",
      body: new FormData(adminDeleteAccountForm),
    });
    const data = await response.json();

    if (!data.success) {
      showMessage("error", data.message || "Account deletion failed.");
      return;
    }

    showMessage("success", data.message || "Account deleted successfully.");
    window.location.href = "/public/pages/users.php";
  });
}

const form = document.querySelector("#loginform");
const form2 = document.querySelector("#signupform");
const username = document.querySelector("#floatingInputValue1");
const username2 = document.querySelector("#floatingInputValue11");
const password = document.querySelector("#floatingInputValue3");
const password2 = document.querySelector("#floatingInputValue33");
const confirmpassword = document.querySelector("#floatingInputValue44");
const resetbtn = document.getElementsByName("reset");
const loginAlert = document.querySelector("#passalert");
const alert2 = document.querySelector("#passalert2");

function validatePassword(password) {
  const errors = [];

  if (password.length < 8) {
    errors.push("*At least 8 characters long");
  }
  if (!/[A-Z]/.test(password)) {
    errors.push(".uppercase letter");
  }
  if (!/[a-z]/.test(password)) {
    errors.push(".lowercase letter");
  }
  if (!/[0-9]/.test(password)) {
    errors.push(".digit");
  }
  if (!/[!@#$%^&*]/.test(password)) {
    errors.push(".special character");
  }

  return errors;
}

window.validatePassword = validatePassword;

if (form) {
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (form.checkValidity()) {
      const res = await fetch("../../backend/auth/login.php", {
        method: "POST",
        body: new FormData(e.target),
      });
      const data = await res.json();
      console.log(data.redirect);
      if (data.success) {
        window.location.href = data.redirect ?? "/";
      } else {
        if (loginAlert) {
          loginAlert.style.display = "block";
          loginAlert.textContent = data.message;
        }
        if (password) {
          password.classList.add("is-invalid");
        }
        if (username) {
          username.classList.add("is-invalid");
        }
      }
    }
  });
}

if (form2) {
  form2.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (form2.checkValidity() && password2.value == confirmpassword.value) {
      const res = await fetch("../../backend/auth/signup.php", {
        method: "POST",
        body: new FormData(e.target),
      });
      const data = await res.json();
      if (data.success) {
        window.location.href = data.redirect ?? "/";
      } else {
        if (alert2) {
          alert2.style.display = "block";
          alert2.textContent = data.message;
        }
        if (username2) {
          username2.classList.add("is-invalid");
        }
      }
    } else {
      form2.classList.add("was-validated");
    }
  });
}

if (resetbtn.length) {
  resetbtn.forEach((element) => {
    element.addEventListener("click", () => {
      const inputs = document.getElementsByName("inputform");
      inputs.forEach((input) => {
        input.value = "";
      });
    });
  });
}

if (confirmpassword && password2) {
  confirmpassword.addEventListener("input", () => {
    if (password2.value != confirmpassword.value) {
      if (alert2) {
        alert2.style.display = "block";
        alert2.textContent = "Passwords don't match!";
      }
      confirmpassword.classList.add("is-invalid");
    } else {
      if (alert2) {
        alert2.style.display = "none";
      }
      confirmpassword.classList.remove("is-invalid");
    }
  });
}

if (password2 && confirmpassword) {
  password2.addEventListener("blur", () => {
    const errors = validatePassword(password2.value);
    let ok = true;
    let msg = "";
    errors.forEach((entry) => {
      if (entry[0] == "." && ok) {
        ok = false;
        msg += "Contains at least one :";
      }
      if (entry[0] != "*") {
        msg += "\n -";
      }
      msg += entry.substring(1, entry.length);
      if (entry[0] == "*") {
        msg += "\n";
      }
    });
    if (errors.length > 0) {
      confirmpassword.disabled = true;
      confirmpassword.value = "";
      if (alert2) {
        alert2.style.display = "block";
        alert2.textContent = msg;
      }
      password2.classList.add("is-invalid");
    } else {
      if (alert2) {
        alert2.style.display = "none";
      }
      confirmpassword.disabled = false;
      password2.classList.remove("is-invalid");
    }
  });
}

const form = document.querySelector("#loginform");
const form2 = document.querySelector("#signupform");
const username = document.querySelector("#floatingInputValue1");
const username2 = document.querySelector("#floatingInputValue11");
const password = document.querySelector("#floatingInputValue3");
const password2 = document.querySelector("#floatingInputValue33");
const confirmpassword = document.querySelector("#floatingInputValue44");
const resetbtn = document.getElementsByName("reset");
const alert = document.querySelector("#passalert");
const alert2 = document.querySelector("#passalert2");

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  if (form.checkValidity()) {
    const res = await fetch("../../backend/auth/login.php", {
      method: "POST",
      body: new FormData(e.target),
    });
    const data = await res.json();
    if (data.success) {
      window.location.href = data.redirect ?? "/";
    } else {
      alert.style.display = "block";
      alert.textContent = data.message;
      password.classList.add("is-invalid");
      username.classList.add("is-invalid");
    }
  }
});
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
      alert2.style.display = "block";
      alert2.textContent = data.message;
      username2.classList.add("is-invalid");
    }
  } else {
    form2.classList.add("was-validated");
  }
});
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

resetbtn.forEach((element) => {
  element.addEventListener("click", (e) => {
    const inputs = document.getElementsByName("inputform");
    inputs.forEach((e) => {
      e.value = "";
    });
  });
});

confirmpassword.addEventListener("input", (e) => {
  if (password2.value != confirmpassword.value) {
    alert2.style.display = "block";
    alert2.textContent = "Passwords don't match!";
    confirmpassword.classList.add("is-invalid");
  } else {
    alert2.style.display = "none ";
    confirmpassword.classList.remove("is-invalid");
  }
});

password2.addEventListener("blur", (e) => {
  const errors = validatePassword(password2.value);
  let ok = true;
  let msg = "";
  errors.forEach((e) => {
    if (e[0] == "." && ok) {
      ok = false;
      msg += "Contains at least one :";
    }
    if (e[0] != "*") {
      msg += "\n -";
    }
    msg += e.substring(1, e.length);
    if (e[0] == "*") {
      msg += "\n";
    }
  });
  if (errors.length > 0) {
    confirmpassword.disabled = true;
    confirmpassword.value = "";
    alert2.style.display = "block";
    alert2.textContent = msg;
    password2.classList.add("is-invalid");
  } else {
    alert2.style.display = "none";
    confirmpassword.disabled = false;
    password2.classList.remove("is-invalid");
  }
});

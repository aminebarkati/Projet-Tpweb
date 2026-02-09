const form = document.querySelector("#loginform");
const form2 = document.querySelector("#signupform");
const password = document.querySelector("#floatingInputValue4");
const confirmpassword = document.querySelector("#floatingInputValue5");
const resetbtn = document.getElementsByName("reset");
const alert = document.querySelector("#passalert");

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
  if (password.value != confirmpassword.value) {
    alert.style.display = "block";
    alert.textContent = "Passwords don't match!";
    confirmpassword.classList.add("is-invalid");
  } else {
    alert.style.display = "none ";
    confirmpassword.classList.remove("is-invalid");
  }
});

password.addEventListener("blur", (e) => {
  const errors = validatePassword(password.value);
  console.log(errors);
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
    alert.style.display = "block";
    alert.textContent = msg;
    password.classList.add("is-invalid");
  } else {
    alert.style.display = "none";
    confirmpassword.disabled = false;
    password.classList.remove("is-invalid");
  }
});

form.addEventListener("submit", (e) => {
  if (!form.checkValidity()) {
    e.preventDefault();
  }
  form.classList.add("was-validated");
});

form2.addEventListener("submit", (e) => {
  if (!form2.checkValidity()) {
    e.preventDefault();
  }
  form2.classList.add("was-validated");
});

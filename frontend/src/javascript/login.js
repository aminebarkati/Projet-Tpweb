const form = document.querySelector("#loginform");
const form2 = document.querySelector("#signupform");

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

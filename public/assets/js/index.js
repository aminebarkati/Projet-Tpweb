// function setCookie(name, value, days) {
//   let expires = "";
//   if (days) {
//     const date = new Date();
//     date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
//     expires = "; expires=" + date.toUTCString();
//   }
//   document.cookie =
//     name + "=" + encodeURIComponent(value) + expires + "; path=/";
// }

// const logout = document.querySelector("#logbtn");
// logout.addEventListener("click", () => {
//   setCookie("logedIn", 0, -2);
// });

// logout.addEventListener("click", function () {
//   fetch("logout.php")
//     .then((response) => response.text())
//     .then((data) => {
//       document.getElementById("result").innerText = data;
//     });
// });

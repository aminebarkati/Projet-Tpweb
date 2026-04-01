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
// const pages = {
//   1: "Home",
//   2: "Problemset",
//   3: "Contests",
//   4: "Submissions",
//   5: "Leaderboard",
//   6: "Profile",
// };
// const pages = {
//   1: "problemset.php",
//   2: "contests.php",
//   3: "submissions.php",
//   4: "leaderboard.php",
//   5: "profile.php",
//   6: "administrationboard.php",2026-03-31 21:01:58
// };
const enterProfileBtn = document.querySelector("#enterProfileBtn");
const navTabs = document.querySelectorAll(".tabs");
let entered = false;
let i = 0;
navTabs.forEach((element) => {
  if (document.URL.includes(element.href) && i > 0) {
    // console.log(`key: ${key} , value: ${element}`);
    // console.log(navTabs[key]);
    element.classList.add("text-secondary");
    element.classList.remove("text-white");
    entered = true;
  }
  i++;
});
if (!entered && document.URL == "http://localhost:8000/") {
  navTabs[0].classList.add("text-secondary");
  navTabs[0].classList.remove("text-white");
}

if (enterProfileBtn) {
  enterProfileBtn.addEventListener("click", (e) => {});
}

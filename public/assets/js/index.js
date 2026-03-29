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
const pages = {
  1: "problemset.php",
  2: "contests.php",
  3: "submissions.php",
  4: "leaderboard.php",
  5: "profile.php",
};
const navTabs = document.querySelectorAll(".tabs");
let entered = false;
Object.entries(pages).forEach(([key, element]) => {
  if (document.URL.includes(element)) {
    // console.log(`key: ${key} , value: ${element}`);
    // console.log(navTabs[key]);
    navTabs[key].classList.add("text-secondary");
    navTabs[key].classList.remove("text-white");
    entered = true;
  }
});
if (!entered) {
  navTabs[0].classList.add("text-secondary");
  navTabs[0].classList.remove("text-white");
}

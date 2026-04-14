const s1 = document.querySelector(".s1");
const s2 = document.querySelector(".s2");
const target_user_id = document.querySelector("#accountInfoId");
const user_id = document.querySelector("#user_id");

if (s2) {
  s2.addEventListener("click", (e) => {
    const databody = {
      user_id: user_id.value,
      favorite_user_id: target_user_id.innerHTML,
    };
    fetch("/backend/profile/make-favorite.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(databody),
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (result) {
        if (result.success) {
          s2.classList.add("x");
          s1.classList.remove("x");
          return;
        }
      });
  });
}

if (s1) {
  s1.addEventListener("click", (e) => {
    const databody = {
      user_id: user_id.value,
      favorite_user_id: target_user_id.innerHTML,
    };
    fetch("/backend/profile/delete-favorite.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(databody),
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (result) {
        if (result.success) {
          s1.classList.add("x");
          s2.classList.remove("x");
          return;
        }
      });
  });
}

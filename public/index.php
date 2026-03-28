<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AlgoSpark</title>
  <link
    rel="icon"
    type="image/svg+xml"
    sizes="any"
    href="/public/assets/media/svg-components/code-slash.svg" />
  <link
    rel="stylesheet"
    href="/public/assets/css/bootstrap.min.css" />
  <link rel="stylesheet" href="/public/assets/css/style.css" />
</head>

<body>
  <header>
    <div class="px-3 py-4 border-bottom nav1">
      <div class="container">
        <div
          class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
          <a
            href="#"
            class="d-flex align-items-center my-2 my-lg-0 me-lg-auto text-white text-decoration-none">
            <div class="d-flex justify-content-center align-items-center">
              <object
                data="/public/assets/media/svg-components/code-slash.svg"
                type="image/svg+xml"
                width="50"
                height="50"
                class="px-1"></object>
              <div class="px-1 mb-0 h4 text-white">AlgoSpark</div>
            </div>
          </a>
          <ul
            class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
            <li>
              <a href="/" class="nav-link text-secondary"> Home </a>
            </li>
            <li>
              <a href="/public/pages/problemset.php" class="nav-link text-white">
                Problemset
              </a>
            </li>
            <li>
              <a href="/public/pages/contests.php" class="nav-link text-white">
                Contests
              </a>
            </li>
            <li>
              <a href="/public/pages/submissions.php" class="nav-link text-white">
                Submissions
              </a>
            </li>
            <li>
              <a href="/public/pages/leaderboard.php" class="nav-link text-white">
                Leaderboard
              </a>
            </li>
            <li>
              <a href="/public/pages/profile.php" class="nav-link text-white"> Profile </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="px-3 py-2 border-bottom mb-3 log">
      <div class="container d-flex flex-wrap justify-content-center">
        <form
          class="col-12 col-lg-auto mb-2 mb-lg-0 me-lg-auto"
          role="search">
          <input
            type="search"
            class="form-control"
            placeholder="Search..."
            aria-label="Search" />
        </form>
        <div class="btns-bar">
          <button
            type="button"
            class="btn btn-light tex me-2"
            id="logbtn"
            data-bs-toggle="modal"
            data-bs-target="#LogModal">
            Login
          </button>
          <?php include __DIR__ . "/components/login-modal.php"; ?>
          <button
            type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#signmodal"
            id="signbtn">
            Sign-up
          </button>
          <?php include __DIR__ . "/components/signin-modal.php"; ?>
        </div>
      </div>
    </div>
  </header>
  <div class="container"></div>
  <script src="/public/assets/js/login.js"></script>
  <script src="/public/assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
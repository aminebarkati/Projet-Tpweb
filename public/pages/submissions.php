<!doctype html>
<html lang="en">
<?php
require_once __DIR__ . '/../components/head.php';
$SubmissionsRepository = new SubmissionsRepository();
$Submissions = $SubmissionsRepository->findAll();
?>

<body>
  <header>
    <?php
    require_once __DIR__ . '/../components/nav.php';
    require_once __DIR__ . '/../components/search-bar.php';
    ?>
  </header>
  <div class="container mt-5 table-responsive">
    <table
      class="table table-striped align-middle table-hover caption-top test-info">
      <caption>
        Submissions
        <?php if ($currentUser): ?>
          <div style="float: right;">
            <div class="form-check-inline">
              <input class="form-check-input" type="radio" name="radioDefault" id="radioDefault1" checked>
              <label class="form-check-label" for="radioDefault1">
                all
              </label>
            </div>
            <div class="form-check-inline">
              <input class="form-check-input" type="radio" name="radioDefault" id="radioDefault2">
              <label class="form-check-label" for="radioDefault2">
                only me
              </label>
            </div>
            <div class="form-check-inline">
              <input class="form-check-input" type="radio" name="radioDefault" id="radioDefault3">
              <label class="form-check-label" for="radioDefault3">
                favourites
              </label>
            </div>
          </div>
        <?php endif; ?>
      </caption>
      <thead>
        <th scope="col">Id</th>
        <th scope="col">Time</th>
        <th scope="col">Username</th>
        <th scope="col">Name</th>
        <th scope="col">Language</th>
        <th scope="col">Verdict</th>
        <th scope="col">Difficulity</th>
        <th scope="col">MS</th>
        <th scope="col">MB</th>
      </thead>
      <tbody class="table-group-divider">
      </tbody>
      <tfoot class="table-group-divider"></tfoot>
    </table>
  </div>
  <script src="/public/assets/js/auth.js"></script>
  <script src="/public/assets/js/index.js"></script>
  <script src="/public/assets/js/bootstrap.bundle.min.js"></script>
  <script src="/public/assets/js/submission.js"></script>
</body>

</html>
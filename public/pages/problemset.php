<!doctype html>
<html lang="en">
<?php
require_once __DIR__ . '/../components/head.php';
$ProblemsRepository = new ProblemsRepository();
$Problems = $ProblemsRepository->findAll();
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
        Available problemes
      </caption>
      <thead>
        <th scope="col">Id</th>
        <th scope="col">Title</th>
        <th></th>
        <th scope="col">difficulity</th>
        <th scope="col">Solves</th>
        <th scope="col">Sucess Rate</th>

      </thead>
      <tbody class="table-group-divider">
        <?php
        foreach ($Problems as $Problem):
        ?>
          <tr class="my-row">
            <td scope="row"><?= (int) $Problem->id ?></td>
            <td scope="col">
              <div>
                <a style="display: block; width:max-content;" href="/public/pages/problem.php?problem_id=<?= (int) $Problem->id ?>">
                  <h6 class="text-info"><?= htmlspecialchars((string) $Problem->title, ENT_QUOTES, 'UTF-8') ?></h6>
                </a>
              </div>
              <small><?= htmlspecialchars((string) $Problem->category, ENT_QUOTES, 'UTF-8') ?></small>
            </td>
            <td><?php
                $x = new DateTime();
                $y = new DateTime((string) $Problem->created_at);
                $dif = $x->diff($y)->days <  5 ? 'New' : '';
                echo $dif;
                ?>
            </td>
            <td>
              <h6 class="text-secondary"><?= htmlspecialchars((string) $Problem->difficulty, ENT_QUOTES, 'UTF-8') ?></h6>
            </td>
            <td><?= (int) $Problem->success_count ?></td>
            <td><?= (int) $Problem->acceptance_rate ?> %</td>
            <td>
              <a href="/public/pages/problem.php?problem_id=<?= (int) $Problem->id ?>" class="enterBtn text-info">Enter ></a>
            </td>

          </tr>
        <?php endforeach; ?>

      </tbody>
      <tfoot class="table-group-divider"></tfoot>
    </table>
  </div>
  <script src="/public/assets/js/auth.js"></script>
  <script src="/public/assets/js/index.js"></script>
  <script src="/public/assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<!doctype html>
<html lang="en">
<?php
session_start();
require_once __DIR__ . '/../components/head.php';
$UserRepository = new UserRepository();
$Users = $UserRepository->findAll();
if (!isset($_SESSION['role']) || $_SESSION["role"] != 'Admin') {
    header("location:index.php");
}
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
                Users
            </caption>
            <thead>
                <th scope="col">Id</th>
                <th scope="col">Username</th>
                <th scope="col">Email</th>
                <th scope="col">Rating</th>
                <th scope="col">Role</th>
                <th scope="col">Creation date</th>
                <th scope="col">Last update</th>
            </thead>
            <tbody class="table-group-divider">
                <?php
                foreach ($Users as $User):
                ?>
                    <tr>
                        <td scope="row"><?= (int) $User->id ?></td>
                        <td scope="col">
                            <h6 class="text-info"><?= htmlspecialchars((string) $User->username, ENT_QUOTES, 'UTF-8') ?></h6>
                        </td>
                        <td>
                            <h6 class="text-secondary"><?= htmlspecialchars((string) $User->email, ENT_QUOTES, 'UTF-8') ?></h6>
                        </td>
                        <td><?= (int) $User->rating ?></td>
                        <td><?= !empty($User->is_admin) ? 'Admin' : 'User' ?></td>
                        <td><?= htmlspecialchars((string) $User->created_at, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $User->updated_at, ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a href="/public/pages/profile.php?user_id=<?= (int) $User->id ?>" class="btn btn-primary">Manage</a>
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
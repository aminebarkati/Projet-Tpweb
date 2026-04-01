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
        <?php
        session_start();
        if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] == false) {
            require_once 'btns-bar.php';
        } else {
            require_once 'logout-bar.php';
        }
        ?>
    </div>
</div>
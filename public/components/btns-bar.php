<div class="btns-bar">
    <button
        type="button"
        class="btn btn-light tex me-2"
        id="logbtn"
        data-bs-toggle="modal"
        data-bs-target="#LogModal">
        Login
    </button>
    <?php require_once "login-modal.php"; ?>
    <button
        type="button"
        class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#signmodal"
        id="signbtn">
        Sign-up
    </button>
    <?php require_once "signin-modal.php"; ?>
</div>
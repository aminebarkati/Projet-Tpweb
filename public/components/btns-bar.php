<div class="btns-bar">
    <button
        type="button"
        class="btn btn-light tex me-2"
        id="logbtn"
        data-bs-toggle="modal"
        data-bs-target="#LogModal">
        Log In
    </button>
    <?php require_once "login-modal.php"; ?>
    <button
        type="button"
        class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#signmodal"
        id="signbtn">
        Sign Up
    </button>
    <?php require_once "signup-modal.php"; ?>
</div>
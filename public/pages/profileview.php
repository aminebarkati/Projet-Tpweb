<!doctype html>
<html lang="en">
<?php
require_once __DIR__ . '/../components/head.php';
require_once __DIR__ . '/../../backend/autoloader.php';

$UserRepository = new UserRepository();
$FavoriteRepository = new FavoriteRepository();
$targetUser = null;
if (!empty($currentUser)) {
    $requestedTargetUsername = isset($_GET['username']) ? (string) $_GET['username'] : null;
    if (!empty($requestedTargetUsername)) {
        $candidateTarget = $UserRepository->findByUsername($requestedTargetUsername);
        if ($candidateTarget) {
            $targetUser = $candidateTarget;
        }
    }
}

$avatarUrlValue = $targetUser ? trim((string) ($targetUser->avatar_url ?? '')) : '';
$avatarSrc = $avatarUrlValue !== '' ? '/storage/imgs/' . rawurlencode($avatarUrlValue) : '';
$showAvatarImage = $avatarSrc !== '';
$roleLabel = ($targetUser && !empty($targetUser->is_admin)) ? 'Admin' : 'User';
$initial = $targetUser ? strtoupper(substr((string) $targetUser->username, 0, 1)) : 'U';
$isFavourite = !empty($FavoriteRepository->checkFavoriteById($currentUser->id, $targetUser->id));
?>

<body>
    <header>
        <?php
        require_once __DIR__ . '/../components/nav.php';
        require_once __DIR__ . '/../components/search-bar.php';
        ?>
    </header>
    <main class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                            <div class="position-relative">
                                <?php if ($showAvatarImage): ?>
                                    <img
                                        id="profileAvatarImage"
                                        src="<?= htmlspecialchars($avatarSrc, ENT_QUOTES, 'UTF-8') ?>"
                                        alt="Profile avatar"
                                        class="rounded-circle border"
                                        style="width: 96px; height: 96px; object-fit: cover;"
                                        onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');">
                                <?php endif; ?>
                                <div
                                    id="profileAvatarFallback"
                                    class="rounded-circle border text-white d-flex align-items-center justify-content-center <?= $showAvatarImage ? 'd-none' : '' ?>"
                                    style="width: 96px; height: 96px; font-size: 2rem; background: linear-gradient(135deg, #385163ff, #001b2eff);">
                                    <?= htmlspecialchars($initial, ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </div>
                            <div class="text-center text-md-start">
                                <h2 id="profileDisplayUsername" class="h4 mb-1"><?= $targetUser ? htmlspecialchars((string) $targetUser->username, ENT_QUOTES, 'UTF-8') : 'Guest' ?></h2>
                                <div id="profileDisplayEmail" class="text-secondary mb-2"><?= $targetUser ? htmlspecialchars((string) $targetUser->email, ENT_QUOTES, 'UTF-8') : 'Please log in to manage your profile.' ?></div>
                                <?php if ($targetUser): ?>
                                    <span id="profileRoleBadge" class="badge text-bg-primary me-2">Role: <?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8') ?></span>
                                    <span id="profileRatingBadge" class="badge text-bg-light">Rating: <?= (int) $targetUser->rating ?></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" height="36px" viewBox="0 -960 960 960" width="36px" class="star s1 <?= !$isFavourite ? 'x' : '' ?>" fill="currentColor">
                                        <path d="m306.22-701.88 115.45-149.79q11.33-14.66 26.55-21.83 15.23-7.17 31.84-7.17t31.77 7.17q15.17 7.17 26.5 21.83l115.45 149.79L829-643q24 8 37.67 27.91 13.66 19.9 13.66 43.98 0 11.11-3.19 22.2-3.18 11.08-10.47 21.24L754-367l4 170q.33 32.33-21.67 54.67-22 22.33-52.11 22.33-1.89 0-19.89-3L480-174.33l-184.19 51.28q-5.14 2.05-10.82 2.55-5.67.5-10.4.5-30.26 0-51.92-22.46Q201-164.92 202-197.67l4-170.5L93.67-528.33q-7.29-10.22-10.48-21.37Q80-560.85 80-572q0-23.67 13.45-43.24Q106.89-634.81 131-643l175.22-58.88Z" />
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" height="36px" viewBox="0 -960 960 960" width="36px" class="star s2 <?= $isFavourite ? 'x' : '' ?>" fill="currentColor">
                                        <path d="m306.22-701.88 115.45-149.79q11.33-14.66 26.55-21.83 15.23-7.17 31.84-7.17t31.77 7.17q15.17 7.17 26.5 21.83l115.45 149.79L829-643q24 8 37.67 27.91 13.66 19.9 13.66 43.98 0 11.11-3.19 22.2-3.18 11.08-10.47 21.24L754-367l4 170q.33 32.33-21.67 54.67-22 22.33-52.11 22.33-1.89 0-19.89-3L480-174.33l-184.19 51.28q-5.14 2.05-10.82 2.55-5.67.5-10.4.5-30.26 0-51.92-22.46Q201-164.92 202-197.67l4-170.5L93.67-528.33q-7.29-10.22-10.48-21.37Q80-560.85 80-572q0-23.67 13.45-43.24Q106.89-634.81 131-643l175.22-58.88Zm40.45 57.55-204 68 130.66 189-4.66 201.66L480-244l211.33 59.33-4.66-202.66 130.66-187-204-70L480-818 346.67-644.33Zm133.33 143Z" />
                                    </svg>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>

                <div id="profileMessage" class="alert d-none" role="alert"></div>

                <?php if (!$targetUser): ?>
                    <div class="alert alert-warning" role="alert">
                        You need to log in to access and update your profile.
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <div class="col-12 col-lg-8">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body p-4">
                                    <h3 class="h5 mb-3">Profile Details</h3>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="bio" class="form-label">Bio</label>
                                            <p
                                                class=""
                                                id="bio"
                                                name="bio"
                                                rows="4"
                                                placeholder="Tell people about yourself"><?= htmlspecialchars((string) ($targetUser->bio ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-body p-4">
                                    <h3 class="h5 mb-3">Account Info</h3>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item px-0 d-flex justify-content-between">
                                            <span class="text-secondary">User ID</span>
                                            <input type="text" value="<?= (int) $currentUser->id ?>" hidden name="" id="user_id">
                                            <strong id="accountInfoId"><?= (int) $targetUser->id ?></strong>
                                        </li>
                                        <li class=" list-group-item px-0 d-flex justify-content-between">
                                            <span class="text-secondary">Role</span>
                                            <strong id="accountInfoRole"><?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8') ?></strong>
                                        </li>
                                        <li class="list-group-item px-0 d-flex justify-content-between">
                                            <span class="text-secondary">Rating</span>
                                            <strong id="accountInfoRating"><?= (int) $targetUser->rating ?></strong>
                                        </li>
                                        <li class="list-group-item px-0 d-flex justify-content-between">
                                            <span class="text-secondary">Created</span>
                                            <strong id="accountInfoCreatedAt"><?= htmlspecialchars((string) $targetUser->created_at, ENT_QUOTES, 'UTF-8') ?></strong>
                                        </li>
                                        <li class="list-group-item px-0 d-flex justify-content-between">
                                            <span class="text-secondary">Updated</span>
                                            <strong id="accountInfoUpdatedAt"><?= htmlspecialchars((string) $targetUser->updated_at, ENT_QUOTES, 'UTF-8') ?></strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <script src="/public/assets/js/auth.js"></script>
    <script src="/public/assets/js/profile.js"></script>
    <script src="/public/assets/js/index.js"></script>
    <script src="/public/assets/js/favorite.js"></script>
    <script src="/public/assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
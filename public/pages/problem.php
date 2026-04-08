<!doctype html>
<html lang="en">
<?php
$pageStylesheets = ['/public/assets/css/problem.css'];
require_once __DIR__ . '/../components/head.php';
require_once __DIR__ . '/../../backend/autoloader.php';

$problemId = isset($_GET['problem_id']) ? (int) $_GET['problem_id'] : 0;

if ($problemId <= 0) {
    $errorMessage = 'Invalid problem ID';
    $problem = null;
    $testCases = [];
    $languages = [];
} else {
    $ProblemsRepository = new ProblemsRepository();
    $TestCasesRepository = new TestCasesRepository();
    $LanguagesRepository = new LanguagesRepository();

    $problem = $ProblemsRepository->findById($problemId);

    if (!$problem) {
        $errorMessage = 'Problem not found';
        $testCases = [];
        $languages = [];
    } else {
        $errorMessage = null;
        $testCases = $TestCasesRepository->findSampleByProblemId($problemId);
        $languages = $LanguagesRepository->findEnabled();
    }
}
?>

<body>
    <header>
        <?php
        require_once __DIR__ . '/../components/nav.php';
        require_once __DIR__ . '/../components/search-bar.php';
        ?>
    </header>

    <main class="container mt-4">
        <?php if ($errorMessage): ?>
            <div class="container">
                <div class="alert alert-danger" role="alert">
                    <h4>Error</h4>
                    <p><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
                    <a href="/public/pages/problemset.php" class="btn btn-primary">← Back to Problemset</a>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-0">
                <div class="col-12 col-lg-10 border-end p-4 problem-pane">
                    <div class="mb-4">
                        <a href="/public/pages/problemset.php" class="text-decoration-none text-secondary">← Back to Problemset</a>
                    </div>

                    <div class="mb-4">
                        <h1 class="h3 mb-2">
                            <span class="text-info"><?= (int) $problem->id ?> - </span><?= htmlspecialchars((string) $problem->title, ENT_QUOTES, 'UTF-8') ?>
                        </h1>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge text-bg-info"><?= htmlspecialchars((string) $problem->category, ENT_QUOTES, 'UTF-8') ?></span>

                            <?php
                            $difficulty = htmlspecialchars((string) $problem->difficulty, ENT_QUOTES, 'UTF-8');
                            $difficultyClass = match ($difficulty) {
                                'Easy' => 'text-bg-success',
                                'Medium' => 'text-bg-warning',
                                'Hard' => 'text-bg-danger',
                                default => 'text-bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $difficultyClass ?>"><?= $difficulty ?></span>

                            <span class="badge text-bg-secondary">
                                AC: <?= (int) $problem->success_count ?> / <?= (int) $problem->total_attempts ?>
                            </span>

                            <span class="badge text-bg-secondary">
                                Acceptance: <?= (int) $problem->acceptance_rate ?>%
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="mb-3">Description</h5>
                        <div class="card border-0 bg-light p-3">
                            <p><?= nl2br(htmlspecialchars((string) $problem->description, ENT_QUOTES, 'UTF-8')) ?></p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="mb-3">Constraints</h5>
                        <div class="card border-0 bg-light p-3">
                            <ul class="mb-0">
                                <li>Time Limit: <strong><?= (int) $problem->time_limit_ms ?>ms</strong></li>
                                <li>Memory Limit: <strong><?= (int) $problem->memory_limit_mb ?>MB</strong></li>
                            </ul>
                        </div>
                    </div>

                    <?php if (count($testCases) > 0): ?>
                        <div class="mb-4">
                            <h5 class="mb-3">Sample Test Cases</h5>

                            <?php foreach ($testCases as $index => $testCase): ?>
                                <div class="card mb-3 border">
                                    <div class="card-header bg-secondary text-white py-2">
                                        <strong>Example <?= $index + 1 ?></strong>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label fw-bold mb-0">Input</label>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-secondary btn-sm problem-copy-btn copy-testcase-btn"
                                                    data-copy-target="testcase-input-<?= (int) $testCase->id ?>">
                                                    Copy
                                                </button>
                                            </div>
                                            <pre class="bg-light p-3 rounded problem-io-code"><code id="testcase-input-<?= (int) $testCase->id ?>"><?= htmlspecialchars((string) $testCase->input, ENT_QUOTES, 'UTF-8') ?></code></pre>
                                        </div>
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label fw-bold mb-0">Output</label>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-secondary btn-sm problem-copy-btn copy-testcase-btn"
                                                    data-copy-target="testcase-output-<?= (int) $testCase->id ?>">
                                                    Copy
                                                </button>
                                            </div>
                                            <pre class="bg-light p-3 rounded problem-io-code"><code id="testcase-output-<?= (int) $testCase->id ?>"><?= htmlspecialchars((string) $testCase->expected_output, ENT_QUOTES, 'UTF-8') ?></code></pre>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-4">
                            No sample test cases available for this problem.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-lg-2 p-4 problem-pane">
                    <div class="card shadow-sm border-0 problem-side-card">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0">Problem Actions</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($isLoggedIn): ?>
                                <button
                                    type="button"
                                    class="btn btn-success w-100"
                                    data-bs-toggle="modal"
                                    data-bs-target="#submitSolutionModal">
                                    Submit Solution
                                </button>
                            <?php else: ?>
                                <div class="alert alert-warning" role="alert">
                                    <h6 class="mb-2">Please Log In</h6>
                                    <p class="mb-0">You need to be logged in to submit a solution. Please log in or sign up to continue.</p>
                                    <button class="btn btn-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#loginModal">
                                        Log In
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#signupModal">
                                        Sign Up
                                    </button>
                                </div>
                            <?php endif; ?>

                            <div id="submissionMessage" class="alert d-none mt-3" role="alert"></div>

                            <div class="mt-4 pt-4 border-top">
                                <h6 class="mb-3">Recent Submissions</h6>
                                <div id="recentSubmissions" class="text-muted" data-problem-id="<?= (int) $problem->id ?>">
                                    <small>Loading submissions...</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($isLoggedIn): ?>
                <div class="modal fade" id="submitSolutionModal" tabindex="-1" aria-labelledby="submitSolutionModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="submitSolutionModalLabel">Submit Solution</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="submissionForm" method="post" action="/backend/problemset/submit.php">
                                    <input type="hidden" name="problem_id" value="<?= (int) $problem->id ?>">

                                    <div class="mb-3">
                                        <label for="languageSelect" class="form-label fw-bold">Programming Language</label>
                                        <select id="languageSelect" name="language_id" class="form-select" required>
                                            <option value="">-- Select Language --</option>
                                            <?php foreach ($languages as $language): ?>
                                                <option
                                                    value="<?= (int) $language->id ?>"
                                                    data-extension="<?= htmlspecialchars((string) ($language->file_extension ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                                    <?= htmlspecialchars((string) $language->name, ENT_QUOTES, 'UTF-8') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="codeInput" class="form-label fw-bold">Code</label>
                                        <textarea
                                            id="codeInput"
                                            name="code"
                                            class="form-control problem-code-textarea"
                                            rows="13"
                                            placeholder="// Write your solution here..."></textarea>
                                        <small class="text-muted d-block mt-2">Max size: 256KB</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="solutionFile" class="form-label fw-bold">Attach Solution File (optional)</label>
                                        <input
                                            type="file"
                                            id="solutionFile"
                                            name="solution_file"
                                            class="form-control"
                                            accept=".cpp,.cc,.c,.h,.hpp,.py,.java,.js,.ts,.txt">
                                        <small class="text-muted d-block mt-2">If the code textarea is empty, attached file content will be used.</small>
                                    </div>

                                    <div id="submissionModalMessage" class="alert d-none mt-3" role="alert"></div>

                                    <button type="submit" class="btn btn-success w-100">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <script src="/public/assets/js/auth.js"></script>
    <script src="/public/assets/js/index.js"></script>
    <script src="/public/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/public/assets/js/problem.js"></script>
</body>

</html>
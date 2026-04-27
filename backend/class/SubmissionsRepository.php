<?php

class SubmissionsRepository extends Repository
{
    const tableName = 'submissions';
    private const PENDING_VERDICT_ID = 8;

    public function __construct()
    {
        return parent::__construct(self::tableName);
    }

    public function createPending(int $userId, int $problemId, int $languageId): int
    {
        $insert = $this->db->prepare(
            "INSERT INTO {$this->tableName} (user_id, problem_id, language_id, verdict_id) VALUES (?, ?, ?, ?)"
        );
        $insert->execute([$userId, $problemId, $languageId, self::PENDING_VERDICT_ID]);

        return (int) $this->db->lastInsertId();
    }

    public function findPending(int $limit = 20): array
    {
        $safeLimit = max(1, min($limit, 100));
        $query = "
            SELECT
                s.id,
                s.user_id,
                s.problem_id,
                s.language_id,
                s.submitted_at,
                p.title AS problem_title,
                p.time_limit_ms,
                p.memory_limit_mb,
                l.name AS language_name,
                l.file_extension,
                l.compiler_command
            FROM {$this->tableName} s
            INNER JOIN verdict_status v ON v.id = s.verdict_id AND v.verdict = 'PENDING'
            INNER JOIN problems p ON p.id = s.problem_id
            INNER JOIN languages l ON l.id = s.language_id
            ORDER BY s.submitted_at ASC, s.id ASC
            LIMIT {$safeLimit}
        ";

        $response = $this->db->query($query);
        return $response->fetchAll(PDO::FETCH_OBJ);
    }

    public function updateJudgingResult(
        int $submissionId,
        int $verdictId,
        ?int $executionTimeMs,
        ?int $memoryUsedMb,
        int $passedTests,
        int $totalTests,
        ?string $errorMessage
    ): void {
        $this->update($submissionId, [
            'verdict_id' => $verdictId,
            'execution_time_ms' => $executionTimeMs,
            'memory_used_mb' => $memoryUsedMb,
            'passed_tests' => $passedTests,
            'total_tests' => $totalTests,
            'error_message' => $errorMessage,
        ]);
    }

    public function findRecentByUserAndProblem(int $userId, int $problemId, int $limit = 5): array
    {
        $safeLimit = max(1, min($limit, 20));
        $query = "
            SELECT
                s.id,
                s.submitted_at,
                l.name AS language_name,
                vs.verdict,
                vs.display_name,
                vs.color_code,
                s.execution_time_ms,
                s.memory_used_mb
            FROM {$this->tableName} s
            INNER JOIN languages l ON l.id = s.language_id
            LEFT JOIN verdict_status vs ON vs.id = s.verdict_id
            WHERE s.user_id = ? AND s.problem_id = ?
            ORDER BY s.submitted_at DESC, s.id DESC
            LIMIT {$safeLimit}
        ";

        $response = $this->db->prepare($query);
        $response->execute([$userId, $problemId]);
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findAll()
    {
        $query = "
        select
        s.id,
        ps.category,
        s.submitted_at,
        l.name AS language_name,
        vs.verdict,
        vs.display_name,
        vs.color_code,
        s.execution_time_ms,
        s.memory_used_mb,
        us.username,
        ps.difficulty,
        ps.title,
        ps.id as pid
        from {$this->tableName} s,
        verdict_status vs, 
        problems ps,
        users us,
        languages l
        where 
        us.id = user_id
        and l.id=s.language_id
        and vs.id=verdict_id 
        and problem_id=ps.id 
        order by s.submitted_at Desc
        limit 20
        ";

        $response = $this->db->query($query);
        $elements = $response->fetchAll(PDO::FETCH_OBJ);
        return $elements;
    }

    public function findByUserId($userId)
    {
        $query = "
        select 
        s.id,
        ps.category,
        s.submitted_at,
        l.name AS language_name,
        vs.verdict,
        vs.display_name,
        vs.color_code,
        s.execution_time_ms,
        s.memory_used_mb,
        us.username,
        ps.difficulty,
        ps.title,
        ps.id as pid
        from {$this->tableName} s,
        verdict_status vs,
        problems ps,
        users us,
        languages l
        where us.id = user_id
        and l.id=s.language_id
        and vs.id=verdict_id
        and problem_id=ps.id
        and user_id = ?
        order by s.submitted_at Desc
        ";
        $response = $this->db->prepare($query);
        $response->execute([$userId]);
        return $response->fetchAll(PDO::FETCH_OBJ);
    }
    public function findAllFavoritesById($userId)
    {
        $query = "
        select 
        s.id,
        ps.category,
        s.submitted_at,
        l.name AS language_name,
        vs.verdict,
        vs.display_name,
        vs.color_code,
        s.execution_time_ms,
        s.memory_used_mb,
        us.username,
        ps.difficulty,
        ps.title,
        ps.id as pid
        from {$this->tableName} s,
        verdict_status vs,
        problems ps,
        users us,
        languages l,
        user_favorites fav
        where us.id = fav.favorite_user_id
        and fav.user_id=?
        and l.id=s.language_id
        and vs.id=verdict_id
        and problem_id=ps.id
        and s.user_id =fav.favorite_user_id 
        order by s.submitted_at Desc
        ";
        $response = $this->db->prepare($query);
        $response->execute([$userId]);
        return $response->fetchAll(PDO::FETCH_OBJ);
    }
}

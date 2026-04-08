<?php

class SubmissionsRepository extends Repository
{
    const tableName = 'submissions';

    public function __construct()
    {
        return parent::__construct(self::tableName);
    }

    public function createPending(int $userId, int $problemId, int $languageId, string $code): void
    {
        $this->create([
            'user_id' => $userId,
            'problem_id' => $problemId,
            'language_id' => $languageId,
            'code' => $code,
            'verdict_id' => 8,
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
}

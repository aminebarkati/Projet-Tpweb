<?php

class ProblemsRepository extends Repository
{
    const tableName = 'problems';
    public function __construct()
    {
        return parent::__construct(self::tableName);
    }
    public function findAll()
    {
        $response = $this->db->query("select * from {$this->tableName} order by id Desc limit 20");
        $elements = $response->fetchAll(PDO::FETCH_OBJ);
        return $elements;
    }

    public function updateJudgingStats(int $problemId, bool $accepted): void
    {
        $problem = $this->findById($problemId);
        if (!$problem) {
            return;
        }

        $successCount = (int) $problem->success_count + ($accepted ? 1 : 0);
        $totalAttempts = (int) $problem->total_attempts + 1;
        $acceptanceRate = $totalAttempts > 0 ? round(($successCount / $totalAttempts) * 100, 2) : 0.0;

        $this->update($problemId, [
            'success_count' => $successCount,
            'total_attempts' => $totalAttempts,
            'acceptance_rate' => $acceptanceRate,
        ]);
    }
}

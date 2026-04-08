<?php

class TestCasesRepository extends Repository
{
    const tableName = 'test_cases';

    public function __construct()
    {
        return parent::__construct(self::tableName);
    }

    public function findSampleByProblemId(int $problemId): array
    {
        $response = $this->db->prepare("SELECT * FROM {$this->tableName} WHERE problem_id = ? AND is_sample = 1 ORDER BY order_index ASC");
        $response->execute([$problemId]);
        return $response->fetchAll(PDO::FETCH_OBJ);
    }
}

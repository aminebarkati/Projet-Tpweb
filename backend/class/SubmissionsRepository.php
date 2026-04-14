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
    public function findAll()
    {
        $query = "
        select
        s.id,
        s.submitted_at,
        l.name AS language_name,
        vs.verdict,
        vs.display_name,
        vs.color_code,
        s.execution_time_ms,
        s.memory_used_mb,
        us.username,
        ps.difficulty,
        ps.title
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
        s.submitted_at,
        l.name AS language_name,
        vs.verdict,
        vs.display_name,
        vs.color_code,
        s.execution_time_ms,
        s.memory_used_mb,
        us.username,
        ps.difficulty,
        ps.title
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
        s.submitted_at,
        l.name AS language_name,
        vs.verdict,
        vs.display_name,
        vs.color_code,
        s.execution_time_ms,
        s.memory_used_mb,
        us.username,
        ps.difficulty,
        ps.title
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

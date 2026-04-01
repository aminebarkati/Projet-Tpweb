<?php

class UserRepository extends Repository
{
    const tableName = 'users';
    public function __construct()
    {
        return parent::__construct(self::tableName);
    }
    public function findAllUsers()
    {
        $response = $this->db->query("select username from {$this->tableName}");
        $elements = $response->fetchAll(PDO::FETCH_OBJ);
        return $elements;
    }

    public function findByUsername($username)
    {
        $response = $this->db->prepare("select * from {$this->tableName} where username = ?");
        $response->execute([$username]);
        return $response->fetch(PDO::FETCH_OBJ);
    }
    public function findByEmail($email)
    {
        $response = $this->db->prepare('SELECT id FROM users WHERE email = ?');
        $response->execute([$email]);
        return $response->fetch(PDO::FETCH_OBJ);
    }

    public function deductPointsById(int $userId, int $points): void
    {
        $response = $this->db->prepare('UPDATE users SET rating = GREATEST(0, rating + ?) WHERE id = ?');
        $response->execute([$points, $userId]);
    }
}

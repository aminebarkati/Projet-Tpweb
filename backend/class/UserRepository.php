<?php

class UserRepository extends Repository
{
    const tableName = 'users';
    public function __construct()
    {
        return parent::__construct(self::tableName);
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
    public function findFavouritesById($id)
    {
        $query = "
        select us.*
        from {$this->tableName} us,
        user_favorites fav 
        where fav.user_id = ?
        and us.id=fav.favorite_user_id
        ";
        $response = $this->db->prepare($query);
        $response->execute([$id]);
        return $response->fetchAll(PDO::FETCH_OBJ);
    }
}

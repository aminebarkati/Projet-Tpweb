<?php

class FavoriteRepository extends Repository
{
    const tableName = 'user_favorites';

    public function __construct()
    {
        return parent::__construct(self::tableName);
    }
    public function checkFavoriteById($id, $favid)
    {
        $query = "
        select  *
        from {$this->tableName} fav 
        where 
        user_id=?
        and fav.favorite_user_id = ?
        ";
        $response = $this->db->prepare($query);
        $response->execute([$id, $favid]);
        return $response->fetchAll(PDO::FETCH_OBJ);
    }
    public function deleteByUserId($user_id, $favorite_user_id)
    {
        $response = $this->db->prepare(query: "delete from {$this->tableName} where user_id = ? and favorite_user_id=?");
        $response->execute([$user_id, $favorite_user_id]);
    }
}

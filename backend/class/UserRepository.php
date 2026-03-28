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
}

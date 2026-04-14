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
}

<?php

class LanguagesRepository extends Repository
{
    const tableName = 'languages';

    public function __construct()
    {
        return parent::__construct(self::tableName);
    }

    public function findEnabled(): array
    {
        $response = $this->db->query("SELECT * FROM {$this->tableName} WHERE is_enabled = 1 ORDER BY id ASC");
        return $response->fetchAll(PDO::FETCH_OBJ);
    }
}

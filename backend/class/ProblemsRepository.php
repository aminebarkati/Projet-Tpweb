<?php

class ProblemsRepository extends Repository
{
    const tableName = 'problems';
    public function __construct()
    {
        return parent::__construct(self::tableName);
    }
}

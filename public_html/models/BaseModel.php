<?php

class BaseModel
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = getPDO();
    }
}

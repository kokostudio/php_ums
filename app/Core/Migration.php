<?php

namespace App\Core;

abstract class Migration
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    abstract public function up(): void;
    abstract public function down(): void;
}

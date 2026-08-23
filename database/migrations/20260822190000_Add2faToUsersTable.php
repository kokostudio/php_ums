<?php

use App\Core\Migration;

class Add2faToUsersTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            ALTER TABLE users 
            ADD COLUMN two_factor_secret VARCHAR(255) NULL AFTER password,
            ADD COLUMN two_factor_enabled TINYINT(1) DEFAULT 0 AFTER two_factor_secret;
        ");
        
        echo "Added 2FA columns to users table.\n";
    }

    public function down(): void
    {
        $this->db->exec("
            ALTER TABLE users 
            DROP COLUMN two_factor_secret,
            DROP COLUMN two_factor_enabled;
        ");
        
        echo "Dropped 2FA columns from users table.\n";
    }
}

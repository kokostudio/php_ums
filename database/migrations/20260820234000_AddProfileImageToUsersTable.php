<?php

use App\Core\Migration;

class AddProfileImageToUsersTable extends Migration
{
    public function up(): void
    {
        $this->db->exec("
            ALTER TABLE users 
            ADD COLUMN profile_image VARCHAR(255) NULL AFTER email;
        ");
    }

    public function down(): void
    {
        $this->db->exec("
            ALTER TABLE users 
            DROP COLUMN profile_image;
        ");
    }
}

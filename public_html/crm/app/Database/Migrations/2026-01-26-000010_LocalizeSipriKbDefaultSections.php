<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class LocalizeSipriKbDefaultSections extends Migration {

    public function up() {
        // This migration is intentionally a no-op for data; default KB sections are created on tenant creation.
        // Kept for forward-compatibility if we later need to add language keys or seed missing defaults.
    }

    public function down() {
        // no-op
    }
}



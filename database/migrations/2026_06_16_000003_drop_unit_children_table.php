<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('unit_children');
    }

    public function down(): void
    {
        // Not recreating — table was replaced by parent_id approach
    }
};

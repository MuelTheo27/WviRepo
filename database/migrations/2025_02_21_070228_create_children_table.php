<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->string('child_code')->unique();
            $table->unsignedBigInteger("sponsor_id");
<<<<<<< HEAD:database/migrations/2025_02_18_075453_create_childs_table.php
            $table->foreign('sponsor_id')->on("sponsors")->references("id")->onDelete("cascade");
=======
            $table->foreign('sponsor_id')->on("sponsors")->references("sponsor_id")->onDelete("cascade");
            $table->unsignedBigInteger("content_id");
            $table->foreign('content_id')->on("content")->references("content_id")->onDelete("cascade");
>>>>>>> 316d8f059e4cc55c28dbeb66651b5503be65cc91:database/migrations/2025_02_21_070228_create_children_table.php
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('childs');
    }
};

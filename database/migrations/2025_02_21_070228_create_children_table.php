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
            // $table->id();
            // $table->string('child_code')->unique();
            // $table->unsignedBigInteger("sponsor_id");
            // $table->foreign('sponsor_id')->on("sponsors")->references("id")->onDelete("cascade");
            // $table->foreign('sponsor_id')->on("sponsors")->references("sponsor_id")->onDelete("cascade");
            // $table->unsignedBigInteger("content_id");
            // $table->foreign('content_id')->on("content")->references("content_id")->onDelete("cascade");
            // $table->timestamps();

            $table->id();
            $table->string('child_code')->unique();
            $table->foreignId("sponsor_id")->constrained("sponsors")->onDelete("cascade");
            $table->unsignedBigInteger("content_id"); // Tambahkan kolom saja dulu
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

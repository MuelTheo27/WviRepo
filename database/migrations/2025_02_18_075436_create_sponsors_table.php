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
        Schema::create('sponsors', function (Blueprint $table) {
        $table->id('sponsor_id');
        $table->unsignedBigInteger("sponsor_category_id");
        $table->string('sponsor_name');
        $table->foreign('sponsor_category_id')->on("sponsor_categories")->references("sponsor_category_id");
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsors');
    }
};

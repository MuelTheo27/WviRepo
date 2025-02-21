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
        $table->id('child_id');
        $table->unsignedBigInteger("sponsor_id");
        $table->string('child_code')->unique();
        $table->foreign('sponsor_id')->on("sponsors")->references("sponsor_id")->onDelete("cascade");

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

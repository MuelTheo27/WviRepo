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
        Schema::create('content', function (Blueprint $table) {
        $table->id();       
        $table->string("child_idn");
        $table->text('content_url');
        $table->year("fiscal_year");
        $table->timestamps();
        $table->softDeletesDatetime();
        $table->foreign("child_idn")->references("child_idn")->on("children")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};

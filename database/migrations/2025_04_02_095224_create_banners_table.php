<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('image'); // Image field
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('banners');
    }
};

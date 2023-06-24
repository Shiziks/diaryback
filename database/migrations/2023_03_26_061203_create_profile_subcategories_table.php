<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_subcategories', function (Blueprint $table) {
            $table->id();
            $table->string('subcategory_name');
            $table->bigInteger('profilecategory_id')->unsigned()->index();
            $table->foreign('profilecategory_id')->references('id')->on('profile_categories');
            $table->boolean('admin_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile_subcategories');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_attachments', function (Blueprint $table) {
            if (config('filelibrary.use_uuid')) {
                $table->uuid('id')->primary();
                $table->foreignUuid('file_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
                $table->uuid('model_id');
            } else {
                $table->id();
                $table->foreignId('file_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedBigInteger('model_id');
            }

            $table->string('model_name');
            $table->string('category')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_attachments');
    }
}

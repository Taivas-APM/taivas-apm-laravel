<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_requests', function (Blueprint $table) {
            $table->id();
            $table->string('url')->index('url');
            $table->dateTime('started_at')->index('started_at');
            $table->dateTime('stopped_at');
            $table->integer('request_duration')->index('request_duration');
            $table->integer('db_duration')->index('db_duration');
            $table->integer('db_count')->index('db_count');
            $table->mediumText('db_queries');
            $table->bigInteger('memory_peak')->index('memory_peak');
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
        Schema::dropIfExists('tracking_requests');
    }
}

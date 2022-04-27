<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issueStampSheet', function (Blueprint $table) {
            $table->string('transactionId')->primary();
            $table->string('service');
            $table->string('method');
            $table->string('action');
            $table->string('userId');
            $table->json('args');
            $table->json('tasks');
            $table->timestamp('timestamp');
        });
        Schema::table('issueStampSheet', function (Blueprint $table) {
            $table->index(['userId', 'timestamp']);
            $table->index(['service', 'method']);
            $table->index(['action']);
        });
        Schema::create('issueStampSheetJoinTask', function (Blueprint $table) {
            $table->string('transactionId')->primary();
            $table->string('taskId');
            $table->string('action');
        });
        Schema::create('executeStampSheet', function (Blueprint $table) {
            $table->string('transactionId')->primary();
            $table->string('service');
            $table->string('method');
            $table->string('userId');
            $table->string('action');
            $table->json('args');
            $table->json('result');
            $table->timestamp('timestamp');
        });
        Schema::table('executeStampSheet', function (Blueprint $table) {
            $table->index(['userId', 'timestamp']);
            $table->index(['service', 'method']);
            $table->index(['action']);
        });
        Schema::create('executeStampTask', function (Blueprint $table) {
            $table->string('taskId')->primary();
            $table->string('transactionId');
            $table->string('service');
            $table->string('method');
            $table->string('userId');
            $table->string('action');
            $table->json('args');
            $table->json('result');
            $table->timestamp('timestamp');
        });
        Schema::table('executeStampTask', function (Blueprint $table) {
            $table->index(['transactionId', 'timestamp']);
            $table->index(['userId', 'timestamp']);
            $table->index(['service', 'method']);
            $table->index(['action']);
        });
        Schema::create('accessLog', function (Blueprint $table) {
            $table->string('requestId')->primary();
            $table->string('service');
            $table->string('method');
            $table->string('userId');
            $table->json('request');
            $table->json('result');
            $table->timestamp('timestamp');
        });
        Schema::create('player', function (Blueprint $table) {
            $table->string('userId')->primary();
            $table->double('purchasedAmount')->default(0);
            $table->integer('fetchedBeginAt')->nullable();
            $table->integer('fetchedEndAt')->nullable();
            $table->timestamp('lastAccessAt');
        });
        Schema::create('timeline', function (Blueprint $table) {
            $table->string('transactionId')->primary();
            $table->string('type');
            $table->string('userId');
            $table->string('action');
            $table->json('args')->nullable();
            $table->string('rewardAction')->nullable();
            $table->json('rewardArgs')->nullable();
            $table->timestamp('timestamp');
        });
        Schema::table('timeline', function (Blueprint $table) {
            $table->index(['userId', 'timestamp', 'action']);
        });
        Schema::create('metrics', function (Blueprint $table) {
            $table->string('metricsId')->primary();
            $table->string('key');
            $table->double('value');
            $table->timestamp('timestamp');
        });
        Schema::table('metrics', function (Blueprint $table) {
            $table->index(['key', 'timestamp']);
        });
        Schema::create('gcp', function (Blueprint $table) {
            $table->string('datasetName')->primary();
            $table->integer('beginAt');
            $table->integer('endAt');
            $table->text('credentials');
        });
        Schema::create('grn', function (Blueprint $table) {
            $table->string('grn')->primary();
            $table->string('parent');
            $table->string('category');
            $table->string('key');
        });
        Schema::table('grn', function (Blueprint $table) {
            $table->index(['parent', 'category']);
        });
        Schema::create('grnKey', function (Blueprint $table) {
            $table->string('keyId')->primary();
            $table->string('grn');
            $table->string('category');
            $table->string('requestId');
        });
        Schema::table('grnKey', function (Blueprint $table) {
            $table->index(['grn', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('issueStampSheet');
        Schema::dropIfExists('issueStampSheetJoinTask');
        Schema::dropIfExists('executeStampSheet');
        Schema::dropIfExists('executeStampTask');
        Schema::dropIfExists('accessLog');
        Schema::dropIfExists('player');
        Schema::dropIfExists('timeline');
        Schema::dropIfExists('metrics');
        Schema::dropIfExists('gcp');
        Schema::dropIfExists('grn');
        Schema::dropIfExists('grnKey');
    }
}

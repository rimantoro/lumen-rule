<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RuleengineRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('ruleengine_rules')) {
            Schema::create('ruleengine_rules', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('code');
                $table->string('title');
                $table->string('rules');
                $table->text('group_logic')->nullable();
                $table->integer('active');

                $table->unique('code');
                
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('ruleengine_rules')) {
            Schema::drop('ruleengine_rules');
        }
    }
}

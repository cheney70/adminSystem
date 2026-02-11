<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToPivotTables extends Migration
{
    public function up()
    {
        Schema::table('role_admin', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('permission_role', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('role_admin', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('permission_role', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Transaction extends Migration
{
    public function up()
    {
        //---- Transaction code as account code
        Schema::create('user', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // <- add this
            $table->string('username')->unique()->primary();
            $table->string('password');
            $table->enum('role', ['editor', 'viewer']);
        });


        //---- Transaction code as account code
        Schema::create('transaction_code', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // <- add this
            $table->string('code')->unique()->primary();
            $table->string('keterangan');
        });

        //---- Tabel PKB
        Schema::create('transaction_pkb', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // <- add this
            $table->id()->autoIncrement();
            $table->string('wo');
            $table->string('license_plate');
            $table->string('customer');
            $table->dateTime('invoice_date');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });

        //---- Tabel Kredit
        Schema::create('transaction_kredit', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // <- add this
            $table->id()->autoIncrement();
            $table->integer('jasa');
            $table->integer('parts');
            $table->integer('bahan');
            $table->integer('OPL');
            $table->integer('OPB');
            $table->string('kode_jasa');
            $table->string('kode_parts');
            $table->string('kode_bahan');
            $table->string('kode_opl');
            $table->string('kode_opb');
            $table->string('wo');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });

        // Table Debit
        Schema::create('transaction_debit', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // <- add this
            $table->id()->autoIncrement();
            $table->integer('jasa');
            $table->integer('parts');
            $table->integer('bahan');
            $table->integer('OPL');
            $table->integer('OPB');
            $table->string('kode_jasa');
            $table->string('kode_parts');
            $table->string('kode_bahan');
            $table->string('kode_opl');
            $table->string('kode_opb');
            $table->integer('ppn');
            $table->string('kode_ppn');
            $table->string('total');
            $table->string('wo');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }
    public function down()
    {
        Schema::drop('transaction_code');
        Schema::drop('transaction_pkb');
        Schema::drop('transaction_kredit');
        Schema::drop('transaction_debit');
    }
}

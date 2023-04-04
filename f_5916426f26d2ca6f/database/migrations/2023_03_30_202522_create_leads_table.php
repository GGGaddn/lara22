<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string("name")->comment("Название сделки");
            $table->integer("amocrm_id")->comment("Идентификатор сделки в AmoCRM");
            $table->integer("price")->nullable()->comment("Сумма сделки");
            $table->boolean("is_deleted")->nullable()->comment("Сделка удалена?");
            $table->timestamp("closed_at")->nullable()->comment("Дата закрытия сделки");
            $table->string("contact_name")->nullable()->comment("Контакт: имя");
            $table->string("contact_phone")->nullable()->comment("Контакт: телефон");
            $table->string("contact_email")->nullable()->comment("Контакт: e-mail");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

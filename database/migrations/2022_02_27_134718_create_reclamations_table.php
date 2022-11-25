<?php

use App\Models\CategorieReclamation;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReclamationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reclamations', function (Blueprint $table) {
            $table->id();
            $table->string('objet');
            $table->longText('description');
            $table->foreignIdFor(CategorieReclamation::class)->constrained();
            $table->foreignIdFor(User::class)->nullable();
            $table->boolean('resolue')->default(false);
            $table->text('resolutions')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email');
            $table->string('identifiant');
            $table->foreignIdFor(State::class)->constrained();
            $table->boolean('archivee')->default(false);
            $table->string('uid');
            $table->integer('numero');
            $table->string('priorite')->default('Moyenne');
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
        Schema::dropIfExists('reclamations');
    }
}

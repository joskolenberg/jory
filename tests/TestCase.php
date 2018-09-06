<?php

namespace JosKolenberg\Jory\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Database\Schema\Blueprint;
use JosKolenberg\Jory\Tests\Models\Band;
use JosKolenberg\Jory\Tests\Models\Person;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp()
    {
        parent::setUp();

        $this->setUpDatabase($this->app);

        Person::seed();
        Band::seed();
    }

    protected function setUpDatabase(Application $app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('people', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        $app['db']->connection()->getSchemaBuilder()->create('bands', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        $app['db']->connection()->getSchemaBuilder()->create('band_person', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('person_id');
            $table->foreign('person_id')
                ->references('id')->on('people')
                ->onDelete('restrict');
            $table->unsignedInteger('band_id');
            $table->foreign('band_id')
                ->references('id')->on('bands')
                ->onDelete('restrict');
        });

    }

}

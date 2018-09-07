<?php

namespace JosKolenberg\Jory\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use JosKolenberg\Jory\Tests\Models\Album;
use JosKolenberg\Jory\Tests\Models\Band;
use JosKolenberg\Jory\Tests\Models\Person;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp()
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
        $this->seedDatabase();

    }

    protected function setUpDatabase(Application $app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('people', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
        });

        $app['db']->connection()->getSchemaBuilder()->create('bands', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        $app['db']->connection()->getSchemaBuilder()->create('band_members', function (Blueprint $table) {
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

        $app['db']->connection()->getSchemaBuilder()->create('albums', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('band_id');
            $table->foreign('band_id')
                ->references('id')->on('bands')
                ->onDelete('restrict');
            $table->date('release_date');
        });

        $app['db']->connection()->getSchemaBuilder()->create('songs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('album_id');
            $table->foreign('album_id')
                ->references('id')->on('album')
                ->onDelete('restrict');
        });

    }

    private function seedDatabase()
    {
        // Seed Persons
        foreach ([
                     1 => ['first_name' => 'Mick', 'last_name' => 'Jagger', 'date_of_birth' => '1943-07-26'],
                     2 => ['first_name' => 'Keith', 'last_name' => 'Richards', 'date_of_birth' => '1943-12-18'],
                     3 => ['first_name' => 'Ronnie', 'last_name' => 'Wood', 'date_of_birth' => '1947-06-01'],
                     4 => ['first_name' => 'Charlie', 'last_name' => 'Watts', 'date_of_birth' => '1941-06-02'],
                     5 => ['first_name' => 'Robert', 'last_name' => 'Plant', 'date_of_birth' => '1948-08-20'],
                     6 => ['first_name' => 'Jimmy', 'last_name' => 'Page', 'date_of_birth' => '1944-01-09'],
                     7 => ['first_name' => 'John Paul', 'last_name' => 'Jones', 'date_of_birth' => '1946-01-03'],
                     8 => ['first_name' => 'John', 'last_name' => 'Bonham', 'date_of_birth' => '1948-05-31'],
                     9 => ['first_name' => 'John', 'last_name' => 'Lennon', 'date_of_birth' => '1940-10-09'],
                     10 => ['first_name' => 'Paul', 'last_name' => 'McCartney', 'date_of_birth' => '1942-06-18'],
                     11 => ['first_name' => 'George', 'last_name' => 'Harrison', 'date_of_birth' => '1943-02-24'],
                     12 => ['first_name' => 'Ringo', 'last_name' => 'Starr', 'date_of_birth' => '1940-07-07'],
                     13 => ['first_name' => 'Jimi', 'last_name' => 'Hendrix', 'date_of_birth' => '1942-11-27'],
                     14 => ['first_name' => 'Noel', 'last_name' => 'Redding', 'date_of_birth' => '1945-12-25'],
                     15 => ['first_name' => 'Mitch', 'last_name' => 'Mitchell', 'date_of_birth' => '1946-07-09'],
                 ] as $data) {
            Person::create($data);
        }

        // Seed Bands
        foreach ([
                     1 => 'Rolling Stones',
                     2 => 'Led Zeppelin',
                     3 => 'Beatles',
                     4 => 'Jimi Hendrix Experience',
                 ] as $name) {
            Band::create([
                'name' => $name,
            ]);
        }

        // Associate persons with bands
        foreach ([
                     1 => [1,2,3,4],
                     2 => [5,6,7,8],
                     3 => [9,10,11,12],
                     4 => [13,14,15],
                 ] as $bandId => $personIds){
            foreach ($personIds as $personId) {
                DB::table('band_members')->insert([
                    'band_id' => $bandId,
                    'person_id' => $personId,
                ]);
            }
        }

        // Seed Albums
        foreach ([
                     1 => ['band_id' => 1, 'name' => 'Let it bleed', 'release_date' => '1969-12-05'],
                     2 => ['band_id' => 1, 'name' => 'Sticky Fingers', 'release_date' => '1971-04-23'],
                     3 => ['band_id' => 1, 'name' => 'Exile on main st.', 'release_date' => '1972-05-12'],
                     4 => ['band_id' => 2, 'name' => 'Led Zeppelin', 'release_date' => '1969-01-12'],
                     5 => ['band_id' => 2, 'name' => 'Led Zeppelin II', 'release_date' => '1969-10-22'],
                     6 => ['band_id' => 2, 'name' => 'Led Zeppelin III', 'release_date' => '1970-10-05'],
                     7 => ['band_id' => 3, 'name' => 'Sgt. Peppers lonely hearts club band', 'release_date' => '1967-06-01'],
                     8 => ['band_id' => 3, 'name' => 'Abbey road', 'release_date' => '1969-09-26'],
                     9 => ['band_id' => 3, 'name' => 'Let it be', 'release_date' => '1970-05-08'],
                     10 => ['band_id' => 4, 'name' => 'Are you experienced', 'release_date' => '1967-05-12'],
                     11 => ['band_id' => 4, 'name' => 'Axis: Bold as love', 'release_date' => '1967-12-01'],
                     12 => ['band_id' => 4, 'name' => 'Electric ladyland', 'release_date' => '1968-10-16'],
                 ] as $data) {
            Album::create($data);
        }

    }
}

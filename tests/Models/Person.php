<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 04-09-18
 * Time: 21:51
 */

namespace JosKolenberg\Jory\Tests\Models;


class Person extends Model
{

    public function bands()
    {
        return $this->belongsToMany(Band::class);
    }

    public static function seed()
    {
        foreach ([
                     1 => 'Mick Jagger',
                     2 => 'Keith Richards',
                     3 => 'Ronnie Wood',
                     4 => 'Charlie Watts',
                     5 => 'Robert Plant',
                     6 => 'Jimmy Page',
                     7 => 'John Paul Jones',
                     8 => 'John Bonham',
                     9 => 'Paul McCartney',
                     10 => 'John Lennon',
                     11 => 'George Harrison',
                     12 => 'Jimi Hendrix',
                     13 => 'Noel Redding',
                     14 => 'Mitch Mitchell',
                 ] as $name) {
            static::create([
                'name' => $name,
            ]);
        }
    }
}
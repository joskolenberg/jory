<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 06-09-18
 * Time: 23:11
 */

namespace JosKolenberg\Jory\Tests\Models;


use Illuminate\Support\Facades\DB;

class Band extends Model
{

    public function people()
    {
        return $this->belongsToMany(Person::class);
    }

    public static function seed()
    {
        foreach ([
                     1 => 'Rolling Stones',
                     2 => 'Led Zeppelin',
                     3 => 'Beatles',
                     4 => 'Jimi Hendrix Experience',
                 ] as $name) {
            static::create([
                'name' => $name,
            ]);
        }

        foreach ([
            1 => [1,2,3,4],
            2 => [5,6,7,8],
            3 => [9,10,11,12],
            4 => [13,14,15],
                 ] as $bandId => $personIds){
            foreach ($personIds as $personId) {
                DB::table('band_person')->insert([
                    'band_id' => $bandId,
                    'person_id' => $personId,
                ]);
            }
        }
    }

}
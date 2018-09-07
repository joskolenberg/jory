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
        return $this->belongsToMany(Band::class, 'band_members');
    }

}
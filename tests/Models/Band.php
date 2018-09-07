<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 06-09-18
 * Time: 23:11
 */

namespace JosKolenberg\Jory\Tests\Models;

class Band extends Model
{

    public function members()
    {
        return $this->belongsToMany(Person::class, 'band_members');
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 06-09-18
 * Time: 21:11
 */

namespace JosKolenberg\Jory\Tests\Models;


class Model extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = ['id'];
    public $timestamps = false;
}
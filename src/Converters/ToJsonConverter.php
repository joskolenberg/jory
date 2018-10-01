<?php

namespace JosKolenberg\Jory\Converters;

use JosKolenberg\Jory\Jory;

/**
 * Class to convert a Jory object to a Json string.
 *
 * Class ToArrayConverter
 */
class ToJsonConverter
{
    /**
     * @var Jory
     */
    private $jory;
    /**
     * @var bool
     */
    private $minified;

    /**
     * ToJsonConverter constructor.
     *
     * @param Jory $jory
     * @param bool $minified
     */
    public function __construct(Jory $jory, bool $minified = true)
    {
        $this->jory = $jory;
        $this->minified = $minified;
    }

    /**
     * Get the Json string based on given Jory object.
     *
     * @return array
     */
    public function get(): string
    {
        $array = (new ToArrayConverter($this->jory, $this->minified))->get();

        // if array is empty convert into empty object
        // To prevent json being an array [] instead of an object {}
        if(empty($array)){
            $array = new \stdClass();
        }else{
            $this->fixEmptyRelationArrays($array);
        }

        return json_encode($array);
    }

    /**
     * If relations hold an empty array as data, convert them to an empty object.
     * This result in json being an object {} (what we want) instead of an array [].
     *
     * @param array $array
     */
    protected function fixEmptyRelationArrays(array &$array)
    {
        $activeKey = '';
        if(array_key_exists('rlt', $array)) $activeKey = 'rlt';
        if(array_key_exists('relations', $array)) $activeKey = 'relations';

        if(!$activeKey) return;

        $relations = $array[$activeKey];

        if($relations){
            foreach ($relations as $name => $jory){
                if(empty($jory)){
                    $array[$activeKey][$name] = new \stdClass();
                }else{
                    $this->fixEmptyRelationArrays($jory);
                }
            }
        }
    }
}

<?php

namespace Company\Related\Helper;

/**
 * Class Data
 * @package Company\Related\Helper
 */
class Data extends Config
{
    /**
     * @param array $data
     * @param $field
     * @return array
     */
    public function getIdsArray(array $data, $field)
    {
        $newData = [];
        foreach ($data as $item) {
            $newData[] = $item[$field];
        }

        return $newData;
    }
}
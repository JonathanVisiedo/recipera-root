<?php

namespace Ghost\Services;

class FoodApiService
{

    /**
     * @var string
     */
    private $url;

    /**
     * @param string $url
     */
    public function __construct($url = "https://world.openfoodfacts.org/api/v0/product/")
    {
        $this->url = $url;
    }


    public function call($barcode)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . trim($barcode, ' ') . '.json');
        curl_setopt($ch, CURLOPT_HEADER, 0);            // No header in the result
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return, do not echo result
        $raw_data = curl_exec($ch);
        curl_close($ch);
        $return =  json_decode($raw_data, true);

        return $return;
    }

    public function getNutriments(array $data)
    {

        $is_water = $data['product']['nutriscore_data']['is_water'];
        $is_fat = $data['product']['nutriscore_data']['is_fat'];
        $is_beverage = $data['product']['nutriscore_data']['is_beverage'];


        $type = ($is_water == '1' ? 'water' : ($is_fat == '1' ? 'fat' : ($is_beverage == '1' ? 'beverage' : 'uknown')));


        $nutriments = $data['product']['nutriments'];
        $values = [
            'type' => $type
        ];

        /**
         * Those values are excluded because they wont be used in the nutriment array
         */
        $excluded = ['nova-group_100g', 'enery_100g', 'fruits-vegetables-nuts-estimate-from-ingredients_100g', 'nutrition-score-fr_100g', 'nutrition-score-en_100g', 'nutrition-score-es_100g', 'nutrition-score-nl_100g'];

        /**
         * We have to read the array of nutriments because each beverage is different and so are the returns,
         * lets take _100g as name provider because it's generic across all products. It's also going to help
         * to calculate the value for the recipe as we have g in common.
         */
        foreach ($nutriments as $key => $nutriment){

            if(preg_match('/_100g$/', $key) && !in_array($key, $excluded)) {
                $name = str_replace('_100g', '', $key);
                $unit = $nutriments[$name.'_unit'];
                $values[$name] = [
                    'unit' => $unit,
                    '100g' => $nutriment
                ];
            }

        }

        return $values;
    }

}
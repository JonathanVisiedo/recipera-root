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


        /**
         * Those values are excluded because they wont be used in the nutriment array
         */
        $excluded = [
            'nova-group_100g',
            'energy_100g',
            'fruits-vegetables-nuts-estimate-from-ingredients_100g',
            'nutrition-score-fr_100g',
            'nutrition-score-en_100g',
            'nutrition-score-es_100g',
            'nutrition-score-nl_100g',
            'carbon-footprint-from-known-ingredients_100g'
        ];

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

    function nutri_table($recipe)
    {
        $nutri_table = [];
        $daily = $this->daily_recommendation();
        for($i=0;$i<count($recipe['ingredients']);$i++) {

            $nutriment_keys = array_keys($recipe['ingredients'][$i]['nutriments']);

            for($j = 0; $j < count($nutriment_keys); $j++) {
                $nutri_table['recipe'][$nutriment_keys[$j]] += $recipe['ingredients'][$i]['nutriments'][$nutriment_keys[$j]]['100g'] / 100 * $recipe['ingredients'][$i]['quantity'];
                $nutri_table['100g'][$nutriment_keys[$j]] += $recipe['ingredients'][$i]['nutriments'][$nutriment_keys[$j]]['100g'];
            }

            for($j = 0; $j < count($nutriment_keys); $j++) {
                if($daily[$nutriment_keys[$j]] > 0) {
                    $nutri_table['recipe_daily']['percent'][$nutriment_keys[$j]] = $nutri_table['recipe'][$nutriment_keys[$j]] / $daily[$nutriment_keys[$j]] * 100;
                    $nutri_table['recipe_daily']['daily'][$nutriment_keys[$j]] = $daily[$nutriment_keys[$j]];
                }

            }

        }

        return $nutri_table;
    }

    private function daily_recommendation () {

        return [
            'fat' => 70,
            'saturated-fat' => 20,
            'cholesterol' => 0.3,
            'sodium' => 2.3,
            'potassium' => 4.7,
            'carbohydrates' => 260,
            'sugars' => 90,
            'salt' => 6,
            'fiber' => 28,
            'proteins' => 50,
            'iron' => 0.014,
            'vitamin-A' => 0.0009,
            'vitamin-C' => 0.09,
            'vitamin-D' => 0.00002,
            'vitamin-E' => 0.015,
            'calcium' => 1.3,
            'chromium' => 0.000035,
            'magnesium' => 0.42,
            'manganese' => 0.0023,
            'selenium' => 0.000055,
            'zinc' => 0.0023,
            'chloride' => 0.0023,
            'energy-kcal' => 2000,
            'energy-kj' => 8400
        ];

    }


}
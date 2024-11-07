<?php

class shopCollectionPluginBackendGoController extends waViewController
{
    private int $collection_feature_id = 148;
    private int $provider_feature_id = 144;
    private int $parent_category_id = 2986;
    private int $provider_feature_value_id;
    private int $collection_feature_value_id;

    public function execute()
    {
        $settings = wa('shop')->getPlugin('collection')->getSettings();
        $this->collection_feature_id = $settings['collection_feature_id'];
        $this->provider_feature_id = $settings['provider_feature_id'];
        $this->parent_category_id = $settings['parent_category_id'];

        $id = waRequest::get('id');

        $this->provider_feature_value_id = 0;
        $this->collection_feature_value_id = 0;

        $model = new shopProductFeaturesModel();
        $features = $model->getByField('product_id', $id, true);

        foreach ($features as $feature) {
            if ($feature['feature_id'] == $this->provider_feature_id) {
                $this->provider_feature_value_id = $feature['feature_value_id'];
            }
            if ($feature['feature_id'] == $this->collection_feature_id) {
                $this->collection_feature_value_id = $feature['feature_value_id'];
            }
        }

        $url = '/';

        if ($this->provider_feature_value_id & $this->collection_feature_value_id) {

            $url = $this->getCategoryUrl();

            // empty category
            if ($url == '/') {

                $model = new shopFeatureValuesVarcharModel();
                $provider_feature_name = $model->getByField('id', $this->provider_feature_value_id, true);
                $collection_feature_name = $model->getByField('id', $this->collection_feature_value_id, true);
                if (isset($provider_feature_name[0]['value']) & isset($collection_feature_name[0]['value'])) {

                    // create category
                    $model = new shopCategoryModel();
                    $new_url = $model->stripCategoryUrl($this::transliterate('Collection-' . $provider_feature_name[0]['value'] . "-" . $collection_feature_name[0]['value'], 'ru_RU'));
                    $new_url = $model->suggestUniqueUrl($new_url, null, $this->parent_category_id);

                    $name = $collection_feature_name[0]['value'];

                    $params = [
                        'name' => $name,
                        'url' => $new_url,
                        'conditions' => $this->getContition(true),
                        'type' => 1,
                        'status' => 0,
                    ];

                    $model->add($params, $this->parent_category_id);

                    $url = $this->getCategoryUrl();
                }

            }

        }

        $this->redirect($url, 301);
    }

    private function getCategoryUrl()
    {

        $condition1 = $this->getContition(true);
        $condition2 = $this->getContition();

        $model = new waModel();
        $result = $model->query("SELECT url FROM shop_category WHERE parent_id = {$this->parent_category_id} AND (conditions = '" . $condition1 . "' OR conditions = '" . $condition2 . "')");
        $data = $result->fetchAll();
        if (isset($data[0]['url'])) {
            return "/category/" . $data[0]['url'];
        }
        return '/';
    }

    private function getContition($mode = false)
    {
        if ($mode)
            return "postavshchik.value_id={$this->provider_feature_value_id}&kollektsiya.value_id={$this->collection_feature_value_id}";
        else
            return "kollektsiya.value_id={$this->collection_feature_value_id}&postavshchik.value_id={$this->provider_feature_value_id}";
    }

    public static function transliterate($string)
    {
        static $transliteration = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
            'т' => 't', 'у' => 'u', 'ф' => 'f', 'ы' => 'y', 'э' => 'e', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G',
            'Д' => 'D', 'Е' => 'E', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M',
            'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Ы' => 'Y',
            'Э' => 'E', 'ё' => 'yo', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ъ' => '',
            'ь' => '', 'ю' => 'yu', 'я' => 'ya', 'Ё' => 'YO', 'Х' => 'H', 'Ц' => 'TS', 'Ч' => 'CH', 'Ш' => 'SH',
            'Щ' => 'SHCH', 'Ъ' => '', 'Ь' => '', 'Ю' => 'YU', 'Я' => 'YA', ' ' => '-', '+' => '-',
        );

        return strtr($string, $transliteration);
    }

    public static function toCanonicalUrl($url, $ignore_tolower = false)
    {
        if ($ignore_tolower) {
            return preg_replace('/[^a-z_0-9\-]/i', '', self::transliterate($url));
        }
        return strtolower(preg_replace('/[^a-z_0-9\-]/i', '', self::transliterate($url)));
    }

}
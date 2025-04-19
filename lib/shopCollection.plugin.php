<?php

class shopCollectionPlugin extends shopPlugin
{
    public static function show($product_id, $feature_name, $feature_value)
    {
        $settings = wa('shop')->getPlugin('collection')->getSettings();
        $collection_feature_id = $settings['collection_feature_id'];
        $model = new shopFeatureModel();
        $data = $model->getByField('id', $collection_feature_id);
        if (isset($data['code']) && $data['code'] == $feature_name) {
            $url = "/webasyst/shop/?plugin=collection&action=go&id=" . $product_id;
            return "<a href=\"$url\">{$feature_value}</a>";
        }
        return $feature_value;
    }

    public static function getComplect($product_id)
    {
        // получаем коды характеристик
        $settings = wa('shop')->getPlugin('collection')->getSettings();
        $complect_feature_id = $settings['complect_feature_id'];
        $provider_feature_id = $settings['provider_feature_id'];

        // получаем их имя
        $model = new shopFeatureModel();
        $data = $model->getByField('id', $complect_feature_id, true);
        if (!isset($data[0]['code']))
            return "";
        $complect_feature_name = $data[0]['code'];

        $data = $model->getByField('id', $provider_feature_id, true);
        if (!isset($data[0]['code']))
            return "";
        $provider_feature_name = $data[0]['code'];

        // получаем из товара значения характеристик
        $model = new shopProductFeaturesModel();
        $data = $model->getValues($product_id);
        if (!isset($data[$complect_feature_name]))
            return "";
        $complect_feature_value = $data[$complect_feature_name];
        if (!isset($data[$provider_feature_name]))
            return "";
        $provider_feature_value = $data[$provider_feature_name];

        // возвращаем товар по такому поставщику с таким значением характеристики комплект
        $collection = new shopProductsCollection('search/' . $provider_feature_name . '=' . $provider_feature_value . '%26' . $complect_feature_name . '=' . $complect_feature_value);
        $data = $collection->getProducts('*,skus_filtered');

        $newData = [];
        foreach ($data as $key => $product) {
            if ($key == $product_id)
                continue;
            $newData[$key] = $product;
        }

        if (count($newData) > 0) {
            return $newData;
        }
        return [];
    }
}
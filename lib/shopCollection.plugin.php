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
}
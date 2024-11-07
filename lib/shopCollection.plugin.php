<?php

class shopCollectionPlugin extends shopPlugin
{
    public static function show($product_id, $feature_name, $feature_value)
    {
        if ($feature_name == 'kollektsiya') {
            $url = "/webasyst/shop/?plugin=collection&action=go&id=" . $product_id;
            return "<a href=\"$url\">{$feature_value}</a>";
        }
        return $feature_value;
    }
}
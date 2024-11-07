<?php

class shopCollectionPluginSettingsSaveController extends waJsonController
{
    public function execute()
    {
        try {
            $post = warequest::post('settings');

            $settings = array(
                'provider_feature_id' => $post['provider_feature_id'],
                'collection_feature_id' => $post['collection_feature_id'],
                'parent_category_id' => $post['parent_category_id'],
            );

            wa()->getplugin('collection')->savesettings($settings);
            $this->response = array('msg' => _wp('Saved'));
        } catch (waException $e) {
            $this->setError($e->getMessage());
        }
    }
}

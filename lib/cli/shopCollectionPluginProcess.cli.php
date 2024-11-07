<?php

class shopCollectionPluginProcessCli extends waCliController
{
    protected $collection_feature_id;
    protected $parent_category_id;
    private $provider_feature_id;

    public function execute()
    {
        $settings = wa('shop')->getPlugin('collection')->getSettings();

        $this->collection_feature_id = $settings['collection_feature_id'];
        $this->provider_feature_id = $settings['provider_feature_id'];
        $this->parent_category_id = $settings['parent_category_id'];

        if ($this->collection_feature_id == 0 || $this->provider_feature_id == 0 || $this->parent_category_id == 0) {
            waLog::log('Настройте плагин', 'shop/collection.cli.log');
            return;
        }

        $model = new waModel();
        $result = $model->query("
            SELECT DISTINCT
                p.id 
            FROM
                shop_product p
                JOIN shop_product_features pf_k ON pf_k.product_id = p.id
                JOIN shop_product_features pf_b ON pf_b.product_id = p.id
            WHERE
                NOT EXISTS (SELECT * FROM shop_category_products cp WHERE cp.category_id = {$this->parent_category_id} AND cp.product_id = p.id)
                AND pf_k.feature_id = {$this->collection_feature_id}
                AND pf_b.feature_id = {$this->provider_feature_id}");
        $data = $result->fetchAll();

        if (count($data)) {

            $model = new shopCategoryProductsModel();

            $products = [];
            foreach ($data as $product)
                $products[] = $product['id'];

            $model->add($products, $this->parent_category_id);

            $qty = count($data);
            waLog::log("Добавили товаров: {$qty}", 'shop/collection.cli.log');
        }
    }
}
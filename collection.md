```
echo "<pre>";
$collection = new shopProductsCollection('category/1960');
// getProducts($fields = "*", $offset = 0, $limit = null, $escape = true)
// https://developers.webasyst.com/api/explorer/shop/shop.product.search/
$products = $collection->getProducts('*,images',0,200);
echo count($products)."<br>";
print_r($products);
die();
```

https://localhost/product/Koltso_Fresh_fr_tc-r00701-b-w-x-x-w/

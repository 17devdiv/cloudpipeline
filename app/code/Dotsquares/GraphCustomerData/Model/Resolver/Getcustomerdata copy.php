<?php

declare(strict_types=1);

namespace Dotsquares\GraphCustomerData\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;

/**
 * Customers field resolver, used for GraphQL request processing.
 */
class Getcustomerdata implements ResolverInterface
{
    public $_storeManager;
    public $_swatchHelper;
    public $_swatchCollection;
    public $_productFactory;
    private $productRepository;
    private $wishlist;
    protected $userContext;
    protected $stockRegistry;
    private $getSalableQuantityDataBySku;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Swatches\Helper\Media $swatchHelper,
        \Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory $swatchCollection,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Wishlist\Model\Wishlist $wishlist,
        \Magento\Authorization\Model\CompositeUserContext $userContext,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku $getSalableQuantityDataBySku
    ) {
        $this->_storeManager = $storeManager;
        $this->_swatchHelper = $swatchHelper;
        $this->_swatchCollection = $swatchCollection;
        $this->_productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->wishlist = $wishlist;
        $this->userContext = $userContext;
        $this->stockRegistry = $stockRegistry;
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
    }


    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $data = $this->getListProduct($args);
        // print_r($data);die;
        return $data;
    }

    public function getListProduct($args)
    {   
        if (isset($args['prosku']) && $args['prosku']) {
            $productSku = $args['prosku'];
            //var_dump($this->getCustomerId()); die;
            //print_r($this->getAttrLabelByCode('has_options')); die;
            $baseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
            $linkUrl = str_replace(' ', '%20', $productSku);
            $apiUrl = $baseUrl . "rest/V1/products/" . $linkUrl;
            //echo $apiUrl; die;
            // $headers = array('Content-Type:application/json');
            // $accessToken = "eyJraWQiOiIxIiwiYWxnIjoiSFMyNTYifQ.eyJ1aWQiOjEsInV0eXBpZCI6MiwiaWF0IjoxNzA3OTA3MzcyLCJleHAiOjE3MDc5MTA5NzJ9.YXO6sz1wwsPKjs37sGFWOLZx9M6ezg44Py2wFS_Ry9o";
            $headers = [
                // "Authorization: Bearer $accessToken", 
                "Content-Type: application/json"
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($ch);
            curl_close($ch);

            $respnseCover = json_decode($response, TRUE);
            // print_r($respnseCover);die;
            if (isset($respnseCover['extension_attributes']['configurable_product_options']) && $respnseCover['extension_attributes']['configurable_product_options']) {
                $configureOptions = $respnseCover['extension_attributes']['configurable_product_options'];

                $newArr = array();
                $newArr1 = array();
                foreach ($configureOptions as $key => $value) {
                    if ($value['label'] == 'Color') {
                        foreach ($value['values'] as $key1 => $value1) {
                            $newArr[] = array(
                                'value_index' => $value1['value_index'],
                                'label' => $this->getOptionTextLabel($value1['value_index'], 'color'),
                                'thumb' => $this->getSwatchThumbImage($value1['value_index']),
                                'tooltip' => $this->getSwatchImage($value1['value_index'])
                            );
                            $respnseCover['extension_attributes']['configurable_product_options'][$key]['values'] = $newArr;
                        }
                    }

                    if ($value['label'] == 'Size') {
                        foreach ($value['values'] as $key1 => $value1) {
                            $newArr1[] = array(
                                'value_index' => $value1['value_index'],
                                'label' => $this->getOptionTextLabel($value1['value_index'], 'size')
                            );
                            $respnseCover['extension_attributes']['configurable_product_options'][$key]['values'] = $newArr1;
                        }
                    }
                }

                $configureSubProducts = $respnseCover['extension_attributes']['configurable_product_links'];

                $subSelection = array();

                foreach ($configureSubProducts as $key2 => $value2) 
                {

                    $proGet = $this->_productFactory->create()->load($value2);
                    $productStock = $this->stockRegistry->getStockItem($value2);
                    $salable = $this->getSalableQuantityDataBySku->execute($proGet->getSku());
                    //print_r($salable[0]['qty']); die;
                    $stock = array();
                    if (count($salable) > 0) {
                        $stock[] = array('qty' => $salable[0]['qty'], 'manage_stock' => $salable[0]['qty'] > 0 ? $salable[0]['manage_stock'] : false);
                    }
                    //print_r($productStock->debug()); die;


                    //print_r($proGet->debug()); die;
                    $subSelection[] = array(
                        'id' => $value2,
                        'price' => $proGet->getPrice(),
                        'special_price' => $proGet->getSpecialPrice(),
                        'special_from' => $proGet->getSpecialFromDate(),
                        'special_to' => $proGet->getSpecialToDate(),
                        'color' => $proGet->getResource()->getAttribute('color')->getFrontend()->getValue($proGet),
                        'size' => $proGet->getResource()->getAttribute('size')->getFrontend()->getValue($proGet),
                        'small_img' => $proGet->getSmallImage(),
                        'thumb_img' => $proGet->getThumbnail(),
                        'image' => $proGet->getImage(),
                        'swatch_img' => $proGet->getSwatchImage(),
                        'stock_management' => $stock
                    );
                    $respnseCover['extension_attributes']['configurable_product_links'] = $subSelection;
                }
            }

            if (isset($respnseCover['product_links']) && $respnseCover['product_links']) {
                $relatedProducts = $respnseCover['product_links'];
                $releArray = array();
                foreach ($relatedProducts as $relatedKey => $relatedValue) {
                    $proReleted = $this->getDetailBySku($relatedValue['linked_product_sku']);
                    $priceAsLow = '';
                    if ($relatedValue['linked_product_type'] == "configurable") {
                        $priceAsLow = $this->getAsLowAs($proReleted);
                    }

                    $releArray[] = array(
                        'sku' => $relatedValue['sku'],
                        'product_id' => $proReleted->getId(),
                        'wishlist_flag' => $this->wishlistInvestigate($proReleted->getId()),
                        'link_type' => $relatedValue['link_type'],
                        'linked_product_sku' => $relatedValue['linked_product_sku'],
                        'linked_product_type' => $relatedValue['linked_product_type'],
                        'position' => $relatedValue['position'],
                        'name' => $proReleted->getName(),
                        'price' => ($priceAsLow) ? $priceAsLow : $proReleted->getFinalPrice(),
                        'special_price' => $proReleted->getSpecialPrice(),
                        'small_img' => $proReleted->getSmallImage(),
                        'thumb_img' => $proReleted->getThumbnail(),
                        'image' => $proReleted->getImage()
                    );
                    $respnseCover['product_links'] = $releArray;
                }
            }

            if (isset($respnseCover['custom_attributes']) && $respnseCover['custom_attributes']) {
                $attr = $respnseCover['custom_attributes'];
                $atrArray = array();
                foreach ($attr as $atrKey => $atrValue) {
                    $labelGet = $this->getAttrLabelByCode($atrValue['attribute_code']);
                    if (isset($labelGet['default_frontend_label']) && $labelGet['default_frontend_label']) {
                        $atrArray[] = array(
                            'attribute_code' => $atrValue['attribute_code'],
                            'attribute_label' => $labelGet['default_frontend_label'],
                            'value' => $atrValue['value']
                        );
                    } else {
                        $atrArray[] = array(
                            'attribute_code' => $atrValue['attribute_code'],
                            'value' => $atrValue['value']
                        );
                    }
                    $respnseCover['custom_attributes'] = $atrArray;
                }
            }

            $respnseCover['wishlist'] = $this->wishlistInvestigate($this->getDetailBySku($productSku)->getId());

            // print_r($respnseCover);die;
            return $respnseCover;
            // echo json_encode($respnseCover);
            // exit;
        }
    }

    public function getSwatchThumbImage($optionIdvalue)
    {
        $swatchCollection = $this->_swatchCollection->create();
        $swatchCollection->addFieldtoFilter('option_id', $optionIdvalue);
        $item = $swatchCollection->getFirstItem();
        if (!empty($item->getValue()))
            return $this->_swatchHelper->getSwatchAttributeImage('swatch_thumb', $item->getValue());
    }

    public function getSwatchImage($optionIdvalue)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/swatcheslog.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        // $logger->info("text");

        $swatchCollection = $this->_swatchCollection->create();
        $swatchCollection->addFieldtoFilter('option_id', $optionIdvalue);
        $item = $swatchCollection->getFirstItem();
        $logger->info(print_r($item->getData(), true));
        // $logger->info(print_r($item->getValue()));
        if (!empty($item->getValue()))
            return $item->getValue();
    }

    public function getOptionTextLabel($option_id, $attribute_code)
    {
        $product = $this->_productFactory->create();
        $isAttributeExist = $product->getResource()->getAttribute($attribute_code);
        $optionText = '';
        if ($isAttributeExist && $isAttributeExist->usesSource()) {
            $optionText = $isAttributeExist->getSource()->getOptionText($option_id);
        }
        return $optionText;
    }

    public function getCustomerId()
    {
        return $this->userContext->getUserId();
    }

    public function wishlistInvestigate($productId)
    {
        $wishlistFlag = 0;
        $productsIds = array();
        $wishlistproIds = $this->wishlist->loadByCustomerId($this->getCustomerId())->getItemCollection();
        foreach ($wishlistproIds as $items) {
            $productsIds[] = $items->getProduct()->getId();
        }

        if (in_array($productId, $productsIds)) {
            $wishlistFlag = 1;
        }

        return $wishlistFlag;
    }

    public function getDetailBySku($sku)
    {
        return $this->productRepository->get($sku);
    }

    public function getAttrLabelByCode($code)
    {
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $apiUrl = $baseUrl . "rest/V1/products/attributes/" . $code;
        $headers = array('Content-Type:application/json');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, TRUE);
    }

    public function getAsLowAs($product)
    {
        $_children = $product->getTypeInstance()->getUsedProducts($product);

        $priceArray = array();
        foreach ($_children as $child) {
            $priceArray[] = $child->getFinalPrice();
        }
        $asLowas = min($priceArray);

        return $asLowas;
    }
}

<?php

namespace FacebookFeed\API\Resource;

use FacebookFeed\Model\FacebookFeedProductExcludedQuery;
use FacebookFeed\Model\Map\FacebookFeedProductExcludedTableMap;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Resource\ProductSaleElements AS ProductSaleElementsResource;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\ResourceAddonInterface;
use Thelia\Api\Resource\ResourceAddonTrait;
use Thelia\Model\ProductSaleElements;

class FacebookFeedProductExcluded implements ResourceAddonInterface
{
    use ResourceAddonTrait;

    public ProductSaleElementsResource $productSaleElements;

    #[Groups([ProductSaleElementsResource::GROUP_ADMIN_READ, ProductSaleElementsResource::GROUP_ADMIN_WRITE])]
    public bool $isExcluded = false;

    /**
     * @throws PropelException
     */
    public function buildFromModel(ActiveRecordInterface|ProductSaleElements $activeRecord, PropelResourceInterface $abstractPropelResource): ResourceAddonInterface
    {
        if (null === $facebookFeedProductExcluded = FacebookFeedProductExcludedQuery::create()->filterByProductSaleElements($activeRecord)->findOne()){
            return $this;
        }

        $this->setIsExcluded(
            $activeRecord->hasVirtualColumn('FacebookFeedProductExcluded_is_excluded')
                ? $activeRecord->getVirtualColumn('FacebookFeedProductExcluded_is_excluded')
                : $facebookFeedProductExcluded->getIsExcluded()
        );

        return $this;
    }

    public function buildFromArray(array $data, PropelResourceInterface $abstractPropelResource): ResourceAddonInterface
    {
        if (isset($data['isExcluded'])){
            $this->setIsExcluded($data['isExcluded']);
        }

        return $this;
    }

    /**
     * @throws PropelException
     */
    public function doSave(ActiveRecordInterface|ProductSaleElements $activeRecord, PropelResourceInterface $abstractPropelResource): void
    {
        if (null === $model = FacebookFeedProductExcludedQuery::create()->useProductSaleElementsQuery()->filterById($activeRecord->getId())->endUse()->findOne()){
            $model = new \FacebookFeed\Model\FacebookFeedProductExcluded();
            $model->setProductSaleElements($activeRecord);
        }

        $model->setIsExcluded($this->isExcluded());
        $model->save();
    }


    public function isExcluded(): bool
    {
        return $this->isExcluded;
    }

    public function setIsExcluded(bool $isExcluded): FacebookFeedProductExcluded
    {
        $this->isExcluded = $isExcluded;
        return $this;
    }

    public function getProductSaleElements(): ProductSaleElementsResource
    {
        return $this->productSaleElements;
    }

    public function setProductSaleElements(ProductSaleElementsResource $productSaleElements): FacebookFeedProductExcluded
    {
        $this->productSaleElements = $productSaleElements;
        return $this;
    }

    public static function getResourceParent(): string
    {
        return \Thelia\Api\Resource\ProductSaleElements::class;
    }

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new FacebookFeedProductExcludedTableMap();
    }
}
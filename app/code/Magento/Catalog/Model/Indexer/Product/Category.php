<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Catalog\Model\Indexer\Product;

class Category extends \Magento\Catalog\Model\Indexer\Category\Product
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'catalog_product_category';

    /**
     * @param \Magento\Catalog\Model\Indexer\Category\Product\Action\FullFactory $fullActionFactory
     * @param Category\Action\RowsFactory $rowsActionFactory
     * @param \Magento\Indexer\Model\IndexerRegistry $indexerRegistry
     */
    public function __construct(
        \Magento\Catalog\Model\Indexer\Category\Product\Action\FullFactory $fullActionFactory,
        Category\Action\RowsFactory $rowsActionFactory,
        \Magento\Indexer\Model\IndexerRegistry $indexerRegistry
    ) {
        parent::__construct($fullActionFactory, $rowsActionFactory, $indexerRegistry);
    }
}

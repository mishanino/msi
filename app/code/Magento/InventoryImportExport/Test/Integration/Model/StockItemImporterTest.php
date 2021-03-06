<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventoryImportExport\Test\Integration\Model;

use Magento\CatalogImportExport\Model\StockItemImporterInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryCatalog\Api\DefaultSourceProviderInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class StockItemImporterTest extends TestCase
{
    /**
     * @var DefaultSourceProviderInterface
     */
    private $defaultSourceProvider;
    
    /**
     * @var StockItemImporterInterface
     */
    private $importer;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var SourceItemRepositoryInterface
     */
    private $sourceItemRepository;

    /**
     * Setup Test for Stock Item Importer
     */
    public function setUp()
    {
        $this->defaultSourceProvider = Bootstrap::getObjectManager()->get(
            DefaultSourceProviderInterface::class
        );
        $this->importer = Bootstrap::getObjectManager()->get(
            StockItemImporterInterface::class
        );
        $this->searchCriteriaBuilderFactory = Bootstrap::getObjectManager()->get(
            SearchCriteriaBuilderFactory::class
        );
        $this->sourceItemRepository = Bootstrap::getObjectManager()->get(
            SourceItemRepositoryInterface::class
        );
    }

    /**
     * Tests Source Item Import of default source
     *
     * @magentoDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/products.php
     * @magentoDbIsolation enabled
     */
    public function testSourceItemImportWithDefaultSource()
    {
        $stockData = [
            'sku' => 'SKU-1',
            'qty' => 1,
            'is_in_stock' => SourceItemInterface::STATUS_IN_STOCK
        ];

        $this->importer->import([$stockData]);

        $compareData = $this->buildDataArray($this->getSourceItemList()->getItems());
        $expectedData = [
            SourceItemInterface::SKU => $stockData['sku'],
            SourceItemInterface::QUANTITY => '1.0000',
            SourceItemInterface::SOURCE_CODE => (string)$this->defaultSourceProvider->getCode(),
            SourceItemInterface::STATUS => (string)SourceItemInterface::STATUS_IN_STOCK
        ];

        $this->assertArrayHasKey('SKU-1', $compareData);
        $this->assertSame($expectedData, $compareData['SKU-1']);
    }

    /**
     * Get List of Source Items which match SKU and Source ID of dummy data
     *
     * @return \Magento\InventoryApi\Api\Data\SourceItemSearchResultsInterface
     */
    private function getSourceItemList()
    {
        /** @var SearchCriteriaBuilder $searchCriteria */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

        $searchCriteriaBuilder->addFilter(
            SourceItemInterface::SKU,
            'SKU-1'
        );

        $searchCriteriaBuilder->addFilter(
            SourceItemInterface::SOURCE_CODE,
            $this->defaultSourceProvider->getCode()
        );

        /** @var SearchCriteria $searchCriteria */
        $searchCriteria = $searchCriteriaBuilder->create();
        return $this->sourceItemRepository->getList($searchCriteria);
    }

    /**
     * @param SourceItemInterface[] $sourceItems
     * @return array
     */
    private function buildDataArray(array $sourceItems)
    {
        $comparableArray = [];
        foreach ($sourceItems as $sourceItem) {
            $comparableArray[$sourceItem->getSku()] = [
                SourceItemInterface::SKU => $sourceItem->getSku(),
                SourceItemInterface::QUANTITY => $sourceItem->getQuantity(),
                SourceItemInterface::SOURCE_CODE => $sourceItem->getSourceCode(),
                SourceItemInterface::STATUS => $sourceItem->getStatus()
            ];
        }
        return $comparableArray;
    }
}

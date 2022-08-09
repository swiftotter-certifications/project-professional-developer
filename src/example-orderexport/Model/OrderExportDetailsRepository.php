<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsSearchResultsInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsSearchResultsInterfaceFactory;
use SwiftOtter\OrderExport\Api\OrderExportDetailsRepositoryInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface;
use SwiftOtter\OrderExport\Model\ResourceModel\OrderExportDetails as OrderExportDetailsResource;
use SwiftOtter\OrderExport\Model\OrderExportDetails;
use SwiftOtter\OrderExport\Model\OrderExportDetailsFactory;
use SwiftOtter\OrderExport\Model\ResourceModel\OrderExportDetails\Collection as OrderExportDetailsCollection;
use SwiftOtter\OrderExport\Model\ResourceModel\OrderExportDetails\CollectionFactory as OrderExportDetailsCollectionFactory;

class OrderExportDetailsRepository implements OrderExportDetailsRepositoryInterface
{
    /** @var OrderExportDetails */
    private $exportDetailsResource;
    /** @var OrderExportDetailsFactory */
    private $exportDetailsFactory;
    /** @var OrderExportDetailsCollectionFactory */
    private $exportDetailsCollectionFactory;
    /** @var CollectionProcessorInterface */
    private $collectionProcessor;
    /** @var OrderExportDetailsSearchResultsInterfaceFactory */
    private $exportDetailsSearchResultsFactory;

    public function __construct(
        OrderExportDetailsResource $exportDetailsResource,
        OrderExportDetailsFactory $exportDetailsFactory,
        OrderExportDetailsCollectionFactory $exportDetailsCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        OrderExportDetailsSearchResultsInterfaceFactory $exportDetailsSearchResultsFactory
    ) {
        $this->exportDetailsResource = $exportDetailsResource;
        $this->exportDetailsFactory = $exportDetailsFactory;
        $this->exportDetailsCollectionFactory = $exportDetailsCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->exportDetailsSearchResultsFactory = $exportDetailsSearchResultsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(OrderExportDetailsInterface $exportDetails) : OrderExportDetailsInterface
    {
        if (!($exportDetails instanceof AbstractModel)) {
            throw new CouldNotSaveException(__('The implementation of OrderExportDetailsInterface has changed'));
        }

        try {
            $this->exportDetailsResource->save($exportDetails);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $exportDetails;
    }

    /**
     * {@inheritdoc}
     */
    public function getById(int $detailsId): OrderExportDetailsInterface
    {
        /** @var OrderExportDetails $details */
        $details = $this->exportDetailsFactory->create();
        $this->exportDetailsResource->load($details, $detailsId);

        if (!$details->getId()) {
            throw new NoSuchEntityException(__('The order export details could  not be found'));
        }
        return $details;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(OrderExportDetailsInterface $exportDetails): bool
    {
        if (!($exportDetails instanceof AbstractModel)) {
            throw new CouldNotDeleteException(__('The implementation of OrderExportDetailsInterface has changed'));
        }

        try {
            $this->exportDetailsResource->delete($exportDetails);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById(int $detailsId): bool
    {
        return $this->delete($this->getById($detailsId));
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria): OrderExportDetailsSearchResultsInterface
    {
        /** @var OrderExportDetailsCollection $collection */
        $collection = $this->exportDetailsCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var OrderExportDetailsSearchResultsInterface $searchResults */
        $searchResults = $this->exportDetailsSearchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
}

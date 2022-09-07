<?php
declare(strict_types = 1);

namespace SwiftOtter\OrderExport\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsSearchResultsInterface;

interface OrderExportDetailsRepositoryInterface
{
    /**
     * @throws CouldNotSaveException
     */
    public function save(OrderExportDetailsInterface $exportDetails): OrderExportDetailsInterface;

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $detailsId): OrderExportDetailsInterface;

    public function getList(SearchCriteriaInterface $criteria): OrderExportDetailsSearchResultsInterface;

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(OrderExportDetailsInterface $exportDetails): bool;

    /**
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($blockId): bool;
}

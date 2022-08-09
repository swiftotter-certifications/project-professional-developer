<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsSearchResultsInterface;

interface OrderExportDetailsRepositoryInterface
{
    /**
     * @param \SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface|\Magento\Framework\Model\AbstractModel $exportDetails
     * @return \SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(OrderExportDetailsInterface $exportDetails): OrderExportDetailsInterface;

    /**
     * @param int $detailsId
     * @return \SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $detailsId): OrderExportDetailsInterface;

    /**
     * @param \SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface|\Magento\Framework\Model\AbstractModel $exportDetails
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(OrderExportDetailsInterface $exportDetails): bool;

    /**
     * @param int $detailsId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById(int $detailsId): bool;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SwiftOtter\OrderExport\Api\Data\OrderExportDetailsSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): OrderExportDetailsSearchResultsInterface;
}

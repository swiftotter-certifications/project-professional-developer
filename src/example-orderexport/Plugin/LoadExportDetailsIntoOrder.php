<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Plugin;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterfaceFactory;
use SwiftOtter\OrderExport\Api\OrderExportDetailsRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

class LoadExportDetailsIntoOrder
{
    /** @var OrderExportDetailsRepositoryInterface */
    private $exportDetailsRepository;
    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;
    /** @var OrderExportDetailsInterfaceFactory */
    private $exportDetailsFactory;

    public function __construct(
        OrderExportDetailsRepositoryInterface $exportDetailsRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderExportDetailsInterfaceFactory $exportDetailsFactory
    ) {
        $this->exportDetailsRepository = $exportDetailsRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->exportDetailsFactory = $exportDetailsFactory;
    }

    /**
     * @param int $id
     * @return OrderInterface $order
     */
    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $order,
        $id
    ) {
        $this->setExportDetails($order);
        return $order;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $result,
        SearchCriteriaInterface $searchCriteria
    ) {
        foreach ($result->getItems() as $order) {
            $this->setExportDetails($order);
        }
        return $result;
    }

    private function setExportDetails(OrderInterface $order): void
    {
        $extensionAttributes = $order->getExtensionAttributes();

        $exportDetails = $extensionAttributes->getExportDetails();
        if ($exportDetails) {
            return;
        }

        $this->searchCriteriaBuilder->addFilter('order_id', $order->getEntityId());
        $exportDetailsList = $this->exportDetailsRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        if (count($exportDetailsList) > 0) {
            $extensionAttributes->setExportDetails(reset($exportDetailsList));
        } else {
            /** @var OrderExportDetailsInterface $exportDetails */
            $exportDetails = $this->exportDetailsFactory->create();
            $extensionAttributes->setExportDetails($exportDetails);
        }
    }
}

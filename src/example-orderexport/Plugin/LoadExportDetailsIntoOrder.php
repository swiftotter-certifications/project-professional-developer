<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Plugin;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderExtensionInterfaceFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterfaceFactory;
use SwiftOtter\OrderExport\Api\OrderExportDetailsRepositoryInterface;

class LoadExportDetailsIntoOrder
{
    /**
     * @var OrderExtensionInterfaceFactory
     */
    private $extensionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var OrderExportDetailsRepositoryInterface
     */
    private $orderExportDetailsRepository;

    /**
     * @var OrderExportDetailsInterfaceFactory
     */
    private $detailsFactory;

    public function __construct(
        OrderExtensionInterfaceFactory $extension,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderExportDetailsRepositoryInterface $orderExportDetailsRepository,
        OrderExportDetailsInterfaceFactory $detailsFactory
    ) {
        $this->extensionFactory = $extension;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderExportDetailsRepository = $orderExportDetailsRepository;
        $this->detailsFactory = $detailsFactory;
    }

    /**
     * @param OrderInterface $order
     * @return OrderInterface
     */
    public function afterGet(
        OrderRepositoryInterface $subject,
        $order
    ) {
        $this->setExportDetails($order);

        return $order;
    }

    /**
     * @param OrderSearchResultInterface $searchResult
     * @return OrderSearchResultInterface
     */
    public function afterGetList(
        OrderRepositoryInterface $orderRepository,
        $searchResult
    ) {
        foreach ($searchResult->getItems() as $order) {
            $this->setExportDetails($order);
        }

        return $searchResult;
    }

    private function setExportDetails(OrderInterface $order): void
    {
        $extensionAttributes = $order->getExtensionAttributes();

        /** @var OrderExportDetailsInterface $exportDetails */
        $exportDetails = $extensionAttributes->getExportDetails();
        if ($exportDetails) {
            return;
        }

        $exportDetailsList = $this->orderExportDetailsRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter('order_id', $order->getEntityId())
                ->create()
        )->getItems();

        if (count($exportDetailsList) > 0) {
            $extensionAttributes->setExportDetails(reset($exportDetailsList));
        } else {
            /** @var OrderExportDetailsInterface $details */
            $exportDetails = $this->detailsFactory->create();
            $extensionAttributes->setExportDetails($exportDetails);
        }
    }
}

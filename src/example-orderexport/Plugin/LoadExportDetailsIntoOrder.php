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
use SwiftOtter\OrderExport\Model\OrderExportDetailsRepository;

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
     * @var OrderExportDetailsRepository
     */
    private $orderExportDetailsRepository;

    /**
     * @var OrderExportDetailsInterfaceFactory
     */
    private $detailsFactory;

    public function __construct(
        OrderExtensionInterfaceFactory $extension,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderExportDetailsRepository $orderExportDetailsRepository,
        OrderExportDetailsInterfaceFactory $detailsFactory
    ) {
        $this->extensionFactory = $extension;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderExportDetailsRepository = $orderExportDetailsRepository;
        $this->detailsFactory = $detailsFactory;
    }

    public function afterGet(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order
    ) {
        $this->setExtensionAttributes($order);

        return $order;
    }

    public function afterGetList(OrderRepositoryInterface $orderRepository, OrderSearchResultInterface $searchResult)
    {
        foreach ($searchResult->getItems() as $order) {
            $this->setExtensionAttributes($order);
        }

        return $searchResult;
    }

    private function setExtensionAttributes(OrderInterface $order): void
    {
        $extensionAttributes = $order->getExtensionAttributes() ?? $this->extensionFactory->create();

        $details = $this->orderExportDetailsRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter('order_id', $order->getEntityId())
                ->create()
        )->getItems();

        if (count($details)) {
            $extensionAttributes->setExportDetails(reset($details));
        } else {
            /** @var OrderExportDetailsInterface $details */
            $details = $this->detailsFactory->create();
            $extensionAttributes->setExportDetails($details);
        }

        $order->setExtensionAttributes($extensionAttributes);
    }
}

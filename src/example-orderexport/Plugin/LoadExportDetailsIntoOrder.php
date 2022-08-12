<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Plugin;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterfaceFactory;
use SwiftOtter\OrderExport\Api\OrderExportDetailsRepositoryInterface;

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
        $extensionAttributes = $order->getExtensionAttributes();

        $exportDetails = $extensionAttributes->getExportDetails();
        if ($exportDetails) {
            return $order;
        }

        $this->searchCriteriaBuilder->addFilter('order_id', $id);
        $exportDetailsList = $this->exportDetailsRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        if (count($exportDetailsList) > 0) {
            $extensionAttributes->setExportDetails(reset($exportDetailsList));
        } else {
            /** @var OrderExportDetailsInterface $exportDetails */
            $exportDetails = $this->exportDetailsFactory->create();
            $extensionAttributes->setExportDetails($exportDetails);
        }

        return $order;
    }
}

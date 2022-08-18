<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Action;

use Magento\Sales\Api\Data\OrderInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterfaceFactory;
use SwiftOtter\OrderExport\Api\OrderExportDetailsRepositoryInterface;
use SwiftOtter\OrderExport\Model\HeaderData;
use Magento\Framework\Exception\CouldNotSaveException;

class SaveExportDetailsToOrder
{
    /** @var OrderExportDetailsInterfaceFactory */
    private $exportDetailsFactory;
    /** @var OrderExportDetailsRepositoryInterface */
    private $exportDetailsRepository;

    public function __construct(
        OrderExportDetailsInterfaceFactory $exportDetailsFactory,
        OrderExportDetailsRepositoryInterface $exportDetailsRepository
    ) {
        $this->exportDetailsFactory = $exportDetailsFactory;
        $this->exportDetailsRepository = $exportDetailsRepository;
    }

    /**
     * @throws CouldNotSaveException
     */
    public function execute(OrderInterface $order, HeaderData $headerData, array $results): void
    {
        $orderExts = $order->getExtensionAttributes();
        $exportDetails = $orderExts->getExportDetails();

        if (!$exportDetails) {
            /** @var OrderExportDetailsInterface $exportDetails */
            $exportDetails = $this->exportDetailsFactory->create();
            $orderExts->setExportDetails($exportDetails);
        }
        
        $exportDetails->setOrderId((int)$order->getEntityId());

        $success = $results['success'] ?? false;
        if ($success) {
            $time = (new \DateTime())->setTimezone(new \DateTimeZone('UTC'));
            $exportDetails->setExportedAt($time);
        }

        if ($merchantNotes = $headerData->getMerchantNotes()) {
            $exportDetails->setMerchantNotes($merchantNotes);
        }
        if ($shipOn = $headerData->getShipDate()) {
            $exportDetails->setShipOn($shipOn);
        }

        $this->exportDetailsRepository->save($exportDetails);
    }
}

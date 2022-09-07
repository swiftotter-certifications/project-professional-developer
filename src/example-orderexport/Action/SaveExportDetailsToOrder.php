<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc. 1/1/20
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Action;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\OrderRepository;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterfaceFactory;
use SwiftOtter\OrderExport\Api\OrderExportDetailsRepositoryInterface;
use SwiftOtter\OrderExport\Model\HeaderData;

class SaveExportDetailsToOrder
{
    /** @var OrderExportDetailsRepositoryInterface */
    private $repository;

    /** @var OrderRepository */
    private $orderRepository;

    /** @var OrderExportDetailsInterfaceFactory */
    private $exportDetailsFactory;

    public function __construct(
        OrderRepository $orderRepository,
        OrderExportDetailsRepositoryInterface $repository,
        OrderExportDetailsInterfaceFactory $exportDetailsFactory
    ) {
        $this->repository = $repository;
        $this->orderRepository = $orderRepository;
        $this->exportDetailsFactory = $exportDetailsFactory;
    }

    /**
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function execute(int $orderId, HeaderData $headerData, array $results): void
    {
        $order = $this->orderRepository->get($orderId);
        $orderExts = $order->getExtensionAttributes();

        /** @var OrderExportDetailsInterface $details */
        $details = $orderExts->getExportDetails();
        if (!$details) {
            $details = $this->exportDetailsFactory->create();
            $details->setOrderId((int)$order->getEntityId());
            $orderExts->setExportDetails($details);
        }

        if (isset($results['success']) && $results['success'] === true) {
            $details->setExportedAt((new \DateTime())->setTimezone(new \DateTimeZone('UTC')));
        }

        if ($merchantNotes = $headerData->getMerchantNotes()) {
            $details->setMerchantNotes($merchantNotes);
        }
        if ($shipDate = $headerData->getShipDate()) {
            $details->setShipOn($shipDate);
        }

        $this->repository->save($details);
    }
}

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
use SwiftOtter\OrderExport\Api\OrderExportDetailsRepositoryInterface;
use SwiftOtter\OrderExport\Model\HeaderData;

class SaveExportDetailsToOrder
{
    /**
     * @var OrderExportDetailsRepositoryInterface
     */
    private $repository;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    public function __construct(
        OrderRepository $orderRepository,
        OrderExportDetailsRepositoryInterface $repository
    ) {
        $this->repository = $repository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function execute(int $orderId, HeaderData $headerData, array $results): void
    {
        $order = $this->orderRepository->get($orderId);
        $details = $order->getExtensionAttributes()->getExportDetails();

        if (isset($results['success']) && $results['success'] === true) {
            $details->setExportedAt((new \DateTime())->setTimezone(new \DateTimeZone('UTC')));
        }

        $details->setOrderId($orderId);
        $details->setMerchantNotes($headerData->getMerchantNotes());
        if ($shipDate = $headerData->getShipDate()) {
            $details->setShipOn($shipDate);
        }

        $this->repository->save($details);
    }
}

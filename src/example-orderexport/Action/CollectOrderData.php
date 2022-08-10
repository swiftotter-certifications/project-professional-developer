<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Action;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use SwiftOtter\OrderExport\Model\HeaderData;

class CollectOrderData
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function execute(int $orderId, HeaderData $headerData): array
    {
        $order = $this->orderRepository->get($orderId);

        $output = [];
        // TODO Accumulate output
        return $output;
    }
}

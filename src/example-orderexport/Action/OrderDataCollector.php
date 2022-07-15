<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc. 12/28/19
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Action;

use Magento\Sales\Api\OrderRepositoryInterface;
use SwiftOtter\OrderExport\Api\OrderDataCollectorInterface;
use SwiftOtter\OrderExport\Model\HeaderData;

class OrderDataCollector
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var OrderDataCollectorInterface[]
     */
    private $collectors;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        array $collectors = []
    ) {
        $this->orderRepository = $orderRepository;
        $this->collectors = $collectors;
    }

    public function execute(int $orderId, HeaderData $headerData): array
    {
        $order = $this->orderRepository->get($orderId);
        $output = [];

        foreach ($this->collectors as $collector) {
            $output = array_merge($output, $collector->collect($order, $headerData));
        }

        return $output;
    }
}

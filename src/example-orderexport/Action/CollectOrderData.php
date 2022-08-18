<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Action;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use SwiftOtter\OrderExport\Api\OrderDataCollectorInterface;
use SwiftOtter\OrderExport\Model\HeaderData;

class CollectOrderData
{
    /** @var OrderDataCollectorInterface[] */
    private $collectors;

    public function __construct(
        array $collectors = []
    ) {
        $this->collectors = $collectors;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function execute(OrderInterface $order, HeaderData $headerData): array
    {
        $output = [];
        foreach ($this->collectors as $collector) {
            $output = array_merge_recursive($output, $collector->collect($order, $headerData));
        }

        return $output;
    }
}

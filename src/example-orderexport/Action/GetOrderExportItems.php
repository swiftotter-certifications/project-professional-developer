<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Action;

use Magento\Sales\Api\Data\OrderInterface;

class GetOrderExportItems
{
    /** @var array */
    private $allowedTypes;

    public function __construct(
        array $allowedTypes = []
    ) {
        $this->allowedTypes = $allowedTypes;
    }

    public function execute(OrderInterface $order): array
    {
        $items = [];
        foreach ($order->getItems() as $orderItem) {
            if (in_array($orderItem->getProductType(), $this->allowedTypes)) {
                $items[] = $orderItem;
            }
        }
        return $items;
    }
}

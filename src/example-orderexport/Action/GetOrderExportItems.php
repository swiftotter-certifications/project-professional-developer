<?php
declare(strict_types = 1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Action;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Sales\Api\Data\OrderItemInterface;

class GetOrderExportItems
{
    /**
     * @var array
     */
    private $allowedTypes;

    public function __construct(
        array $allowedTypes = []
    ) {
        $this->allowedTypes = $allowedTypes;
    }

    /**
     * @return OrderItemInterface[]
     */
    public function execute(OrderInterface $order): array
    {
        $items = [];
        foreach ($order->getItems() as $item) {
            if (in_array($item->getProductType(), $this->allowedTypes)) {
                $items[] = $item;
            }
        }
        return $items;
    }
}

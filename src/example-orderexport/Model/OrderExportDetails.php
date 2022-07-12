<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Model;

use Magento\Framework\Model\AbstractModel;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface;

class OrderExportDetails extends AbstractModel implements OrderExportDetailsInterface
{
    protected function _construct()
    {
        $this->_init(\SwiftOtter\OrderExport\Model\ResourceModel\OrderExportDetails::class);
    }

    public function getOrderId(): int
    {
        return (int)$this->getData('order_id');
    }

    public function setOrderId(int $orderId): OrderExportDetailsInterface
    {
        $this->setData('order_id', $orderId);
        return $this;
    }

    public function getShipOn(): ?\DateTime
    {
        $dateStr = $this->getData('ship_on');
        return ($dateStr) ? new \DateTime($dateStr) : null;
    }

    public function setShipOn(\DateTime $shipOn): OrderExportDetailsInterface
    {
        $this->setData('ship_on', $shipOn);
        return $this;
    }

    public function getExportedAt(): \DateTime
    {
        return new \DateTime($this->getData('exported_at'));
    }

    public function setExportedAt(\DateTime $exportedAt): OrderExportDetailsInterface
    {
        $this->setData('exported_at', $exportedAt);
        return $this;
    }

    public function hasBeenExported(): bool
    {
        return (bool)$this->getData('exported_at');
    }

    public function getMerchantNotes(): string
    {
        return (string)$this->getData('merchant_notes');
    }

    public function setMerchantNotes(string $merchantNotes): OrderExportDetailsInterface
    {
        $this->setData('merchant_notes', $merchantNotes);
        return $this;
    }
}

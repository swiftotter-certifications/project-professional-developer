<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use SwiftOtter\OrderExport\Action\AttachExpeditedExportNote;

class AttachExpeditedExportNoteObserver implements ObserverInterface
{
    /** @var AttachExpeditedExportNote */
    private $attachExpeditedExportNote;

    public function __construct(
        AttachExpeditedExportNote $attachExpeditedExportNote
    ) {
        $this->attachExpeditedExportNote = $attachExpeditedExportNote;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var OrderInterface $order */
        $order = $observer->getEvent()->getData('order');
        if (!$order) {
            return;
        }

        $this->attachExpeditedExportNote->execute($order);
    }
}
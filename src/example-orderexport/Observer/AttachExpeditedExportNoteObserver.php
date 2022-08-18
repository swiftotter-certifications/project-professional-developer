<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;
use SwiftOtter\OrderExport\Action\AttachExpeditedExportNote;

class AttachExpeditedExportNoteObserver implements ObserverInterface
{
    /** @var AttachExpeditedExportNote */
    private $attachExpeditedExportNote;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        AttachExpeditedExportNote $attachExpeditedExportNote,
        LoggerInterface $logger
    ) {
        $this->attachExpeditedExportNote = $attachExpeditedExportNote;
        $this->logger = $logger;
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

        try {
            $this->attachExpeditedExportNote->execute($order);
        } catch (LocalizedException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}

<?php
declare(strict_types = 1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Model\Layout\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Cms\Block\BlockByIdentifier;
use Magento\Framework\View\LayoutInterface;

class FulfillmentNoticeProcessor implements LayoutProcessorInterface
{
    /**
     * @var LayoutInterface
     */
    private $layout;

    public function __construct(
        LayoutInterface $layout
    ) {
        $this->layout = $layout;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        /** @var BlockByIdentifier $fulfillmentBlock */
        $fulfillmentBlock = $this->layout->createBlock(BlockByIdentifier::class);
        $fulfillmentBlock->setData('identifier', 'fulfillment-notice');

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['fulfillmentNotice']['config']['noticeContent'] = $fulfillmentBlock->toHtml();
        return $jsLayout;
    }
}

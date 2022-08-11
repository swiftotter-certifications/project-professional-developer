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
use SwiftOtter\OrderExport\Model\Config;

class FulfillmentNoticeProcessor implements LayoutProcessorInterface
{
    /**
     * @var LayoutInterface
     */
    private $layout;
    /** @var Config */
    private $config;

    public function __construct(
        LayoutInterface $layout,
        Config $config
    ) {
        $this->layout = $layout;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        if (!$this->config->isEnabled()) {
            return $jsLayout;
        }

        /** @var BlockByIdentifier $fulfillmentBlock */
        $fulfillmentBlock = $this->layout->createBlock(BlockByIdentifier::class);
        $fulfillmentBlock->setData('identifier', 'fulfillment-notice');

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['fulfillmentNotice']['config']['noticeContent'] = $fulfillmentBlock->toHtml();
        return $jsLayout;
    }
}

<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Model\Layout\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\View\LayoutInterface;
use SwiftOtter\OrderExport\Model\Config;
use Magento\Cms\Block\BlockByIdentifier;

class FulfillmentNoticeProcessor implements LayoutProcessorInterface
{
    /** @var Config */
    private $config;
    /** @var LayoutInterface */
    private $layout;

    public function __construct(
        Config $config,
        LayoutInterface $layout
    ) {
        $this->config = $config;
        $this->layout = $layout;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        if (!$this->config->isEnabled()) {
            return $jsLayout;
        }

        $fulfillmentBlock = $this->layout->createBlock(BlockByIdentifier::class);
        $fulfillmentBlock->setData('identifier', 'fulfillment-notice');
        $fulfillmentBlockOutput = $fulfillmentBlock->toHtml();

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['fulfillmentNotice']['config']['noticeContent'] = $fulfillmentBlockOutput;

        return $jsLayout;
    }
}

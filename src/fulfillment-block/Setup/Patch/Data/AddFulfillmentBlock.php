<?php
declare(strict_types = 1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\FulfillmentBlock\Setup\Patch\Data;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Api\Data\BlockInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddFulfillmentBlock implements DataPatchInterface
{
    const BLOCK_ID = 'fulfillment-notice';

    private $blockContent = <<<END
<style>
#html-body [data-pb-style=LORUG1F]{justify-content:flex-start;display:flex;flex-direction:column;background-color:#eaeaea;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;padding:20px}
</style>
<div data-content-type="row" data-appearance="contained" data-element="main">
    <div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="LORUG1F">
        <h4 data-content-type="heading" data-appearance="default" data-element="main">Note</h4>
        <div data-content-type="text" data-appearance="default" data-element="main">
            <p>We strive to ship your order as soon as possible, but fullfilment depends on order volume and customization time. We cannot guarantee a specific fulfillment timeline.</p>
        </div>
    </div>
</div>
END;


    /**
     * @var BlockInterfaceFactory
     */
    private $blockFactory;
    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        BlockInterfaceFactory $blockFactory,
        BlockRepositoryInterface $blockRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    public function apply()
    {
        $this->searchCriteriaBuilder->addFilter('identifier', self::BLOCK_ID);
        $blocks = $this->blockRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        if (count($blocks) > 0) {
            return;
        }

        $block = $this->blockFactory->create();
        $block->setTitle('Fulfillment Notification')
            ->setIdentifier(self::BLOCK_ID)
            ->setContent($this->blockContent)
            ->setIsActive(true)
            ->setData('store_id', [0]);
        $this->blockRepository->save($block);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}

<?php
declare(strict_types = 1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Action;

use Magento\Framework\Api\SearchCriteriaBuilder;
use SwiftOtter\OrderExport\Api\OrderExportDetailsRepositoryInterface;

class ArchiveExports
{
    const EXPIRATION_DAYS = 30;

    /**
     * @var OrderExportDetailsRepositoryInterface
     */
    private $orderExportDetailsRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        OrderExportDetailsRepositoryInterface $orderExportDetailsRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderExportDetailsRepository = $orderExportDetailsRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @throws \Exception
     */
    public function execute(): void
    {
        $expDate = (new \DateTime())
            ->setTimezone(new \DateTimeZone('UTC'))
            ->sub(new \DateInterval('P' . self::EXPIRATION_DAYS . 'D'));

        $this->searchCriteriaBuilder->addFilter('archived', 0)
            ->addFilter('exported_at', $expDate->format('Y-m-d H:i:s'), 'lt');
        $archiveOrders = $this->orderExportDetailsRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        foreach ($archiveOrders as $order) {
            $order->setArchived(true);
            $this->orderExportDetailsRepository->save($order);
        }
    }
}

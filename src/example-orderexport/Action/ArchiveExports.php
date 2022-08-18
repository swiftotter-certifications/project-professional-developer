<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Action;

use Magento\Framework\Api\SearchCriteriaBuilder;
use SwiftOtter\OrderExport\Api\OrderExportDetailsRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;

class ArchiveExports
{
    const EXPIRATION_DAYS = 30;
    
    /** @var OrderExportDetailsRepositoryInterface */
    private $exportDetailsRepository;
    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    public function __construct(
        OrderExportDetailsRepositoryInterface $exportDetailsRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->exportDetailsRepository = $exportDetailsRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @throws CouldNotSaveException
     */
    public function execute(): void
    {
        $expDate = (new \DateTime())
            ->setTimezone(new \DateTimeZone('UTC'))
            ->sub(new \DateInterval('P' . self::EXPIRATION_DAYS . 'D'));

        $this->searchCriteriaBuilder->addFilter('archived', 0)
            ->addFilter('exported_at', $expDate->format('Y-m-d H:i:s'), 'lt');
        $archiveOrders = $this->exportDetailsRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        foreach ($archiveOrders as $archiveOrder) {
            $archiveOrder->setArchived(true);
            $this->exportDetailsRepository->save($archiveOrder);
        }
    }
}

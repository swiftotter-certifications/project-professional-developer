<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Cron;

use SwiftOtter\OrderExport\Action\ArchiveExports as ArchiveExportsAction;
use Magento\Framework\Exception\CouldNotSaveException;

class ArchiveExports
{
    /** @var ArchiveExportsAction */
    private $archiveExports;

    public function __construct(
        ArchiveExportsAction $archiveExports
    ) {
        $this->archiveExports = $archiveExports;
    }

    /**
     * @throws CouldNotSaveException
     */
    public function execute(): void
    {
        $this->archiveExports->execute();
    }
}

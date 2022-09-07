<?php
declare(strict_types = 1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Cron;

use SwiftOtter\OrderExport\Action\ArchiveExports as ArchiveExportsAction;

class ArchiveExports
{
    /**
     * @var ArchiveExportsAction
     */
    private $archiveExportsAction;

    public function __construct(
        ArchiveExportsAction $archiveExportsAction
    ) {
        $this->archiveExportsAction = $archiveExportsAction;
    }

    /**
     * @throws \Exception
     */
    public function execute(): void
    {
        $this->archiveExportsAction->execute();
    }
}

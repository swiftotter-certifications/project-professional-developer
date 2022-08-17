<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Cron;

use SwiftOtter\OrderExport\Action\ArchiveExports as ArchiveExportsAction;

class ArchiveExports
{
    /** @var ArchiveExportsAction */
    private $archiveExports;

    public function __construct(
        ArchiveExportsAction $archiveExports
    ) {
        $this->archiveExports = $archiveExports;
    }

    public function execute(): void
    {
        $this->archiveExports->execute();
    }
}

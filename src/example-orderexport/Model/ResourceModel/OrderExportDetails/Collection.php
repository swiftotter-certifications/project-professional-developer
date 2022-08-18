<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Model\ResourceModel\OrderExportDetails;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SwiftOtter\OrderExport\Model\OrderExportDetails;
use SwiftOtter\OrderExport\Model\ResourceModel\OrderExportDetails as OrderExportDetailsResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(OrderExportDetails::class, OrderExportDetailsResource::class);
    }
}

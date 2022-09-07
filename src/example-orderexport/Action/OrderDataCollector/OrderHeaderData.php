<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Action\OrderDataCollector;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderAddressRepositoryInterface;
use SwiftOtter\OrderExport\Api\OrderDataCollectorInterface;

class OrderHeaderData implements OrderDataCollectorInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var OrderAddressRepositoryInterface
     */
    private $orderAddressRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        OrderAddressRepositoryInterface $orderAddressRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function collect(OrderInterface $order, \SwiftOtter\OrderExport\Model\HeaderData $headerData): array
    {
        $address = $this->getShippingAddressFor($order);

        $output = [
            'id' => $order->getIncrementId(),
            'currency' => $order->getBaseCurrencyCode(),
            'discount' => $order->getBaseDiscountAmount(),
            'total' => $order->getBaseGrandTotal()
        ];

        if ($address) {
            $output['shipping'] = [
                'name' => $address->getFirstname() . ' ' . $address->getLastname(),
                'address' => $address->getStreet() ? implode(', ', $address->getStreet()) : '',
                'city' => $address->getCity(),
                'state' => $address->getRegionCode(),
                'postcode' => $address->getPostcode(),
                'country' => $address->getCountryId(),
                'amount' => $order->getBaseShippingAmount(),
                'method' => $order->getShippingDescription(),
            ];
        }

        return $output;
    }

    private function getShippingAddressFor(OrderInterface $order): ?OrderAddressInterface
    {
        $addresses = $this->orderAddressRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter('parent_id', $order->getEntityId())
                ->addFilter('address_type', 'shipping')
                ->create()
        );

        if (!count($addresses->getItems())) {
            return null;
        } else {
            $items = $addresses->getItems();
            return reset($items);
        }
    }
}

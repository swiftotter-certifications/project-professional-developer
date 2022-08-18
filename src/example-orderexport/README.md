# SwiftOtter_OrderExport

NOTE: This package is a complete example of the OrderExport module and is intended for reference. Don't install it if
you intend to follow along with the training course and build the module yourself.

Export orders to the merchant ERP.

## Store Configuration

At Stores > Configuration > Sales > Sales > Order Export:

* **Enabled**
* **API URL:** API endpoint URL for export
* **API Token:** Security token to use in export API request
* **Expedited SKUs:** List of expedited SKUs to trigger specialized merchant note
* **Expedited SKUs Merchant Note:** The merchant note to include when an order matches expedited SKUs

## CLI Command

Run the following CLI command to export an order:

```
bin/magento order-export:run <order_id>
```

### Arguments

* **<order_id>** - Numerical ID of the order to export

### Options

* **--notes** - The "Merchant Notes"
* **--ship-date** - The expected shipping date

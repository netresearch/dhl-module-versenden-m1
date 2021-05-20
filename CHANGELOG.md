# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 1.9.2 - 2021-05-20

### Fixed

- Consider Visual Check of Age service setting during automatic shipment creation. 

## 1.9.1 - 2021-02-12

### Removed

- Removed tracking pixel from success checkout. 

## 1.9.0 - 2020-12-14

### Added

- Possibility to configure an alternative notification email for parcel outlet routing service.

### Changed

- Rename Wunschpaket (Preferred Delivery) services to align with official DHL naming.
- Transmit shipping cost for label requests with customs declaration.


## 1.8.0 - 2020-06-22

### Added

- The shipping product "Merchandise Shipment" (Warenpost) can now be booked.
- The automatic shipment creation configuration allows to set a default shipping product.

### Removed

- Austria is no longer supported as shipping origin.


## 1.7.0 - 2020-05-08

### Changed

- Service defaults for automatic shipment creation are now configured via _Shipment Defaults_.

### Removed

- The value-added service _Preferred Time_ is no longer offered in checkout.

### Fixed

- Configured service defaults are now preselected in packaging popup.


## 1.6.0 - 2020-01-15

### Added

- The value-added service _Parcel Outlet Routing_ can now be booked with shipping labels.
- PHP 7.2 support

### Changed

- Update to BCS v3

### Removed

- New orders for shipping origin Austria can no longer be placed. Processing of orders is still possible.

### Fixed

- Incorrect order amount on customs declaration
- fix service loading
- read return shipment contact data from correct scope
- Fix sidebar service selection


## 1.5.1 - 2018-11-28

### Changed

- Application credentials for parcel management

### Fixed

- Credential usage
- Make cache key dependent on endpoint
- Avoid infinite loop when calculating start date
- Translation configuration


## 1.5.0 - 2018-09-05

### Added

- Connection to Parcel Management API for retrieving available services 
- Tracking Pixel to success checkout working once every 30 days and configuration option for en- or disable 
- Configuration options of handling fee & text for combined Preferred Day and Time
- Configuration option for excluded drop-off days
- 'none' option to checkout services
- Blacklist validation for Preferred Location and Neighbour
- Display included service fees in delivery costs in checkout sidebar

### Changed

- Configuration scopes
- Preferred Neighbour and Preferred Location are now exclusive
- Display DHL Service box only for shipping inside Germany
- No provision of Preferred Day for orders containing backordered items


## 1.4.0 - 2018-07-12

### Added

- Allow using placeholders in bank reference fields for Cash On Delivery
- Added configuration option to transmit the customer's phone number to DHL
- Added compatibility to the module *Amazon Pay for Europe*

### Changed

- Limit max number of shipments to 50 per request to prevent invalid XML error

### Known issues

- If more than one shipment method exists, the DHL services are not displayed immediately.
  They become visible when clicking *Edit address* or when the page is reloaded. This will
  be fixed in future versions.


## 1.3.1 - 2018-04-25

### Added

- Information about Shipment Overview feature added to documentation
- Various documentation improvements and clarifications
- Expanded help texts on shipping method configuration page

### Changed

- The maximum length of Street numbers is now 7 characters instead of 5

### Fixed

- Insurance and CoD amounts of over 1000 units are now possible
- DHL status icons in order grid are now properly updated by Autocreate
- Checkout now does not show a DHL section if no services are available.
- Order emails sent from Autocreate now include the shipment ID
- Magento will no longer crash when changing the display currency in checkout
- Added some missing german translation strings


## 1.3.0 - 2018-01-19

### Added

- Display shipping label status in orders grid

### Fixed

- Code style improvements for Magento Marketplace


## 1.2.0 - 2017-12-15

### Added

- Create shipping labels via order grid mass action
- Encrypt API password in database
- Send shipment confirmation email during cron autocreate

### Fixed

- Remove receiver email address from request if parcel announcement service is disabled
- Fall back to order email address if it is not available at shipping address
- Improve address split for Austrian street numbers
- Re-calculate service fee on shipping method or service selection changes in checkout
- Consider Sundays in preferred day service options calculation
- Log webservice errors during cron autocreate


## 1.1.1 - 2017-09-27

### Fixed

- Improve autoloading of namespaced classes
- No longer terminate cron on shipment validation errors, continue processing
- Apply correct unit of measure for item weight in export declarations
- Display service validation errors in checkout that remained hidden under certain circumstances


## 1.1.0 - 2017-05-10

### Added

- Demand fees for Preferred Day / Preferred Time checkout services

### Fixed

- Missing array key for preferred day
- Fix authentication errors not being shown
- Fix label creation for partial shipments
- Make participation number required
- DB prefix will now be recognized


## 1.0.0 - 2016-10-17

- Initial release

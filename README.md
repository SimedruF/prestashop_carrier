# AHS Carrier ZIP Filter

A PrestaShop module that filters available shipping carriers based on customer postal codes using prefix-based rules.

## Description

This module allows you to restrict which shipping carriers are displayed to customers during checkout based on their delivery postal code. You can configure allowed postal code prefixes for each carrier, giving you fine-grained control over carrier availability by geographic region.

## Features

- **Prefix-based filtering**: Define postal code prefixes for each carrier
- **Multiple prefixes support**: Set multiple allowed prefixes per carrier (comma or semicolon separated)
- **Flexible configuration**: Leave empty for no restrictions on specific carriers
- **Automatic filtering**: Seamlessly filters carriers during checkout based on delivery address
- **Easy management**: Simple admin interface to configure all carriers at once

## Requirements

- PrestaShop 1.7.0.0 or higher
- PHP 5.6 or higher

## Installation

1. Download or clone this repository
2. Upload the `ahscarrierzip` folder to your PrestaShop `/modules/` directory
3. Go to **Modules > Module Manager** in your PrestaShop back office
4. Search for "Carrier ZIP filter"
5. Click **Install**

## Configuration

1. After installation, click **Configure** on the module
2. You'll see a table listing all your carriers
3. For each carrier, enter the allowed postal code prefixes in the input field:
   - Single prefix: `550` (allows all postcodes starting with 550)
   - Multiple prefixes: `550,551` or `550;551` (allows postcodes starting with 550 or 551)
   - Empty field: no restrictions (carrier available for all postal codes)
4. Click **Save** to apply your settings

## How It Works

The module uses the `hookFilterCarrierList` hook to filter the carrier list during checkout:

1. When a customer enters their delivery address with a postal code
2. The module checks each carrier's configured prefix rules
3. If a carrier has prefixes configured, the customer's postal code must match at least one prefix
4. Carriers that don't match are hidden from the checkout carrier selection
5. Carriers with no configured prefixes remain visible for all postal codes

## Example Use Cases

- **Regional carriers**: Limit a local courier to specific city postal codes (e.g., `550` for Sibiu, Romania)
- **Express services**: Restrict premium carriers to major urban areas
- **Zone-based shipping**: Configure different carriers for different geographic zones
- **Cost optimization**: Show specific carriers only in regions where they're most efficient

## Technical Details

- **Module name**: `ahscarrierzip`
- **Version**: 1.0.0
- **Author**: Automatic House Systems SRL
- **Category**: Shipping & Logistics
- **Hook used**: `filterCarrierList`

## Configuration Storage

The module stores configuration in PrestaShop's `ps_configuration` table with keys in the format:
```
AHSCZ_PREFIXES_{carrier_id}
```

All configurations are automatically cleaned up on module uninstallation.

## Support

For issues, questions, or feature requests, please contact:
- **Company**: Automatic House Systems SRL
- **Module**: AHS Carrier ZIP Filter

## License

Copyright © 2025 Automatic House Systems SRL. All rights reserved.

## Changelog

### Version 1.0.0
- Initial release
- Basic prefix-based postal code filtering
- Admin configuration interface
- Support for multiple prefixes per carrier

# Blackstone WooCommerce Payment Gateway Plugin

A Blackstone WooCommerce payment gateway plugin that allows users to save their card information for future purchases and select saved cards during checkout.

## Features

- Save payment methods for future transactions.
- Display saved cards during checkout for easy selection.
- Support for new card payments and tokenized payments using the [BMSpay](https://documentation.bmspay.com/index.html) API.
- Admin settings page to configure gateway options.

## Installation

### Requirements

- WordPress 5.0+
- WooCommerce 4.0+
- PHP 7.2+
- [Composer](https://getcomposer.org/) (for dependency management)

### Steps

1. Clone or download this repository to your WordPress plugin directory:
    ```bash
    git clone https://github.com/your-username/blackstone-woocommerce-gateway.git
    ```
2. Navigate to the plugin directory:
    ```bash
    cd wp-content/plugins/blackstone-woocommerce-gateway
    ```
3. Install dependencies via Composer:
    ```bash
    composer install
    ```
4. Activate the plugin from the WordPress Admin Panel:
    - Go to `Plugins` -> `Installed Plugins`.
    - Find "Blackstone Merchant Payment Gateway" and click "Activate."

## Configuration

1. In the WordPress Admin Panel, go to `WooCommerce` -> `Settings` -> `Payments`.
2. Locate "Blackstone Merchant Payment Gateway" in the list of payment methods.
3. Click "Manage" to configure the following settings:
    - **Enable/Disable**: Toggle the payment gateway on or off.
    - **Title**: The title of the payment gateway displayed during checkout.
    - **Description**: A short description of the payment gateway shown on the checkout page.
    - **Username**: Your BMSpay API username.
    - **MID**: Merchant ID provided by BMSpay.
    - **CID**: Customer ID provided by BMSpay.
    - **App Type**: Application type for BMSpay.
    - **App Key**: The application key for BMSpay.
    - **Test Mode**: Toggle between test and live modes.

## Usage

### Checkout Process

1. **Saved Payment Methods**: During checkout, customers will see their saved cards listed as options. They can select a saved card to make a payment or choose to add a new card.
2. **New Card Payments**: If the customer selects "Use a new payment method," they can enter their card details and proceed with the payment.

### Admin Panel

1. **View Orders**: In the WooCommerce Orders section, admins can view payment details and transaction statuses for orders processed through the custom gateway.

### Contributing

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-branch-name`).
3. Commit your changes (`git commit -am 'Add new feature'`).
4. Push to the branch (`git push origin feature-branch-name`).
5. Create a new Pull Request.

### Issues

If you encounter any issues or bugs, please open an issue on [GitHub](https://github.com/reykel/bmsPay_woocommerce_plugin/issues).

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.

## Support

For support or questions, please email [rhpalacios66@gmail.com](mailto:rhpalacios66@gmail.com).



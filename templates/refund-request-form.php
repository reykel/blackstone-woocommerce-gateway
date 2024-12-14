<?php
if (!defined('ABSPATH')) {
    exit;
}

$order = wc_get_order($order_id);

if ($order) : ?>
    <h2><?php _e('Refund Request', 'blackstone-refund-plugin'); ?></h2>
    <form method="post">
        <p>
            <label for="refund_card_number"><?php _e('Enter Credit Card Number:', 'blackstone-refund-plugin'); ?></label>
            <input type="text" name="refund_card_number" required />
        </p>
        <p>
            <input type="submit" name="submit_refund_request" value="<?php _e('Request Refund', 'blackstone-refund-plugin'); ?>" />
        </p>
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
    <script>
        jQuery(document).ready(function($) {
            $("input[name='refund_card_number']").inputmask("9999 9999 9999 9999");
        });
    </script>
<?php else : ?>
    <p><?php _e('Invalid order ID.', 'blackstone-refund-plugin'); ?></p>
<?php endif; ?>

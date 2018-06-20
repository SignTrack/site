<?php

global $email_user_pass;
$email_user_pass = "";



add_action('woocommerce_thankyou','my_woo_email');

function my_woo_email($order_id){
    global $email_user_pass;
    $order = new WC_Order( $order_id );
    $fname = $order->billing_first_name;
    $lname = $order->billing_last_name;
    $phone = $order->billing_phone;
    $phone = signtrack_formatPhoneNumber($phone);
    $email = $order->billing_email;
    $package_name = '';
    $qty = 1;
    $sign_limit = 0;

    $items = $order->get_items();
    if (count($items) > 1) {
        // TODO Error
        echo '<div class="error">There was an issue, please contact us as soon as possible.</div>';
    }
    foreach ($items as $item) {
        $package_name = strtolower($item['name']);
        $package_name = str_replace(' ', '-', $package_name);

        $qty = $item['qty'];
    }

    switch ($package_name) {
    case 'local-1':
        $sign_limit = 500;
        break;
    case 'local-2':
        $sign_limit = 1500;
        break;
    case 'district-1':
        $sign_limit = 2500;
        break;
    case 'district-2':
        $sign_limit = 5000;
        break;
    case 'statewide':
        $sign_limit = 25000;
        break;
    case 'package-6':
        $sign_limit = 100000;
        break;
    case 'small-package':
        $sign_limit = 1500;
        break;
    case 'large-package':
        $sign_limit = 5000;
        break;
    case 'local-package':
        $sign_limit = 1500;
        $qty = $qty * 4;
        break;
    case 'district-package':
        $sign_limit = 5000;
        $qty = $qty * 12;
        break;
    case 'state-package':
        $sign_limit = 10000;
        $qty = $qty * 12;
        break;
    }

    //dump($order->get_items());

    $url = "https://signtrackapp.com/manage/campaign/handler/format/html";
    // $url = 'http://singtrack.loc';
    $body = array(
        'email' => $email,
        'fname' => $fname,
        'lname' => $lname,
        'phone' => $phone,
        'sign_limit' => $sign_limit,
        'package_name' => $package_name,
        'qty' => $qty,
        'type' => 'setup'
    );
    $args = array(
        'timeout' => 5,
        'body' => $body,
    );

    $response = wp_remote_post( $url, $args );

    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        echo "Something went wrong, we are trying to retrieve your password. (" . $error_message . ')';
        ?>
        <div class="new-user-response">
            <h3>Login Page</h3>
            <p>Username: <?php echo $email; ?></p>
            <p>Password: <span class="signtrack-password"><i class="fa fa-spinner fa-spin"></i></span></p>
            <p><a href=" https://signtrackapp.com/manage" class="button button-secondary">Login</a></p>
        </div>
        <!-- // <script>
        // jQuery(function() {
        //     setupCampaign(
        //         "<?php //echo $email; ?>",
        //         "<?php //echo $fname; ?>",
        //         "<?php //echo $lname; ?>",
        //         "<?php //echo $phone; ?>",
        //         "<?php //echo $package_name; ?>",
        //         "<?php// echo $sign_limit; ?>"
        //     );
        // });
        // </script> -->
        <?php
    }
    else {
        ob_start();
        ?>
        <div class="new-user-response">
            <h3>Login Page</h3>
            <p>Username: <?php echo $email; ?></p>
            <p>Password: <?php echo $response['body']; ?></p>
            <a href=" http://signtrackapp.com/manage" class="button button-primary" target="_BLANK_">Login Now!</a>
        </div>
        <?php
        $email_user_pass = ob_get_contents();
        ob_end_clean();
        echo $email_user_pass;
        add_action( 'woocommerce_email_customer_details', 'woo_custom_return_user_pass');

        add_action( 'woocommerce_email_before_order_table', 'woo_custom_return_user_pass');

        add_action( 'woocommerce_order_details_after_order_table', 'woo_custom_return_user_pass');
    }
}

function woo_custom_return_user_pass() {
    global $email_user_pass;
    echo $email_user_pass;
}


add_filter( 'woocommerce_add_to_cart', 'woocustom_add_to_cart', 0 );

function woocustom_add_to_cart(  ) {
    global $woocommerce;


    $prodId = (int)$_POST["add-to-cart"];
    $prodQty = (int)$_POST["quantity"];

    // Get the total of items for each product in cart
    $cartQty = $woocommerce->cart->get_cart_item_quantities();
    //dump($cartQty);
    // if ($cartQty[$prodId] != 1 || count($cartQty) > 1) {
    //     $woocommerce->cart->empty_cart();
    //     $woocommerce->cart->add_to_cart($prodId,$prodQty);
    // }

    if (count($cartQty) > 1) {
        $woocommerce->cart->empty_cart();
        $woocommerce->cart->add_to_cart($prodId,$prodQty);
    }


}






function signtrack_formatPhoneNumber($s) {
    $rx = "/
        (1)?\D*     # optional country code
        (\d{3})?\D* # optional area code
        (\d{3})\D*  # first three
        (\d{4})     # last four
        (?:\D+|$)   # extension delimiter or EOL
        (\d*)       # optional extension
    /x";
    preg_match($rx, $s, $matches);
    if(!isset($matches[0])) return false;

    $country = $matches[1];
    $area = $matches[2];
    $three = $matches[3];
    $four = $matches[4];
    $ext = $matches[5];

    $out = "$three $four";
    if(!empty($area)) $out = "($area) $out";
    if(!empty($country)) $out = "+$country $out";
    if(!empty($ext)) $out .= " x$ext";

    // check that no digits were truncated
    // if (preg_replace('/\D/', '', $s) != preg_replace('/\D/', '', $out)) return false;
    return $out;
}




add_filter('woocommerce_thankyou_order_received_text', 'woocustom_thankyou', 12 );

function woocustom_thankyou($text) {
    return "<h5>Scroll down for the link to the login page. An email with your login information has also been sent. </h5>";
}

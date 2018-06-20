<?php

require_once('header.php');
require_once('content.php');
require_once('footer.php');

require_once('purchase-controller.php');

$widely_controller->set_responsive_debug(FALSE);

$widely_controller->set_header_banner_nav(FALSE);

$widely_controller->set_show_desc(FALSE);

$widely_controller->set_font_logo(FALSE);

$widely_controller->set_header_home_hero(TRUE);
$widely_controller->set_home_hero_url("wp-content/themes/signtrack/images/phoenix-skyline.jpg");


add_shortcode('plan', 'render_plan_format');

function render_plan_format($atts) {
    $params = shortcode_atts( array(
        'title' => '',
        'limit' => '',
        'price' => '',
        'link' => '',
        'time' => 'mo'
  	), $atts);

      ob_start();
      ?>
      <div class="plan-card">
          <a href="<?php echo $params['link'] ?>">
              <div class="plan-title">
                  <h5><?php echo $params['title'] ?></h5>
              </div>
              <div class="plan-limit">
                  <h3 class="limit-number"><?php echo $params['limit'] ?></h3>
                  <p>sign locations</p>
              </div>
              <div class="plan-price">
                  <button href="<?php echo $params['link'] ?>" class="button button-secondary">$<?php echo $params['price']; ?>/<?php echo $params['time']; ?></button>
              </div>
              <div class="plan-disclaimer">
                  <p></p>
              </div>
          </a>
      </div>
      <?php
      $output = ob_get_contents();
      ob_end_clean();
      return $output;
}

<?php


function signtrack_homepage_header_left() {
  ob_start();

  ?>
  <div class="homepage-header-left-container">

    <div class="logo">
      <img src="wp-content/themes/signtrack/images/logo-outline.png" alt="" />
    </div>
    <div class="caption">
      <h6>Easy Sign Management for Political Campaigns</h6>
    </div>
    <div class="ribbon">
      <div class="ribbon-content">
          <a href="#pricing"><h6>See Plans & Pricing</h6></a>
      </div>
    </div>
  </div>

  <div class="appstore">
     <a href="https://play.google.com/store/apps/details?id=com.signtrackapp.mobile"><img src="wp-content/themes/signtrack/images/btn-google-play-small.png" alt="" /></a>
     <a href="https://itunes.apple.com/us/app/signtrack-app/id982585645"><img src="wp-content/themes/signtrack/images/btn-app-store-small.png" alt="" /></a>
  </div>
  
  <?php

  $output = ob_get_contents();
  ob_end_clean();

  echo $output;
}


function signtrack_homepage_header_right() {
  ob_start();

  ?>
  <div class="awesome stuff">
    <img src="wp-content/themes/signtrack/images/phone-in-hand.png" alt="" />
  </div>
  <?php

  $output = ob_get_contents();
  ob_end_clean();

  echo $output;
}

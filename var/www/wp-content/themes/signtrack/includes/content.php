<?php

add_shortcode('section', 'signtrack_content_section');

function signtrack_content_section($atts, $content) {
  $params = shortcode_atts( array(
    'id' => '',
	'color' => FALSE,
    'close_prev' => FALSE,
    'open_next' => FALSE,
    'iframe' => FALSE
	), $atts);

  if ($params['id']) {
    $id = $params['id'];
  }
  else {
    $id = "";
  }

  if ($params['color']) {
    $class = $params['color'];
  }
  else {
    $class = "";
  }

  ob_start();

  if ($params['close_prev']) {
      ?>
            </div>
        </div>
      <?php
  }


  if ($params['iframe']) {
      ?>
      <div id="<?php echo $id; ?>" class="section <?php echo $class; ?>">
          <div class="iframe-contain" style="position: relative;">
              <?php echo apply_filters('the_content', $content); ?>
          </div>
      </div>

      <script>
      function signtrackVideoResize() {
          var elem = jQuery("iframe");
          var newHeight = (elem.width()*9/16);
          elem.css({'height': newHeight})

          elem.parent().css({"padding-bottom": newHeight });
      }
      jQuery(function() {
          signtrackVideoResize();
          jQuery(window).resize(function() {
              signtrackVideoResize();
          });
      });
      </script>
      <?php
  }

  else {
      ?>
      <div id="<?php echo $id; ?>" class="section <?php echo $class; ?>">
          <div class="section-contain">
              <?php echo apply_filters('the_content', $content); ?>
          </div>
      </div>
      <?php

  }


  if ($params['open_next']) {
      if ($params['open_next'] == 'last') {
          echo '<div class="section">';
          echo '<div class="section-empty">';
      }
      else {
          signtrack_add_section();
      }
  }


  $output = ob_get_contents();
  ob_end_clean();

  return $output;
}



function signtrack_add_section() {
    echo '<div class="section">';
    echo '<div class="section-contain">';
}

function signtrack_close_section() {
    echo '</div></div>';
}

<?php

/**
 * @package   Request Metrics
 * @author    Request Metrics <hello@requestmetrics.com>
 * @license   GPLv3
 * @link      https://requestmetrics.com/
 *
 * Plugin Name:     Request Metrics
 * Plugin URI:      https://requestmetrics.com/docs/wordpress
 * Description:     Track real-user performance, fix Core Web Vitals, and boost your SEO.
 * Version:         1.0.2
 * Author:          Request Metrics
 * Author URI:      https://requestmetrics.com/
 * Text Domain:     reqmtx
 * License:         GPLv3
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:     /languages
 * Requires PHP:    7.4
 */



// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}



/**
 * Plugin Activation/Deactivation
 */
register_activation_hook(__FILE__, "reqmtx_plugin_activation");
function reqmtx_plugin_activation() {
  $default_options = array(
    'token' => ''
  );
  add_option('reqmtx_settings', $default_options);
}
register_deactivation_hook(__FILE__, "reqmtx_plugin_deactivation");
function reqmtx_plugin_deactivation() {
  delete_option('reqmtx_settings');
}


/**
 * Create admin settings
 */
add_action('admin_init', 'reqmtx_settings_init');
function reqmtx_settings_init() {
  register_setting('reqmtx_plugin', 'reqmtx_settings');
  add_settings_section(
    'reqmtx_plugin_section',
    __('Account Settings', 'reqmtx'),
    'reqmtx_account_settings_section_render',
    'reqmtx_plugin'
  );
  add_settings_field(
    'token',
    __('Site Token', 'reqmtx'),
    'reqmtx_token_render',
    'reqmtx_plugin',
    'reqmtx_plugin_section'
  );
}

function reqmtx_account_settings_section_render() {
  echo esc_html(__('General settings to link your Request Metrics account.', 'reqmtx'));
}

function reqmtx_token_render() {
  $options = get_option('reqmtx_settings');
  ?>
  <input type='text' name='reqmtx_settings[token]' value='<?php echo esc_html($options['token']); ?>'
    pattern="[a-z0-9]{7}:[a-z0-9]{7}" placeholder="xxxxxxx:yyyyyyy">
  <p class="description">Get this from your <a href="https://app.requestmetrics.com/" target="_blank">Request Metrics Install Page</a>.
  <?php
}


/**
 * Create admin pages
 */
add_action("admin_menu", "reqmtx_create_admin_pages");
function reqmtx_create_admin_pages() {
  global $submenu;
  add_menu_page(
    __('Request Metrics', 'reqmtx'),
    "Request Metrics",
    "manage_options",
    "reqmtx_dashboard_page",
    "reqmtx_render_dashboard_page",
    "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDI4LjEuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zOnhvZG09Imh0dHA6Ly93d3cuY29yZWwuY29tL2NvcmVsZHJhdy9vZG0vMjAwMyIKCSB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDMxMSAzMjAiCgkgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMzExIDMyMDsiIHhtbDpzcGFjZT0icHJlc2VydmUiPgo8c3R5bGUgdHlwZT0idGV4dC9jc3MiPgoJLnN0MHtmaWxsLXJ1bGU6ZXZlbm9kZDtjbGlwLXJ1bGU6ZXZlbm9kZDtmaWxsOiMwMjBmMjU7fQo8L3N0eWxlPgo8ZyBpZD0iZnVsbF9sb2dvIj4KCTxwYXRoIGlkPSJzaG9ydF9sb2dvIiBjbGFzcz0ic3QwIiBkPSJNMjIxLjMsOTAuMWMxLjYsNywzLjEsMTEuOCw1LjksMTkuMWMtMS4xLDQuMy01LjYsNy4yLTEwLjksMTAuN2MtOS4zLDYtMTguNywxMi43LTI4LjQsMTcuMQoJCWMtMC4yLDAuMS0zLTEwLjQtMy4zLTExLjVMMjIxLjMsOTAuMXogTTIwOC43LDUxLjljMC41LDIuOC0wLjMsNy4yLTEuMyw5LjZjLTEuMSwyLjQtMi4zLDUuMy0yLjcsNi4zYy0wLjQsMC45LDQtMC43LDcuMS02CgkJYzIuMy0zLjgsMi41LTgsMS45LTEwLjRjMC4zLTAuMSwyLjEtMC4zLDIuNC0wLjRjMC41LDMuMSwwLjYsNy44LTAuNCwxMS4yYy0wLjgsMi41LTEuNyw0LjctMi4xLDUuNmMtMC4zLDAuNywzLjQtMC4xLDYuMi01LjMKCQljMS43LTMuMSwyLjMtNy44LDAuOC0xNC40YzAuMi0wLjMsMS40LTEuNSwxLjYtMS44YzAuNiwxLjYsMS4yLDIuNywxLjMsNC40YzAuMywzLjctMC4xLDcuMiwwLjQsOC43YzAuMywxLDQtMy44LDMuNC0xMAoJCWMtMC42LTYuNy01LjQtMTIuOS01LjUtMTQuN2MtMC4xLTIuNi0zLjItNS4xLTguNy00LjljLTUuNCwwLjItMjAuMSw0LjQtMjEuOSwyMy43QzE5OS44LDUwLjMsMjAzLjYsNTEuOCwyMDguNyw1MS45TDIwOC43LDUxLjl6CgkJIE0yODUuOSwxNTguOGMtNS44LDExLjEtMTEuOSwyMi45LTEzLjMsMjUuOWMtNC41LDkuNy04LjgsMjAuMS0xMi44LDMwLjZjLTIyLjUsNTguNy03NS4zLDEyNS4yLTEzOC43LDk4LjQKCQljLTMuOS0xLjctOC0zLjYtMTEuNS01LjljLTIwLjgtMTMuNC01MS00OS40LTYwLjctODEuOGwtMi43LTEzLjNjMCwwLDIxLTI1LDMxLjctMjguNGMzLjItMSwyLjksNC41LDEuNCw3LjZsMTEuNiw5LjEKCQljOS4zLDYuOSwyNC4zLDEzLjcsMzkuMiwxNi42Yy0wLjEtMC4xLTEyLTctMTIuMi03LjJsLTE1LjItMTAuOWMxMC44LTkuMywxNy43LTEzLjUsMjguNy0yMi41YzQuOSwyLjYsOC4xLDQuMiwxMi43LDYuOQoJCWMxMi4yLDcuMSw1OS4zLDI0LjQsNzQuMiwyLjZjLTE1LDEzLTQzLjUsMy4xLTYxLjQtNS44Yy0xNC43LTcuMy0yMy41LTE0LjQtNDEuMS0yMC45Yy00LjUtMS43LTYuMS0yLjMtMTEuMy02LjIKCQljLTQuMy0zLjMtOS03LTEyLjQtMTEuM2MtMS4zLTEuOS0yLTMuNy0yLTUuNGMwLTEuNCwwLjMtMi43LDAuOS0zLjljMC43LTEuMywxLjYtMi41LDIuOC0zLjVjMi44LTIuNSw2LjItMy45LDguOC00CgkJYzMuOS0wLjEsMTAuMiwzLjksMTQsNi4xYzcuOSw0LjcsMTYuOCw5LjMsMjYuNywxMS42YzExLjUsMi43LDMwLjQsNS4xLDQxLjcsNS45YzYuMiwwLjQsMTYuMy0xLjEsMTgtMi41CgkJYy0yLjUsMC41LTEyLjQtMC41LTIyLjQtMi4zYzEzLjctNS40LDI0LjktMTAuMiwzOC40LTE4LjVjMTAtNi4xLDE0LjUtOC45LDE1LjMtMjIuOGMxLjUtMjcuOCw0Mi45LTQ3LDY5LTE1LjQKCQljNi42LDgsOCwxNCw3LjYsMjQuNEMzMTAuMSwxMzAuOCwyODguOSwxNTMuNiwyODUuOSwxNTguOEwyODUuOSwxNTguOHogTTI0My4yLDk5LjZjNSwwLjcsMTAuMSwwLjksMTUuMSwwLjYKCQljNS4yLTAuMyw3LjItMi45LDcuNS01YzAuMi0yLjctMS40LTUuMy00LTYuMmMtNi4zLTIuMy0xMywxLjYtMTYuNCw0LjJjLTAuNSwwLjMtMS4yLDAuNS0xLTAuM2MxLjktNC4yLDUuNS03LjQsOS45LTguOQoJCWMxMS4yLTUuNSwzMy40LTAuNCw0MS44LDkuNWMzLjMsMy4yLDUuMSw3LjcsNSwxMi4yYzAsMC4zLTAuMiwwLjQtMC41LDAuNGMtMC4zLTAuMy0wLjUtMC40LTAuNi0wLjZjLTEuOS0zLjgtNi4xLTEwLjEtMTIuOC0xMC44CgkJYy0yLjctMC4zLTUuMywxLjItNi4yLDMuOGMtMC43LDIuMSwwLDUuMiw0LjUsNy43YzQuMywyLjQsOC45LDQuNCwxMy43LDZjMC4yLDAsMC4zLDAuMywwLjUsMC4zYy0zLjksMTIuNS0xNi4xLDE2LjQtMjYuMSwyMS40CgkJYy0xMC41LDUuMi05LjgsNC4xLTE3LjQtNC40Yy03LjMtOC4yLTE1LjctMTcuNC0xMy43LTMwLjJDMjQyLjgsOTkuNywyNDMsOTkuNiwyNDMuMiw5OS42eiBNMjYyLjgsMTAzLjFjMC45LTEuOSwzLjEtMyw1LjEtMi4zCgkJbDcuNiwyYzIsMC42LDMuNCwyLjQsMy4zLDQuNmMtMC4yLDEuNS0xLjEsMy4yLTQuMSw0LjNsLTUuNSwxLjdjLTAuNCwwLjItMC45LDAuMS0xLjMtMC4zbC0zLjktNC4yCgkJQzI2Mi4yLDEwNi40LDI2Mi4xLDEwNC41LDI2Mi44LDEwMy4xTDI2Mi44LDEwMy4xeiBNODYuOCwxNDkuNEw4Ni44LDE0OS40Yy0wLjYtMC43LTEuMi0xLjQtMS43LTJDNTcsMTY5LjYsMzEsMTkyLjIsMC44LDIyNy43CgkJYy0wLjksMS0xLjEsMi40LTAuNSwzLjZjOC4xLDE3LjIsNC4xLDI5LjMsMjIuOCw0NS42YzEwLjItMTIuNywxNS40LTE5LjQsMjMuOC0yOS40Yy00LjYtMTAtOS41LTI0LjctMTAuNy0zNC42CgkJYy0wLjItMS41LDAuNC0zLDEuNC00YzEuOS0yLjEsMjguMy0yOS41LDM5LjItMzEuOGM2LjMtMS40LDE0LjksMi4yLDExLDkuOWw5LjQsNy45YzkuNy04LjUsMTcuMi0xMy44LDI3LjYtMjIuNGwtMTYuMi02LjkKCQlDMTAyLjMsMTYzLjMsOTAuNywxNTMuNyw4Ni44LDE0OS40TDg2LjgsMTQ5LjR6IE0yNzAuOCwyNi4xYy03LjktMTcuNS0xMy0xMC45LTE5LjItMjQuOWMtMC4yLTAuNS0wLjctMC45LTEuMy0xcy0xLjEsMC0xLjUsMC40CgkJYy05LjEsOC44LTE2LDE1LjQtMjQuOCwyNC4zYzYuOCw0LjMsNC40LDkuOSw2LjIsMTIuMWM0LjcsNS43LDMuMSwxOC4xLTIuMSwyNC4ybC0yLjMsMS45bC0wLjcsMC4yYy0wLjksNC44LTEyLjQsMTIuMi0xNi42LDguMQoJCWMtMC41LDAuMi0xLDAuMy0xLjQsMC40Yy0xMC44LDIuNC02LjgtNy4yLTUuMS0xMC42bDAsMGMzLjMtNy43LTcuNy0zLjItMTAuNi0yYy0wLjYsMC4zLTEuMywwLjQtMS45LDAuNAoJCWMtMy43LDMuNy01LjYsNi4xLTkuMyw5LjdjLTIxLjgsMjEtNDAuNywzNS41LTYyLjYsNTIuNWMxMi44LDcuMSwxMy42LDguMiwyOS4yLDEyLjljMS45LDAuNiw4LjYsMi4xLDE2LjgsMy44CgkJQzIwNSwxMDAuOCwyMzYuOSw2NSwyNzAuOCwyNi4xTDI3MC44LDI2LjF6Ii8+CjwvZz4KPC9zdmc+Cg==");
  add_submenu_page(
    "reqmtx_dashboard_page",
    __('Dashboard', 'reqmtx'),
    "Dashboard",
    "manage_options",
    "reqmtx_dashboard_page",
    "reqmtx_render_dashboard_page");
  add_submenu_page(
    "reqmtx_dashboard_page",
    __('Settings', 'reqmtx'),
    "Settings",
    "manage_options",
    "reqmtx_settings_page",
    "reqmtx_render_settings_page");
}

function reqmtx_render_dashboard_page() {
  $options = get_option('reqmtx_settings');
  $parts = explode(":", $options['token'], 2);
  if (count($parts) == 2) {
    $appId = $parts[1];
  }

  ?>
  <div class="wrap">
    <h1>Request Metrics Dashboard</h1>

    <?php if (empty($appId)) { ?>
      <h2>Please configure your token on the <a href="<?php echo esc_html(admin_url("admin.php?page=reqmtx_settings_page")); ?>">settings page</a>.</h2>
    <?php } else { ?>
      <a href="https://app.requestmetrics.com/app/<?php echo esc_html($appId) ?>?utm_source=wordpress_plugin" target="_blank" class="button button-primary" style="margin-top:20px;">
        Launch Dashboard
      </a>
    <?php } ?>


  </div>
  <?php
}

function reqmtx_render_settings_page() {
  ?>
  <div class="wrap">
    <form action='options.php' method='post'>
      <h1>Request Metrics Settings</h1>
      <?php
      settings_fields('reqmtx_plugin');
      do_settings_sections('reqmtx_plugin');
      submit_button();
      ?>
    </form>
  </div>
  <?php
}


/**
 * Install the agent into the footer for all requests
 */
add_action('wp_footer', 'reqmtx_install_js_snippet');
function reqmtx_install_js_snippet() {
  $options = get_option('reqmtx_settings');
  $page_group = reqmtx_get_page_group_name();

  if (!empty($options['token'])) {
  ?>
    <!-- Request Metrics -->
    <script>
      (function(t,e,n,r){function a(){return e&&e.now?e.now():null}if(!n.version){n._events=[];n._errors=[];n._metadata={};n._urlGroup=null;window.RM=n;n.install=function(e){n._options=e;var a=t.createElement("script");a.async=true;a.crossOrigin="anonymous";a.src=r;var o=t.getElementsByTagName("script")[0];o.parentNode.insertBefore(a,o)};n.identify=function(t,e){n._userId=t;n._identifyOptions=e};n.sendEvent=function(t,e){n._events.push({eventName:t,metadata:e,time:a()})};n.setUrlGroup=function(t){n._urlGroup=t};n.track=function(t,e){n._errors.push({error:t,metadata:e,time:a()})};n.addMetadata=function(t){n._metadata=Object.assign(n._metadata,t)}}})(document,window.performance,window.RM||{},"https://cdn.requestmetrics.com/agent/current/rm.js");
      RM.install({
        token: '<?php echo esc_js($options['token']); ?>'<?php if (!empty($page_group)) {?>,
        urlGroup: '<?php echo esc_js($page_group); ?>'<?php } ?>
      });
    </script>
    <?php
  }
}


/**
 * Track conversion events in WooCommerce
 */
add_action('woocommerce_thankyou', 'reqmtx_send_conversion');
function reqmtx_send_conversion($order_id) {
  $order = wc_get_order($order_id);
  $order_total = $order->get_total();
    ?>
    <script>
      setTimeout(() => {
        var ORDER_TRACKED_KEY = "reqmtx_order_tracked_<?php echo esc_js($order_id); ?>";
        if (!localStorage.getItem(ORDER_TRACKED_KEY)) {
          window.RM && RM.sendEvent("purchase", {
            isConversion: true,
            conversionValue: <?php echo esc_js($order_total); ?>
          });
          localStorage.setItem(ORDER_TRACKED_KEY, true);
        }
      });
    </script>
<?php
}


/**
 * Helper function to try and identify the pageGroup
 */
function reqmtx_get_page_group_name() {

  // is this a WooCommerce store?
  if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    if (is_shop()) {
      return 'shop';
    }
    if (is_product_category()) {
      return 'category';
    }
    if (is_product()) {
      return 'product';
    }
    if (is_cart()) {
      return 'cart';
    }
    if (is_checkout()) {
      return 'checkout';
    }
    if (is_order_received_page()) {
      return 'order_received';
    }
  }

  // Classic WordPress
  if (is_page()) {
    return get_page_template_slug();
  }
  return get_post_type();

}


?>
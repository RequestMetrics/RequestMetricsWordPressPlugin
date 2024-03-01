<?php

/*
 * Plugin Name:   Request Metrics Real User Performance Monitoring
 * Plugin URI:    https://requestmetrics.com/TODO
 * Description:   TODO
 * Version:       1.0.0
 * Author:        Request Metrics
 * Author URI:    https://requestmetrics.com/
 * License:       TODO
 */

/**
 * Plugin Activation/Deactivation
 */
register_activation_hook(__FILE__, "rm_plugin_activation");
function rm_plugin_activation() {
  $default_options = array(
    'token' => ''
  );
  add_option('request_metrics', $default_options);
}
register_deactivation_hook(__FILE__, "rm_plugin_deactivation");
function rm_plugin_deactivation() {
  delete_option('request_metrics');
}


/**
 * Create admin settings
 */
add_action('admin_init', 'rm_settings_init');
function rm_settings_init() {
  register_setting('rm_plugin', 'request_metrics');
  add_settings_section(
    'rm_plugin_section',
    __('Account Settings', 'request_metrics_text_domain'),
    'rm_account_settings_section_render',
    'rm_plugin'
  );
  add_settings_field(
    'token',
    __('Site Token', 'request_metrics_text_domain'),
    'rm_token_render',
    'rm_plugin',
    'rm_plugin_section'
  );
}

add_action('admin_menu', 'rm_add_admin_menu');
function rm_add_admin_menu() {
   add_options_page(
    'Request Metrics',
    'Request Metrics',
    'manage_options',
    'request_metrics',
    'rm_options_page');
}

function rm_account_settings_section_render() {
  echo __('General settings to link your Request Metrics account.', 'request_metrics_text_domain');
}

function rm_token_render() {
  $options = get_option('request_metrics');
  ?>
  <input type='text' name='request_metrics[token]' value='<?php echo $options['token']; ?>'>
  <?php
}

function rm_options_page() {
  ?>
  <div class="wrap">
    <form action='options.php' method='post'>
      <h1>Request Metrics Settings</h1>
      <?php
      settings_fields('rm_plugin');
      do_settings_sections('rm_plugin');
      submit_button();
      ?>
    </form>
  </div>
  <?php
}


/**
 * Install the agent into the footer for all requests
 */
add_action('wp_footer', 'rm_install_js_snippet');
function rm_install_js_snippet() {
  $options = get_option('request_metrics');
  ?>
  <!-- Request Metrics -->
  <script>
    (function(t,e,n,r){function a(){return e&&e.now?e.now():null}if(!n.version){n._events=[];n._errors=[];n._metadata={};n._urlGroup=null;window.RM=n;n.install=function(e){n._options=e;var a=t.createElement("script");a.async=true;a.crossOrigin="anonymous";a.src=r;var o=t.getElementsByTagName("script")[0];o.parentNode.insertBefore(a,o)};n.identify=function(t,e){n._userId=t;n._identifyOptions=e};n.sendEvent=function(t,e){n._events.push({eventName:t,metadata:e,time:a()})};n.setUrlGroup=function(t){n._urlGroup=t};n.track=function(t,e){n._errors.push({error:t,metadata:e,time:a()})};n.addMetadata=function(t){n._metadata=Object.assign(n._metadata,t)}}})(document,window.performance,window.RM||{},"https://cdn.requestmetrics.com/agent/current/rm.js");
    RM.install({
      token: '<?php echo $options['token']; ?>'
    });
  </script>
  <?php
}



?>
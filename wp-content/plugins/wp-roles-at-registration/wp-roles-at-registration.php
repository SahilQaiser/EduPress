<?php
/*
Plugin Name: WP Roles at Registration
Plugin URI: https://www.nettantra.com/wordpress/?utm_src=wp-roles-at-registration
Description: This plugin asks the user to choose his role in the website during registration from a list of selected roles. Works with BuddyPress also.
Version: 0.23
Author: NetTantra
Author URI: https://www.nettantra.com/wordpress/?utm_src=wp-roles-at-registration
License: GPLv2 or later
*/


class WPRolesAtRegistration {
  var $wp_selected_rar_roles;
  var $wp_rar_role_label;

  function __construct() {
    $default_role = get_option("default_role");
    $wp_rar_roles = get_option("wp_rar_roles");
    $wp_rar_role_label = get_option("wp_rar_role_label");
    if(empty($wp_rar_roles)) {
      $this->wp_selected_rar_roles = array($default_role);
    } else {
      $this->wp_selected_rar_roles = (is_array($wp_rar_roles)) ? $wp_rar_roles : unserialize($wp_rar_roles);
      if(!in_array($default_role, $this->wp_selected_rar_roles)) {
        array_push($this->wp_selected_rar_roles, $default_role);
      }
    }
    $this->wp_rar_role_label = (empty($wp_rar_role_label)) ? "Role" : $wp_rar_role_label;
  }

  function manage_allowed_roles_form() {
    global $wp_roles;
    if ( !isset( $wp_roles ) ) $wp_roles = new WP_Roles();
    $default_role = get_option("default_role");
    if($_POST) {
      if(!empty($_POST['wp_rar_roles'])) {
        $post_wp_rar_roles = $_POST['wp_rar_roles'];
        if(!in_array($default_role, $post_wp_rar_roles))
          array_push($post_wp_rar_roles, $default_role);
        $selected_rar_roles = serialize($post_wp_rar_roles);
      } else {
        $selected_rar_roles = serialize(array($default_role));
      }
      update_option("wp_rar_roles", $selected_rar_roles);
      $selected_rar_roles = get_option("wp_rar_roles");
      $this->wp_selected_rar_roles = (is_array($selected_rar_roles)) ? $selected_rar_roles : unserialize($selected_rar_roles);
      if(!empty($_POST['wp_rar_role_label'])) {
        update_option("wp_rar_role_label", $_POST['wp_rar_role_label']);
        $this->wp_rar_role_label = $_POST['wp_rar_role_label'];
      }
    }
    ?>
    <script type="text/javascript">
    <!--
      jQuery(function(){
        jQuery(".wp_rar_roles_cb").click(function(){
          if(jQuery(this).is(":checked")) {
            jQuery(this).parents("label.chooseable:first").addClass("role_selected");
          } else {
            jQuery(this).parents("label.chooseable:first").removeClass("role_selected");
          }
        })
      });
    -->
    </script>
    <style type="text/css">
    <!--
    #choose_roles {
      width: 250px;
      height: 150px;
      overflow: auto;
    }
    .choose_roles_wrap {
      background: #FFF;
      border: 1px solid #CCC;
      padding: 5px;
      width: 250px;
      -moz-border-radius: 5px;
      -webkit-border-radius: 5px;
      border-radius: 5px;
    }
    .choose_roles_wrap label.chooseable {
      display: block;
      padding: 3px;
      margin: 1px;
    }
    .choose_roles_wrap label.role_selected {
      background-color: #D0E8FA;
      -moz-border-radius: 3px;
      -webkit-border-radius: 3px;
      border-radius: 3px;
    }
    .default_role_marker {
      font-size: 8px;
      font-weight: bold;
      color: #254156;
      vertical-align: middle;
    }
    -->
    </style>
      <div class="wrap">
        <h2>Assign Profile Groups to User Roles</h2>
        <form method="post" action="" class="form-table">
          <table>
            <tr valign="top">
              <th>
                Choose Roles
              </th>
              <td>
                <div class="choose_roles_wrap">
                <div id="choose_roles">
                <?php foreach($wp_roles->roles as $role_key=>$role): ?>
                  <div>
                    <?php
                      $label_class ="";
                      $checkbox_status="";
                      if(in_array($role_key, $this->wp_selected_rar_roles)) {
                        $label_class=" role_selected";
                        $checkbox_status = ' checked="checked"';
                      }
                    ?>
                    <label for="wp-rar-roles-<?php echo $role_key; ?>" class="chooseable<?php echo $label_class; ?>">
                    <input type="checkbox" value="<?php echo $role_key; ?>" name="wp_rar_roles[]" id="wp-rar-roles-<?php echo $role_key; ?>" class="wp_rar_roles_cb"<?php echo $checkbox_status; ?><?php echo ($role_key==$default_role) ? ' disabled="disabled"':''; ?> /> <?php echo $role['name']; ?>
                    <?php echo ($role_key==$default_role) ? '<span class="default_role_marker">(Default Role)</span>' : ''; ?>
                    </label>
                  </div>
                <?php endforeach; ?>
                </div>
                </div>
                <span class="description">
                  Choose Roles to be displayed during registration <br />
                  Note: <strong>New User Default Role</strong> can be chosen at <a href="options-general.php">General Settings</a> of WordPress
                </span>
              </td>
            </tr>
            <tr>
              <th><label for="wp-rar-role-label">Role Label</label></th>
              <td>
              <input type="text" name="wp_rar_role_label" value="<?php echo $this->wp_rar_role_label; ?>" id="wp-rar-role-label" /><br />
              <span class="description">
                Enter the label for Role selection form field which will be displayed on the user registration page
              </span>
              </td>
            </tr>
          </table>
          <p class="submit">
            <input type="submit" value="Save Changes" class="button-primary" name="Submit" />
          </p>
        </form>
      </div>
      <div style="font-family: Georgia, 'Times New Roman', serif; font-style: italic; font-size: 12px; padding: 10px 0px; border-top: 1px solid #CCC; margin: 10px;">
        For professional WordPress plugin development/support visit: <a href="http://www.nettantra.com/wp" target="_blank">www.nettantra.com</a>
      </div>
    <?php
  }

  function wp_choose_roles_registration_form() {
    global $wp_roles;
    if ( !isset( $wp_roles ) ) $wp_roles = new WP_Roles();
    $default_role = get_option("default_role");
    if(isset($_POST['wp_rar_user_role'])) {
      $selected_role = $_POST['wp_rar_user_role'];
    } else {
      $selected_role = $default_role;
    }
    if(count($this->wp_selected_rar_roles) < 2) return true;
    ?>
    <style type="text/css">
    <!--
    select.input {
      background: #FFF;
      border: 1px solid #E5E5E5;
      font-size: 16px;
      margin-bottom: 16px;
      margin-right: 6px;
      margin-top: 2px;
      padding: 3px;
      width: 100%;
    }
    -->
    </style>
    <p>
    <label for="wp_rar_user_role"><?php echo $this->wp_rar_role_label; ?><br />
      <select id="wp_rar_user_role" name="wp_rar_user_role" class="input select">
    <?php
    foreach($this->wp_selected_rar_roles as $role) {
      ?>
      <option value="<?php echo $role; ?>"<?php echo ($selected_role == $role) ? ' selected="selected"' : ''; ?>>
        <?php echo $wp_roles->roles[$role]['name']; ?>
      </option>
      <?php
    }
    ?>
      </select>
    </label>
    </p>
    <?php
  }

  function bp_choose_roles_registration_form() {
    global $wp_roles;

    if ( !isset( $wp_roles ) ) $wp_roles = new WP_Roles();
    $default_role = get_option("default_role");

    if(isset($_POST['wp_rar_user_role'])) {
      $selected_role = $_POST['wp_rar_user_role'];
    } else {
      $selected_role = $default_role;
    }
    ?>
    <style type="text/css">
    <!--
    select.input {
      background: #FFF;
      border: 1px solid #E5E5E5;
      font-size: 16px;
      margin-bottom: 16px;
      margin-right: 6px;
      margin-top: 2px;
      padding: 3px;
    }
    #user-role-section {
      width: 48%;
      float: left;
      clear: left;
    }
    #wp_rar_user_role {
      width: 50%;
    }
    -->
    </style>
    <div class="register-section" id="user-role-section">
    <label for="wp_rar_user_role"><?php echo $this->wp_rar_role_label; ?></label>
      <select id="wp_rar_user_role" name="wp_rar_user_role" class="input select">
    <?php
    foreach($this->wp_selected_rar_roles as $role) {
      ?>
      <option value="<?php echo $role; ?>"<?php echo ($selected_role == $role) ? ' selected="selected"' : ''; ?>>
        <?php echo $wp_roles->roles[$role]['name']; ?>
      </option>
      <?php
    }
    ?>
      </select>
    </div>
    <div style="clear: both;"></div>
    <?php
  }

  function set_roles_at_registration($user_ID) {
    if( in_array($_POST['wp_rar_user_role'], $this->wp_selected_rar_roles) ) {
      $wp_rar_user_role = $_POST['wp_rar_user_role'];
    } else {
      $wp_rar_user_role = get_option("default_role");
    }
    if(isset($wp_rar_user_role) and $user_ID) {
      wp_update_user( array ('ID' => $user_ID, 'role' => $wp_rar_user_role) );
    }
  }
}

function wp_rar_admin_menu() {
  $wp_rar_plugin = new WPRolesAtRegistration;
  add_options_page('WP Roles at Registration', 'WP Roles at Registration', 'manage_options', 'wp-roles-at-registration', array($wp_rar_plugin, 'manage_allowed_roles_form'));
}

function init_rar() {
  $wp_rar_plugin = new WPRolesAtRegistration;
  add_action('register_form', array($wp_rar_plugin, 'wp_choose_roles_registration_form'));
  add_action('user_register', array($wp_rar_plugin, 'set_roles_at_registration'));
  if(class_exists('BP_Core_User')) {
    add_action('bp_after_signup_profile_fields', array($wp_rar_plugin, 'bp_choose_roles_registration_form'));
  }
}

add_action('init', 'init_rar');
add_action('admin_menu', 'wp_rar_admin_menu');
?>

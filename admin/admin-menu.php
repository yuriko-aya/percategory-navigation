<?php

add_action( 'admin_menu', 'categori_nav_menu' );

function categori_nav_menu() {
    add_options_page('Per Category navigation', 'Category Navigation', 'manage_options', 'per-category-navigation.php', 'pernav_options' );
}

function pernav_options() {
    if ( !current_user_can( 'manage_options' ) ) {
        wp_die( __('You don not have permission!'));
    }
    ?>
    <div class="wrap">
        <h1>Per Category Navigation Options</h1>
    </div>
    <hr>
    <?php
    if ( isset($_POST['preconfirm']) && $_POST['preconfirm'] == "yes") {
        $nav_location = $_POST['location']; 
        update_option('catnav_location', $nav_location);
        echo '<div class="updated notice"><p>Location saved!</p></div>';
    }
    ?>
    <div><p>Choose where the navigation will be located:</p></div>
    <form name="nav-location" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<?php
        if (get_option('catnav_location') && get_option('catnav_location') == 'top') {
?>
        <div><input type="radio" name="location" value="top" checked="checked"> Top of page<br><br></div>
        <div><input type="radio" name="location" value="bottom"> Bottom of page<br><br></div>
        <div><input type="radio" name="location" value="both"> Both top and bottom<br><br></div>
<?php
        } elseif (get_option('catnav_location') && get_option('catnav_location') == 'bottom') {
?>
        <div><input type="radio" name="location" value="top"> Top of page<br><br></div>
        <div><input type="radio" name="location" value="bottom" checked="checked"> Bottom of page<br><br></div>
        <div><input type="radio" name="location" value="both"> Both top and bottom<br><br></div>
<?php
        } else {
?>
        <div><input type="radio" name="location" value="top"> Top of page<br><br></div>
        <div><input type="radio" name="location" value="bottom"> Bottom of page<br><br></div>
        <div><input type="radio" name="location" value="both" checked="checked"> Both top and bottom<br><br></div>
<?php
        }
?>
        <input type="hidden" name="preconfirm" value="yes"> 
        <input type="submit" value="Save">
    </form>
    <?php
}
<?php
/**
 * SMTP Customizer Settings
 *
 * Adds SMTP settings to the WordPress Customizer and configures SMTP for outgoing emails.
 */

/* -- SMTP Customizer Settings -- */
function smtp_customizer_settings($wp_customize) {
    // Add a section for SMTP settings in the Customizer
    $wp_customize->add_section('smtp_settings', array(
        'title' => __('SMTP Settings', 'theme_name'),
        'priority' => 30,
    ));

    // Add settings and controls for SMTP Host
    $wp_customize->add_setting('smtp_host', array(
        'default' => '',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('smtp_host', array(
        'label' => __('SMTP Host', 'theme_name'),
        'section' => 'smtp_settings',
        'type' => 'text',
    ));

    // Add settings and controls for SMTP Port
    $wp_customize->add_setting('smtp_port', array(
        'default' => '',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('smtp_port', array(
        'label' => __('SMTP Port', 'theme_name'),
        'section' => 'smtp_settings',
        'type' => 'text',
    ));

    // Add settings and controls for SMTP Username
    $wp_customize->add_setting('smtp_username', array(
        'default' => '',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('smtp_username', array(
        'label' => __('SMTP Username', 'theme_name'),
        'section' => 'smtp_settings',
        'type' => 'text',
    ));

    // Add settings and controls for SMTP Password
    $wp_customize->add_setting('smtp_password', array(
        'default' => '',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('smtp_password', array(
        'label' => __('SMTP Password', 'theme_name'),
        'section' => 'smtp_settings',
        'type' => 'password',
    ));

    // Add settings and controls for SMTP Encryption
    $wp_customize->add_setting('smtp_encryption', array(
        'default' => 'none',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('smtp_encryption', array(
        'label' => __('SMTP Encryption', 'theme_name'),
        'section' => 'smtp_settings',
        'type' => 'select',
        'choices' => array(
            'none' => 'None',
            'ssl' => 'SSL',
            'tls' => 'TLS',
        ),
    ));
}

add_action('customize_register', 'smtp_customizer_settings');

/**
 * Setup SMTP Configuration
 *
 * Configures SMTP settings for PHPMailer based on Customizer values.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer
 */
function setup_smtp( $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->Host = get_theme_mod('smtp_host');
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = get_theme_mod('smtp_port');
    $phpmailer->Username = get_theme_mod('smtp_username');
    $phpmailer->Password = get_theme_mod('smtp_password');
    $smtp_encryption = get_theme_mod('smtp_encryption');

    if ( $smtp_encryption === 'ssl' ) {
        $phpmailer->SMTPSecure = 'ssl';
    } elseif ( $smtp_encryption === 'tls' ) {
        $phpmailer->SMTPSecure = 'tls';
    } else {
        $phpmailer->SMTPSecure = '';
    }

    $phpmailer->From = get_option('admin_email');
    $phpmailer->FromName = get_option('blogname');
}

add_action('phpmailer_init', 'setup_smtp');

/**
 * Send Test SMTP Email
 *
 * Sends a test email to verify SMTP configuration.
 */
function send_test_smtp_email() {
    if (isset($_POST['action']) && $_POST['action'] == 'send_test_smtp_email') {
        check_admin_referer('send_test_smtp_email_nonce');

        $to = get_option('admin_email');
        $subject = 'Test Email';
        $message = 'This is a test email to verify SMTP configuration.';

        $headers = array('Content-Type: text/html; charset=UTF-8');

        if (wp_mail($to, $subject, $message, $headers)) {
            update_option('smtp_last_test_success', time());
            wp_send_json_success('Test email sent successfully.');
        } else {
            update_option('smtp_last_test_success', 0);
            wp_send_json_error('Failed to send test email. Please check your SMTP settings.');
        }
    }
}

add_action('wp_ajax_send_test_smtp_email', 'send_test_smtp_email');

/**
 * Display SMTP Admin Notice
 *
 * Displays a warning notice in the admin dashboard if SMTP is not configured or not working.
 */
function smtp_admin_notice() {
    $last_test_success = get_option('smtp_last_test_success', 0);
    $time_since_last_success = time() - $last_test_success;

    if (!get_theme_mod('smtp_host') || !get_theme_mod('smtp_port') || !get_theme_mod('smtp_username') || !get_theme_mod('smtp_password')) {
        echo '<div class="notice notice-warning is-dismissible">
                <p>' . __('SMTP is not configured. Please go to Appearance > Customize > SMTP Settings to configure.', 'theme_name') . '</p>
              </div>';
    } elseif ($last_test_success == 0 || $time_since_last_success > 24 * 60 * 60) {
        echo '<div class="notice notice-warning is-dismissible">
                <p>' . __('SMTP is configured but not tested or failed. Please click the button below to send a test email.', 'theme_name') . '</p>
                <p><button id="send-test-email" class="button-primary">Send Test Email</button></p>
              </div>';
    } else {
        echo '<div class="notice notice-success is-dismissible">
                <p>' . __('SMTP is configured and working correctly.', 'theme_name') . '</p>
                <p><button id="send-test-email" class="button-primary">Send Test Email</button></p>
              </div>';
    }
}

add_action('admin_notices', 'smtp_admin_notice');

/**
 * Enqueue Admin Scripts
 */
function smtp_admin_scripts() {
    wp_enqueue_script('jquery');
    
    $inline_script = "
        jQuery(document).ready(function($) {
            $('#send-test-email').on('click', function() {
                $.ajax({
                    url: '".admin_url('admin-ajax.php')."',
                    type: 'post',
                    data: {
                        action: 'send_test_smtp_email',
                        _wpnonce: '".wp_create_nonce('send_test_smtp_email_nonce')."'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.data);
                            location.reload(); // Reload the page to update the notice
                        } else {
                            alert(response.data);
                        }
                    }
                });
            });
        });
    ";

    wp_add_inline_script('jquery', $inline_script);
}

add_action('admin_enqueue_scripts', 'smtp_admin_scripts');

/**
 * Schedule SMTP Test Event
 */
function schedule_smtp_test() {
    if (!wp_next_scheduled('scheduled_smtp_test_event')) {
        wp_schedule_event(time(), 'daily', 'scheduled_smtp_test_event');
    }
}
add_action('wp', 'schedule_smtp_test');

/**
 * Run Scheduled SMTP Test
 */
function run_scheduled_smtp_test() {
    $to = get_option('admin_email');
    $subject = 'Scheduled Test Email';
    $message = 'This is a scheduled test email to verify SMTP configuration.';
    $headers = array('Content-Type: text/html; charset=UTF-8');

    if (wp_mail($to, $subject, $message, $headers)) {
        update_option('smtp_last_test_success', time());
    } else {
        update_option('smtp_last_test_success', 0);
    }
}
add_action('scheduled_smtp_test_event', 'run_scheduled_smtp_test');

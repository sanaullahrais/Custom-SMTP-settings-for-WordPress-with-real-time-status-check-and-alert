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
 * Test SMTP Connection
 *
 * Tests the SMTP connection to ensure that the configuration is correct.
 *
 * @return bool True if the connection is successful, false otherwise.
 */
function test_smtp_connection() {
    $host = get_theme_mod('smtp_host');
    $port = get_theme_mod('smtp_port');
    $username = get_theme_mod('smtp_username');
    $password = get_theme_mod('smtp_password');
    $encryption = get_theme_mod('smtp_encryption');
    
    if (!$host || !$port || !$username || !$password) {
        return false;
    }

    $mailer = new PHPMailer\PHPMailer\PHPMailer();
    $mailer->isSMTP();
    $mailer->Host = $host;
    $mailer->SMTPAuth = true;
    $mailer->Port = $port;
    $mailer->Username = $username;
    $mailer->Password = $password;
    $mailer->SMTPSecure = $encryption == 'none' ? '' : $encryption;

    try {
        if ($mailer->smtpConnect()) {
            $mailer->smtpClose();
            return true;
        }
    } catch (Exception $e) {
        // Connection failed
    }

    return false;
}

/**
 * Display SMTP Admin Notice
 *
 * Displays a warning notice in the admin dashboard if SMTP is not configured or not working.
 */
function smtp_admin_notice() {
    if ( ! get_theme_mod('smtp_host') || ! test_smtp_connection() ) {
        echo '<div class="notice notice-warning is-dismissible">
                <p>' . __('SMTP is not configured or not working. Please go to Appearance > Customize > SMTP Settings to configure.', 'theme_name') . '</p>
              </div>';
    }
}

add_action('admin_notices', 'smtp_admin_notice');

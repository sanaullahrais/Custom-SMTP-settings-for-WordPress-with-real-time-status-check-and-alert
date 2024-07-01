# 📧 Custom SMTP Settings for WordPress with Real-Time Status Check
Easily configure SMTP settings for your WordPress site through the Customizer and ensure your emails are always sent successfully. This plugin provides real-time status checks and admin notifications to keep you informed.

#📝 Description
This plugin allows you to configure SMTP settings directly from the WordPress Customizer. It includes features to test the SMTP configuration manually and automatically every 24 hours, ensuring continuous functionality. Admin notices will alert you if the SMTP settings are not configured or if there are issues with sending emails.

#✨ Features
🎛️ Configure SMTP settings through Appearance > Customize > SMTP Settings
🌐 Supports SMTP host, port, username, password, and encryption settings
🔄 Real-time status check for SMTP connection
🚨 Admin notice if SMTP is not configured or not working
⏰ Automatic SMTP check every 24 hours
✉️ Manual test email button to verify SMTP configuration

#⚙️ Configuration
Configure SMTP Settings:
1. Go to Appearance > Customize > SMTP Settings.
2. Enter the required SMTP Host, Port, Username, Password, and Encryption settings.

Send a Test Email:
1. After configuring the settings, you will see an admin notice with a button to send a test email.
2. Click the "Send Test Email" button to verify the SMTP configuration.

Automatic SMTP Check:
1. The plugin automatically checks the SMTP configuration every 24 hours and updates the status.

#🚀 Usage
SMTP Customizer Settings
The following SMTP settings can be configured in the WordPress Customizer:
SMTP Host: The host address of your SMTP server.
SMTP Port: The port number for your SMTP server.
SMTP Username: The username for your SMTP account.
SMTP Password: The password for your SMTP account.
SMTP Encryption: The encryption method (None, SSL, TLS).

#Admin Notices
Admin notices will inform you about the status of your SMTP configuration:

⚠️ Warning Notice: Displayed if the SMTP settings are not configured or if the test email fails.
✅ Success Notice: Displayed if the SMTP settings are correctly configured and the test email is successful.

#Scheduled SMTP Test
The plugin schedules a daily event to test the SMTP configuration automatically:

The results of the test are stored and displayed in the admin notices. If the scheduled test fails, a warning notice will be displayed.

#📄 License
This project is licensed under the MIT License.

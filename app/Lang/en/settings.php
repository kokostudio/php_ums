<?php
// app/Lang/en/settings.php
return [
    'title'           => 'System Settings',
    'general'         => 'General Info',
    'login_page'      => 'Login Page',
    'email_settings'  => 'Email Settings',
    'coming_soon'     => 'Coming Soon',

    // General
    'site_name'       => 'System / Site Name',
    'site_name_hint'  => 'Appears in topbar, page titles, and email notifications.',
    'footer_text'     => 'Footer Text',
    'footer_hint'     => 'Displayed at the bottom of the main dashboard.',
    'site_logo'       => 'Site Logo',
    'site_logo_hint'  => 'Displayed in sidebar and login page',
    'site_favicon'    => 'Site Favicon',
    'site_favicon_hint'=> 'Browser tab icon',
    'ph_app_name'     => 'e.g. My Awesome App',
    'ph_footer'       => 'e.g. &copy; 2026 My Company',

    // Login
    'login_title'     => 'Login Page Title',
    'login_title_hint'=> 'Main headline displayed on the login page.',
    'login_subtitle'  => 'Login Subtitle',
    'login_subtitle_hint'=> 'Subtitle text below the title',
    'ph_welcome'      => 'e.g. Welcome Back!',
    'ph_login_sub'    => 'e.g. Please enter your details.',
    'login_logo'      => 'Login Logo',
    'login_logo_hint' => 'Overrides site logo on the login page.',
    'login_bg'        => 'Background Image',
    'login_bg_hint'   => 'Optional background image for the login page.',
    'remove'          => 'Remove',

    'save_settings'   => 'Save Settings',
    'settings_saved'  => 'Settings saved successfully.',
    'general_info'    => 'General Information',
    'manage_desc'     => 'Manage general and branding configuration of your application.',
    
    'system_status'   => 'System Status',
    'maintenance_mode'=> 'Maintenance Mode',
    'maintenance_mode_desc' => 'When active, only Super Admins can access the system.',
    'enable_registration'   => 'Enable User Registration',
    'enable_registration_desc' => 'Allow new users to register an account.',
    
    // Security
    'security' => 'Security',
    'pwd_policy' => 'Password Policy',
    'min_pwd_length' => 'Minimum Password Length',
    'min_pwd_length_desc' => 'Minimum characters required for new users and password changes.',
    'req_upper' => 'Require Uppercase Letter (A-Z)',
    'req_lower' => 'Require Lowercase Letter (a-z)',
    'req_number' => 'Require Number (0-9)',
    'req_special' => 'Require Special Character (!@#$...)',
    
    '2fa_admin' => 'Two-Factor Authentication (2FA)',
    'force_2fa_admin' => 'Force 2FA for Admins',
    'force_2fa_admin_desc' => 'If enabled, users with Admin privileges will be forced to set up 2FA immediately upon login.',
    
    'login_security' => 'Login Security & Rate Limiting',
    'max_login_attempts' => 'Maximum Login Attempts',
    'max_login_attempts_desc' => 'Number of failed login attempts allowed before a temporary block.',
    'login_lockout_time' => 'Login Lockout Time (Minutes)',
    'login_lockout_time_desc' => 'Duration to lock the account when max failed login attempts is reached.',
    'session_lifetime' => 'Session Lifetime (Minutes)',
    'session_lifetime_desc' => 'Inactivity period allowed before the user is automatically logged out.',
    'email_verification' => 'Email Verification',
    'require_email_verification' => 'Require Email Verification',
    'require_email_verification_desc' => 'Require new users to verify their email address before they can log in.',

    // Social Login
    'social_login' => 'Social Login',
    'social_login_desc' => 'Configure social media login providers',
    'google_login' => 'Google Login',
    'enable_google_login' => 'Enable Google Login',
    'google_client_id' => 'Google Client ID',
    'google_client_secret' => 'Google Client Secret',
    'google_redirect_uri' => 'Google Redirect URI',
    'line_login' => 'LINE Login',
    'enable_line_login' => 'Enable LINE Login',
    'line_client_id' => 'LINE Client ID',
    'line_client_secret' => 'LINE Client Secret',
    'line_redirect_uri' => 'LINE Redirect URI',
];

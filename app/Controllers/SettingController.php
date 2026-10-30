<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Core\Session;
use App\Core\Settings;
use App\Core\Logger;
use App\Core\Theme;
use App\Core\ThemeColor;

class SettingController extends Controller
{
    public function index($tab = 'general')
    {
        // Valid tabs
        $validTabs = ['general', 'login', 'translations', 'security', 'social', 'design_spec'];
        
        if (!in_array($tab, $validTabs)) {
            $tab = 'general';
        }

        $db = Database::getInstance();
        $settings = $db->query("SELECT setting_key, setting_value FROM system_settings")->fetchAll(\PDO::FETCH_KEY_PAIR);
        
        $pivoted = [];
        if ($tab === 'translations') {
            $translations = $db->query("SELECT locale, namespace, key_name, text_value FROM translations")->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($translations as $row) {
                $groupKey = $row['namespace'] . '.' . $row['key_name'];
                if (!isset($pivoted[$groupKey])) {
                    $pivoted[$groupKey] = [
                        'namespace' => $row['namespace'],
                        'key_name' => $row['key_name'],
                        'en' => '',
                        'th' => ''
                    ];
                }
                $pivoted[$groupKey][$row['locale']] = $row['text_value'];
            }
        }
        
        $this->view('settings/index', ['pageTitle' => 'System Settings', 'settings' => $settings, 'tab' => $tab, 'translations' => $pivoted]);
    }

    public function update($tab = 'general')
    {
        Security::checkCsrf();
        $tab = in_array($tab, ['general', 'login', 'security', 'social', 'translations', 'design_spec']) ? $tab : 'general';
        if ($tab === 'design_spec') {
            $this->redirect('/settings/design_spec');
            return;
        }

        // Keys allowed per tab
        $textKeysByTab = [
            'general' => [
                'site_name', 'site_footer', 'base_font_size'
            ],
            'login'   => [
                'login_title', 'login_subtitle', 'login_edition', 'login_features_enabled',
                'login_feature1_icon', 'login_feature1_text',
                'login_feature2_icon', 'login_feature2_text',
                'login_feature3_icon', 'login_feature3_text',
                'login_pattern', 'login_logo_size', 'login_title_size', 'login_site_name_size'
            ],
            'security' => ['password_min_length', 'login_max_attempts', 'login_lockout_time', 'session_lifetime', 'require_email_verification'],
            'social'   => [
                'google_client_id', 'google_client_secret', 'google_redirect_uri',
                'line_client_id', 'line_client_secret', 'line_redirect_uri',
                'facebook_client_id', 'facebook_client_secret', 'facebook_redirect_uri',
                'github_client_id', 'github_client_secret', 'github_redirect_uri'
            ],
        ];
        $fileKeysByTab = [
            'general' => ['site_logo', 'site_favicon'],
            'login'   => ['login_logo', 'login_background'],
        ];

        $textKeys = $textKeysByTab[$tab] ?? [];
        $fileKeys = $fileKeysByTab[$tab] ?? [];

        try {
            // 1. Text Inputs
            foreach ($textKeys as $key) {
                if (isset($_POST[$key])) {
                    Settings::set($key, Security::sanitize($_POST[$key]));
                }
            }

            if ($tab === 'general') {
                Settings::set('maintenance_mode', !empty($_POST['maintenance_mode']) ? '1' : '0');
                Settings::set('enable_registration', !empty($_POST['enable_registration']) ? '1' : '0');

                // ── Sidebar Theme (Preset + Custom Color Picker) ──
                $themeKey = (string) ($_POST['sidebar_theme'] ?? 'slate');
                if (!in_array($themeKey, Theme::allowedThemeKeys(), true)) {
                    $themeKey = 'slate';
                }

                // สีที่ผู้ใช้เลือกเอง (ต้องเป็น HEX #RRGGBB เท่านั้น)
                // หากส่งค่าไม่ถูกต้อง ให้คงค่าเดิมที่บันทึกไว้แทนการรีเซ็ต
                $customColor = ThemeColor::normalize((string) ($_POST['sidebar_custom_color'] ?? ''));
                if ($customColor !== null) {
                    Settings::set('sidebar_custom_color', $customColor);
                } elseif (!Settings::get('sidebar_custom_color')) {
                    Settings::set('sidebar_custom_color', Theme::DEFAULT_CUSTOM_COLOR);
                }

                Settings::set('sidebar_theme', $themeKey);

                // ── Brand Icon & Logo Customization ──
                $logoType = (string) ($_POST['logo_type'] ?? 'icon');
                Settings::set('logo_type', in_array($logoType, ['icon', 'image'], true) ? $logoType : 'icon');

                $allIcons = array_keys(\App\Core\BrandIcon::all());
                $logoIcon = (string) ($_POST['logo_icon'] ?? \App\Core\BrandIcon::DEFAULT_ICON);
                Settings::set('logo_icon', in_array($logoIcon, $allIcons, true) ? $logoIcon : \App\Core\BrandIcon::DEFAULT_ICON);

                $logoColor = \App\Core\ThemeColor::normalize((string) ($_POST['logo_icon_color'] ?? ''));
                Settings::set('logo_icon_color', $logoColor ?? \App\Core\BrandIcon::DEFAULT_COLOR);

                $logoSize = (int) ($_POST['logo_icon_size'] ?? \App\Core\BrandIcon::DEFAULT_SIZE);
                $logoSize = max(20, min(64, $logoSize));
                Settings::set('logo_icon_size', (string) $logoSize);

                // ── Favicon Customization ──
                $faviconSync = !empty($_POST['favicon_sync_logo']) ? '1' : '0';
                Settings::set('favicon_sync_logo', $faviconSync);

                $faviconType = (string) ($_POST['favicon_type'] ?? 'icon');
                Settings::set('favicon_type', in_array($faviconType, ['icon', 'image'], true) ? $faviconType : 'icon');

                if ($faviconSync === '1') {
                    Settings::set('favicon_icon', in_array($logoIcon, $allIcons, true) ? $logoIcon : \App\Core\BrandIcon::DEFAULT_ICON);
                    Settings::set('favicon_icon_color', $logoColor ?? \App\Core\BrandIcon::DEFAULT_COLOR);
                } else {
                    $favIcon = (string) ($_POST['favicon_icon'] ?? $logoIcon);
                    Settings::set('favicon_icon', in_array($favIcon, $allIcons, true) ? $favIcon : \App\Core\BrandIcon::DEFAULT_ICON);

                    $favColor = \App\Core\ThemeColor::normalize((string) ($_POST['favicon_icon_color'] ?? ''));
                    Settings::set('favicon_icon_color', $favColor ?? \App\Core\BrandIcon::DEFAULT_COLOR);
                }

                $favBg = (string) ($_POST['favicon_bg_color'] ?? 'transparent');
                if ($favBg !== 'transparent') {
                    $favBg = \App\Core\ThemeColor::normalize($favBg) ?? 'transparent';
                }
                Settings::set('favicon_bg_color', $favBg);
            }
            if ($tab === 'login') {
                $enableUsername = !empty($_POST['enable_username_login']) ? '1' : '0';
                $enableSocial   = !empty($_POST['enable_social_login']) ? '1' : '0';

                if ($enableUsername === '0' && $enableSocial === '0') {
                    $this->respond("/settings/{$tab}", __('settings.at_least_one_auth_method_required'), 'error', 422);
                    return;
                }

                if ($enableUsername === '0') {
                    $googleOn   = Settings::get('enable_google_login') === '1';
                    $lineOn     = Settings::get('enable_line_login') === '1';
                    $facebookOn = Settings::get('enable_facebook_login') === '1';
                    $githubOn   = Settings::get('enable_github_login') === '1';
                    if (!$googleOn && !$lineOn && !$facebookOn && !$githubOn) {
                        $this->respond("/settings/{$tab}", __('settings.need_active_social_if_username_disabled'), 'error', 422);
                        return;
                    }
                }

                Settings::set('enable_username_login', $enableUsername);
                Settings::set('enable_social_login', $enableSocial);
            }
            if ($tab === 'security') {
                Settings::set('pwd_require_upper', !empty($_POST['pwd_require_upper']) ? '1' : '0');
                Settings::set('pwd_require_lower', !empty($_POST['pwd_require_lower']) ? '1' : '0');
                Settings::set('pwd_require_number', !empty($_POST['pwd_require_number']) ? '1' : '0');
                Settings::set('pwd_require_special', !empty($_POST['pwd_require_special']) ? '1' : '0');
                Settings::set('force_2fa_admin', !empty($_POST['force_2fa_admin']) ? '1' : '0');
            }
            if ($tab === 'social') {
                $enableSocial   = !empty($_POST['enable_social_login']) ? '1' : '0';
                $enableUsername = !empty($_POST['enable_username_login']) ? '1' : '0';

                if ($enableSocial === '0' && $enableUsername === '0') {
                    $this->respond("/settings/{$tab}", __('settings.at_least_one_auth_method_required'), 'error', 422);
                    return;
                }

                $googleOn   = !empty($_POST['enable_google_login']) ? '1' : '0';
                $lineOn     = !empty($_POST['enable_line_login']) ? '1' : '0';
                $facebookOn = !empty($_POST['enable_facebook_login']) ? '1' : '0';
                $githubOn   = !empty($_POST['enable_github_login']) ? '1' : '0';

                if ($enableUsername === '0' && (!$enableSocial || (!$googleOn && !$lineOn && !$facebookOn && !$githubOn))) {
                    $this->respond("/settings/{$tab}", __('settings.need_active_social_if_username_disabled'), 'error', 422);
                    return;
                }

                Settings::set('enable_username_login', $enableUsername);
                Settings::set('enable_social_login', $enableSocial);
                Settings::set('enable_google_login', $googleOn);
                Settings::set('enable_line_login', $lineOn);
                Settings::set('enable_facebook_login', $facebookOn);
                Settings::set('enable_github_login', $githubOn);
            }
            if ($tab === 'translations') {
                Settings::set('enable_language_switcher', !empty($_POST['enable_language_switcher']) ? '1' : '0');
                $langs = $_POST['available_languages'] ?? [];
                if (is_array($langs)) {
                    $langs = array_map('\App\Core\Security::sanitize', $langs);
                    Settings::set('available_languages', implode(',', $langs));
                } else {
                    Settings::set('available_languages', '');
                }
            }

            // 2. File Uploads
            $uploadDir = __DIR__ . '/../../public/uploads/settings/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $updatedFiles = []; // เก็บ path รูปใหม่เพื่อส่งกลับไปอัปเดต preview แบบ AJAX

            foreach ($fileKeys as $fileKey) {
                if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES[$fileKey]['tmp_name'];
                    $name    = basename($_FILES[$fileKey]['name']);
                    $mime    = mime_content_type($tmpName);

                    if (strpos($mime, 'image/') === 0) {
                        $ext      = pathinfo($name, PATHINFO_EXTENSION);
                        $safeName = $fileKey . '_' . time() . '.' . $ext;
                        $destination = $uploadDir . $safeName;
                        if (move_uploaded_file($tmpName, $destination)) {
                            Settings::set($fileKey, '/uploads/settings/' . $safeName);
                            $updatedFiles[$fileKey] = '/uploads/settings/' . $safeName;
                            if ($fileKey === 'site_logo') {
                                Settings::set('logo_type', 'image');
                            } elseif ($fileKey === 'site_favicon') {
                                Settings::set('favicon_type', 'image');
                            }
                        }
                    } else {
                        throw new \Exception("File uploaded for {$fileKey} is not a valid image.");
                    }
                }
            }

            Logger::activity("Updated system settings (tab: {$tab}).");
            // AJAX: อยู่หน้าเดิม ไม่รีเฟรช + Toast แจ้งบันทึก (ส่ง path รูปใหม่กลับไปอัปเดต preview ทันที)
            // Fallback (ไม่มี JS): PRG รีโหลดแท็บเดิมเพื่อ apply ธีม/โลโก้ใหม่จากฝั่ง Server
            $this->respond("/settings/{$tab}", __('settings.settings_saved'), 'success', 200, ['stay' => true, 'updated_files' => $updatedFiles]);

        } catch (\Throwable $e) {
            \App\Core\Logger::error("Failed to update system settings (tab: {$tab}): " . $e->getMessage());
            $this->respond("/settings/{$tab}", __('settings.failed_update_settings') . ' ' . $e->getMessage(), 'error', 500);
        }
    }

    public function updateTranslation()
    {
        Security::checkCsrf();

        $namespace = $_POST['namespace'] ?? '';
        $keyName = $_POST['key_name'] ?? '';
        $thValue = $_POST['th_value'] ?? '';
        $enValue = $_POST['en_value'] ?? '';

        if (!$namespace || !$keyName) {
            $this->json(['success' => false, 'type' => 'error', 'message' => __('settings.translation_invalid')], 422);
        }

        try {
            $db = Database::getInstance();
            
            $stmt = $db->prepare("
                INSERT INTO translations (locale, namespace, key_name, text_value) 
                VALUES ('th', ?, ?, ?) 
                ON DUPLICATE KEY UPDATE text_value = VALUES(text_value)
            ");
            $stmt->execute([$namespace, $keyName, $thValue]);

            $stmt = $db->prepare("
                INSERT INTO translations (locale, namespace, key_name, text_value) 
                VALUES ('en', ?, ?, ?) 
                ON DUPLICATE KEY UPDATE text_value = VALUES(text_value)
            ");
            $stmt->execute([$namespace, $keyName, $enValue]);

            Logger::activity("Updated translation for {$namespace}.{$keyName}");
            $this->json(['success' => true, 'type' => 'success', 'message' => __('settings.translation_updated')]);
        } catch (\Throwable $e) {
            \App\Core\Logger::error("Failed to update translation for {$namespace}.{$keyName}: " . $e->getMessage());
            $this->json(['success' => false, 'type' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

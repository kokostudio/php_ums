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
            'general' => ['site_name', 'site_footer', 'base_font_size'],
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
            }
            if ($tab === 'security') {
                Settings::set('pwd_require_upper', !empty($_POST['pwd_require_upper']) ? '1' : '0');
                Settings::set('pwd_require_lower', !empty($_POST['pwd_require_lower']) ? '1' : '0');
                Settings::set('pwd_require_number', !empty($_POST['pwd_require_number']) ? '1' : '0');
                Settings::set('pwd_require_special', !empty($_POST['pwd_require_special']) ? '1' : '0');
                Settings::set('force_2fa_admin', !empty($_POST['force_2fa_admin']) ? '1' : '0');
            }
            if ($tab === 'social') {
                Settings::set('enable_google_login', !empty($_POST['enable_google_login']) ? '1' : '0');
                Settings::set('enable_line_login', !empty($_POST['enable_line_login']) ? '1' : '0');
                Settings::set('enable_facebook_login', !empty($_POST['enable_facebook_login']) ? '1' : '0');
                Settings::set('enable_github_login', !empty($_POST['enable_github_login']) ? '1' : '0');
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
                        }
                    } else {
                        throw new \Exception("File uploaded for {$fileKey} is not a valid image.");
                    }
                }
            }

            Logger::activity("Updated system settings (tab: {$tab}).");
            Session::setFlash('success', __('settings.settings_saved'));
            $this->redirect("/settings/{$tab}");

        } catch (\Exception $e) {
            Session::setFlash('error', __('settings.failed_update_settings') . ' ' . $e->getMessage());
            $this->redirect("/settings/{$tab}");
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
            echo json_encode(['success' => false, 'message' => 'Missing namespace or key.']);
            return;
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
            echo json_encode(['success' => true, 'message' => __('settings.translation_updated')]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

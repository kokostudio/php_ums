<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Security;
use App\Core\Session;
use App\Core\Settings;
use App\Core\Logger;

class SettingController extends Controller
{
    public function index($tab = 'general')
    {
        $tab = in_array($tab, ['general', 'login']) ? $tab : 'general';
        $db = Database::getInstance();
        $settings = $db->query("SELECT setting_key, setting_value FROM system_settings")->fetchAll(\PDO::FETCH_KEY_PAIR);
        
        $this->view('settings/index', ['pageTitle' => 'System Settings', 'settings' => $settings, 'tab' => $tab]);
    }

    public function update($tab = 'general')
    {
        Security::checkCsrf();
        $tab = in_array($tab, ['general', 'login']) ? $tab : 'general';

        // Keys allowed per tab
        $textKeysByTab = [
            'general' => ['site_name', 'site_footer'],
            'login'   => ['login_title', 'login_subtitle'],
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
            Session::setFlash('success', 'Settings saved successfully.');
            $this->redirect("/settings/{$tab}");

        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to update settings: ' . $e->getMessage());
            $this->redirect("/settings/{$tab}");
        }
    }
}

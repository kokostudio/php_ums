<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Lang;

class LangController extends Controller
{
    /**
     * Switch application language and redirect back to previous page
     */
    public function switch($locale)
    {
        $allowed = ['th', 'en'];
        if (in_array($locale, $allowed)) {
            Lang::set($locale);
        }

        $referrer = $_SERVER['HTTP_REFERER'] ?? '/dashboard';
        $this->redirect($referrer);
    }
}

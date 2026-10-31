<?php

namespace App\Core;

abstract class Controller
{
    /**
     * Render a view with optional data and layout
     */
    protected function view(string $view, array $data = [], ?string $layout = 'app'): void
    {
        View::render($view, $data, $layout);
    }

    /**
     * Redirect ไปยังเส้นทางภายในระบบ (relative path เท่านั้น)
     * การตรวจปลายทางรวมศูนย์อยู่ที่ App\Core\Redirect
     */
    protected function redirect(string $url): void
    {
        Redirect::to($url);
    }

    /**
     * Redirect ไปยัง URL ภายนอก (เช่น หน้า Authorize ของ OAuth Provider)
     * บังคับตรวจ host ต่อ allowlist ก่อนเสมอ — ป้องกัน Open Redirect
     */
    protected function redirectExternal(string $url, array $allowedHosts): void
    {
        Redirect::external($url, $allowedHosts);
    }

    /**
     * Return JSON response
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * ตรวจสอบว่า Request ถูกส่งมาแบบ AJAX (axios / fetch) หรือไม่
     * (logic รวมศูนย์อยู่ที่ App\Core\Request::wantsJson)
     */
    protected function isAjax(): bool
    {
        return Request::wantsJson();
    }

    /**
     * Dual-Response: ตอบ JSON เมื่อเป็น AJAX, ตอบ Redirect + Flash เมื่อเป็นฟอร์มปกติ
     * ทำให้ฟอร์มเดิม (ไม่มี data-ajax) ยังทำงานได้ 100% เหมือนเดิม
     *
     * @param string $redirectUrl ปลายทางหลังทำงาน (ใช้ทั้งโหมด Redirect และส่งให้ JS)
     * @param string $message     ข้อความแจ้งเตือน (ต้องผ่าน __() มาแล้ว)
     * @param string $type        success | error | warning | info
     * @param int    $statusCode  HTTP status สำหรับโหมด AJAX
     * @param array  $extra       ข้อมูลเพิ่มเติม เช่น ['reload' => true, 'errors' => [...]]
     */
    protected function respond(string $redirectUrl, string $message, string $type = 'success', int $statusCode = 200, array $extra = []): void
    {
        if ($this->isAjax()) {
            $this->json(array_merge([
                'success'  => in_array($type, ['success', 'info'], true),
                'type'     => $type,
                'message'  => $message,
                'redirect' => $redirectUrl,
            ], $extra), $statusCode);
        }

        Session::setFlash($type, $message);
        $this->redirect($redirectUrl);
    }
}

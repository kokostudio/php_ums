<?php
/**
 * Global AJAX Form Engine (Dual-Response Client)
 * ------------------------------------------------------------------
 * ดักจับการ submit ของทุกฟอร์มที่มี data-ajax="true" (หรือ class .ajax-form)
 * แล้วส่งข้อมูลผ่าน axios พร้อมปุ่ม Loading (Rule 27) และ Toast บนกลาง (Rule 28)
 * ใช้คู่กับ App\Core\Controller::respond() (Dual-Response Pattern)
 *
 * เงื่อนไขการโหลด: ต้อง require หลัง SweetAlert2 และ axios (ถ้ามี) เท่านั้น
 * Layout ใดที่ยังไม่มี showTopToast / withButtonLoading จะได้รับ Fallback อัตโนมัติ
 */
?>
<script>
(function () {
    'use strict';

    var FALLBACK_SUCCESS = <?= json_encode(__('common.success')) ?>;
    var FALLBACK_ERROR   = <?= json_encode(__('common.unexpected_error')) ?>;
    var PLEASE_WAIT      = <?= json_encode(__('common.please_wait')) ?>;
    var SESSION_EXPIRED  = <?= json_encode(__('messages.session_expired')) ?>;

    /* ── Toast CSS (Fade Down/Up) — inject เมื่อหน้านั้นยังไม่มี เช่น layouts/auth.php
          (บน layouts/app.php มีอยู่แล้ว การ inject ซ้ำเป็น CSS ชุดเดียวกัน ไม่กระทบผล) ── */
    if (!document.getElementById('ajaxEngineToastCss')) {
        var toastCss = document.createElement('style');
        toastCss.id = 'ajaxEngineToastCss';
        toastCss.textContent =
            '@keyframes swalToastFadeDown{0%{opacity:0;transform:translate3d(0,-32px,0) scale(0.96)}100%{opacity:1;transform:translate3d(0,0,0) scale(1)}}' +
            '@keyframes swalToastFadeUp{0%{opacity:1;transform:translate3d(0,0,0) scale(1)}100%{opacity:0;transform:translate3d(0,-32px,0) scale(0.96)}}' +
            '.swal2-popup.swal2-toast.swal2-show,.swal2-container.swal2-top .swal2-popup.swal2-toast.swal2-show,.swal2-toast-fade-down{animation:swalToastFadeDown 0.35s cubic-bezier(0.16,1,0.3,1) !important}' +
            '.swal2-popup.swal2-toast.swal2-hide,.swal2-container.swal2-top .swal2-popup.swal2-toast.swal2-hide,.swal2-toast-fade-up{animation:swalToastFadeUp 0.25s cubic-bezier(0.16,1,0.3,1) forwards !important}';
        document.head.appendChild(toastCss);
    }

    /* ── Fallback Toast (สไตล์เดียวกับ window.Toast ของ layouts/app.php ทุกจุด)
          ใช้เมื่อ layout ยังไม่มี showTopToast เช่น layouts/auth.php ── */
    if (typeof window.showTopToast !== 'function') {
        window.showTopToast = function (icon, title, timer) {
            if (!window.Swal) return Promise.resolve();
            timer = timer || 3000;
            var Toast = Swal.mixin({
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: timer,
                timerProgressBar: true,
                width: 'auto',
                customClass: {
                    popup: 'dark:bg-slate-800 dark:text-white border border-gray-100 dark:border-slate-700 shadow-xl',
                    title: 'text-[13px] font-medium font-sans whitespace-nowrap px-2'
                },
                showClass: { popup: 'swal2-toast-fade-down' },
                hideClass: { popup: 'swal2-toast-fade-up' },
                didOpen: function (toast) {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            return Toast.fire({ icon: icon, title: title });
        };
    }

    /* ── Fallback ปุ่ม Loading (Rule 27) ── */
    if (typeof window.withButtonLoading !== 'function') {
        window.withButtonLoading = async function (btn, actionFn, loadingText) {
            if (!btn || btn.nodeType !== 1) {
                return typeof actionFn === 'function' ? await actionFn() : null;
            }
            var origHtml = btn.innerHTML;
            var origWidth = btn.offsetWidth ? (btn.offsetWidth + 'px') : 'auto';
            btn.style.minWidth = origWidth;
            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-not-allowed');
            var label = loadingText || PLEASE_WAIT;
            btn.innerHTML = '<i class="ph ph-circle-notch animate-spin text-base mr-1.5"></i><span></span>';
            btn.querySelector('span').textContent = label;
            try {
                if (typeof actionFn === 'function') {
                    return await actionFn();
                }
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-80', 'cursor-not-allowed');
                btn.innerHTML = origHtml;
                btn.style.minWidth = '';
            }
        };
    }

    /* ── Validation Error: ล้าง/แสดงข้อความใต้ input (Form Validation Standard) ── */
    window.clearAjaxFieldErrors = function (form) {
        form.querySelectorAll('.ajax-field-error').forEach(function (el) { el.remove(); });
        form.querySelectorAll('[data-ajax-invalid="1"]').forEach(function (input) {
            input.classList.remove('border-red-500', 'dark:border-red-500');
            input.removeAttribute('data-ajax-invalid');
        });
    };

    window.highlightAjaxFieldErrors = function (form, errors) {
        Object.keys(errors || {}).forEach(function (name) {
            var messages = Array.isArray(errors[name]) ? errors[name] : [errors[name]];
            var text = messages.filter(Boolean).join(' ');
            if (!text) return;

            var input = form.querySelector('[name="' + name + '"]');
            if (!input) return;

            // ถ้าหน้านั้นมีกล่อง error ของตัวเองอยู่แล้ว (เช่น #username-error ในหน้า Login) ให้ใช้กล่องเดิม
            var errEl = document.getElementById(name + '-error');
            if (!errEl || !form.contains(errEl)) {
                errEl = document.createElement('p');
                errEl.className = 'ajax-field-error text-xs text-red-500 mt-1';
                (input.parentElement || input).insertAdjacentElement('afterend', errEl);
            }
            errEl.textContent = text;
            errEl.classList.remove('hidden');

            input.classList.add('border-red-500', 'dark:border-red-500');
            input.setAttribute('data-ajax-invalid', '1');
            input.addEventListener('input', function onInput() {
                input.classList.remove('border-red-500', 'dark:border-red-500');
                input.removeAttribute('data-ajax-invalid');
                errEl.classList.add('hidden');
                errEl.textContent = '';
                input.removeEventListener('input', onInput);
            });
        });
    };

    /* ── ปุ่ม "ส่งอีเมลยืนยันอีกครั้ง" กรณี Login ด้วยบัญชีที่ยังไม่ได้ยืนยันอีเมล ── */
    window.revealResendVerification = function (afterForm, email) {
        if (document.getElementById('resendVerificationWrap')) return;
        if (!afterForm || !afterForm.parentElement || !window.axios) return;

        var wrap = document.createElement('div');
        wrap.id = 'resendVerificationWrap';
        wrap.className = 'mt-4';

        var form = document.createElement('form');
        form.action = '/resend-verification';
        form.method = 'POST';
        form.setAttribute('data-ajax', 'true');

        var csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = 'csrf_token';
        csrf.value = window.axios.defaults.headers.common['X-CSRF-TOKEN'] || '';

        var emailInput = document.createElement('input');
        emailInput.type = 'hidden';
        emailInput.name = 'email';
        emailInput.value = email || '';

        var btn = document.createElement('button');
        btn.type = 'submit';
        btn.className = 'w-full flex justify-center items-center px-4 py-2 rounded-xl text-sm font-medium text-blue-600 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors';
        btn.innerHTML = '<i class="ph ph-envelope-simple mr-2"></i> <span></span>';
        btn.querySelector('span').textContent = <?= json_encode(__('auth.resend_verification_email'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

        form.appendChild(csrf);
        form.appendChild(emailInput);
        form.appendChild(btn);
        wrap.appendChild(form);
        afterForm.insertAdjacentElement('afterend', wrap);
    };

    /* ── ปิด Modal ที่เปิดอยู่ (ใช้กับ flag modal_close จาก respond()) ──
       รองรับทั้ง modal แบบ <dialog> และ container ที่ซ่อนด้วย class 'hidden' */
    window.closeActiveModal = function () {
        var openDialog = document.querySelector('dialog[open]');
        if (openDialog) {
            openDialog.close();
            return true;
        }

        var modal = document.querySelector('.modal-container:not(.hidden), [data-modal]:not(.hidden)');
        if (modal) {
            modal.classList.add('hidden');
            modal.dispatchEvent(new CustomEvent('modal:closed', { bubbles: true }));
            return true;
        }
        return false;
    };

    /* ── ตัวจัดการผลลัพธ์ JSON จาก Controller::respond() ──
       Contract: { success, type, message, redirect, errors, reload_table, modal_close, session_expired } */
    window.handleAjaxFormResult = function (form, payload) {
        // กัน "toast สำเร็จปลอม": ถ้า server ตอบกลับมาเป็น HTML (เช่น ตาม redirect ไปหน้า login
        // หลัง session หมดอายุ) ไม่ใช่ object ตาม contract — ถือว่าเซสชันหมดอายุ ไม่ใช่ความสำเร็จ
        if (!payload || typeof payload !== 'object') {
            payload = { success: false, type: 'error', session_expired: true, message: SESSION_EXPIRED };
        }

        var type = payload.type || (payload.success === false ? 'error' : 'success');
        var isError = payload.success === false || type === 'error' || type === 'warning';
        var message = payload.message || (isError ? FALLBACK_ERROR : FALLBACK_SUCCESS);

        // เซสชันหมดอายุ (401 จาก AuthMiddleware หรือ 419 CSRF): แจ้งแล้วพาไปหน้าเข้าสู่ระบบ
        if (payload.session_expired) {
            window.showTopToast('error', message, 4000);
            setTimeout(function () {
                window.location.href = payload.redirect || '/login';
            }, 1200);
            return;
        }

        if (isError) {
            window.showTopToast(type === 'warning' ? 'warning' : 'error', message, 4000);
            if (payload.errors) window.highlightAjaxFieldErrors(form, payload.errors);
            if (payload.unverified_email) window.revealResendVerification(form, payload.unverified_email);
            return;
        }

        // เก็บ Toast ไว้แสดงต่อหลัง redirect จบ (กัน Toast ถูกตัดระหว่างโหลดหน้าใหม่)
        // stay = true คือโหมด "อยู่หน้าเดิม" — ไม่ navigate จึงไม่ต้อง stash
        if (payload.redirect && !payload.stay) {
            try {
                sessionStorage.setItem('pendingAjaxToast', JSON.stringify({
                    icon: type === 'info' ? 'info' : 'success',
                    title: message
                }));
            } catch (e) { /* sessionStorage ใช้ไม่ได้ ข้ามไปได้ปลอดภัย */ }
        }

        if (payload.modal_close && typeof window.closeActiveModal === 'function') {
            window.closeActiveModal();
        }

        if (payload.reload_table && !payload.redirect && window.jQuery && jQuery.fn.DataTable) {
            jQuery('table.dataTable').each(function () {
                jQuery(this).DataTable().ajax.reload(null, false);
            });
        }

        // เคลียร์ฟอร์มรอรับข้อมูลใหม่ (เช่นหน้า Create ที่เพิ่มข้อมูลต่อเนื่อง)
        if (payload.reset_form) {
            form.reset();
            window.clearAjaxFieldErrors(form);
            form.dispatchEvent(new CustomEvent('ajax:form-reset', { bubbles: true }));
        }

        // แจ้ง event ให้หน้าเพจที่สนใจอัปเดต UI เองได้ทันที (เช่น preview รูปที่เพิ่งอัปโหลด)
        form.dispatchEvent(new CustomEvent('ajax:form-saved', { bubbles: true, detail: payload }));

        // Redirect flow (เช่น Login → Dashboard): stash Toast ไว้แล้วเดินทางทันที
        // Toast จะแสดง "รอบเดียว" ที่ปลายทางด้วยสไตล์มาตรฐานของระบบ — ไม่แสดงซ้ำสองหน้า
        if (payload.redirect && !payload.stay) {
            setTimeout(function () { window.location.href = payload.redirect; }, 300);
        } else {
            window.showTopToast(type === 'info' ? 'info' : 'success', message);
        }
    };

    /* ── Global AJAX Form Interceptor (จับ submit ระดับ document ครั้งเดียวจบ) ── */
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form.nodeType !== 1) return;
        if (!form.matches('form[data-ajax="true"], form.ajax-form')) return;
        if (e.defaultPrevented) return; // ให้ความสำคัญกับ client-side validation ของหน้านั้นๆ

        var method = (form.getAttribute('method') || 'POST').toUpperCase();
        if (method === 'GET') return; // ฟอร์ม GET ปล่อยให้ทำงานแบบ native

        e.preventDefault();

        if (form.dataset.ajaxBusy === 'true') return;
        form.dataset.ajaxBusy = 'true';

        // e.submitter = ปุ่มที่กดจริง (รองรับปุ่มที่อยู่ "นอก" ฟอร์มผ่าน attribute form="...")
        var submitBtn = e.submitter || form.querySelector('button[type="submit"], input[type="submit"]');
        var formData = new FormData(form);

        window.clearAjaxFieldErrors(form);

        window.withButtonLoading(submitBtn, async function () {
            try {
                var response = await axios({
                    method: method,
                    url: form.getAttribute('action') || window.location.href,
                    data: formData
                });
                // ตรวจชนิดข้อมูลก่อนส่งต่อ — กัน HTML/string หลุดเข้ามาถูกตีความเป็นความสำเร็จ
                window.handleAjaxFormResult(form, response.data);
            } catch (error) {
                var res = error && error.response;
                var payload = (res && res.data && typeof res.data === 'object')
                    ? res.data
                    : { success: false, type: 'error', message: FALLBACK_ERROR };

                // 401 = เซสชันหมดอายุ (AuthMiddleware ตอบ JSON พร้อม flag) — ให้ branch session_expired จัดการ
                if (res && res.status === 401 && !payload.session_expired) {
                    payload.session_expired = true;
                    payload.message = payload.message || SESSION_EXPIRED;
                }

                window.handleAjaxFormResult(form, payload);
            } finally {
                form.dataset.ajaxBusy = 'false';
            }
        }, PLEASE_WAIT);
    });

    /* ── เติม Toast ค้างจากหน้าก่อนหน้า (AJAX redirect ต่อเนื่อง) ── */
    document.addEventListener('DOMContentLoaded', function () {
        try {
            var pending = sessionStorage.getItem('pendingAjaxToast');
            if (!pending) return;
            sessionStorage.removeItem('pendingAjaxToast');
            var t = JSON.parse(pending);
            if (t && t.title && typeof window.showTopToast === 'function') {
                window.showTopToast(t.icon || 'success', t.title, t.timer || 3000);
            }
        } catch (err) {
            try { sessionStorage.removeItem('pendingAjaxToast'); } catch (e2) { /* ข้าม */ }
        }
    });
})();
</script>

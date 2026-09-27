<h2>Password Reset Request</h2>
<p>Hello <?= \App\Core\View::escape($username ?? 'User') ?>,</p>
<p>You are receiving this email because we received a password reset request for your account.</p>
<div style="text-align: center;">
    <a href="<?= \App\Core\View::escape($resetLink ?? '#') ?>" class="btn" style="color: #ffffff;">Reset Password</a>
</div>
<p>If the button doesn't work, you can copy and paste the following link into your browser:</p>
<p class="text-sm text-muted" style="word-break: break-all;">
    <a href="<?= \App\Core\View::escape($resetLink ?? '#') ?>"><?= \App\Core\View::escape($resetLink ?? '#') ?></a>
</p>
<p class="text-sm">This password reset link will expire in 60 minutes.</p>
<hr class="hr">
<p class="text-sm text-muted">
    If you did not request a password reset, no further action is required.
</p>

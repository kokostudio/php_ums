<h2>Verify your email address</h2>
<p>Hello <?= \App\Core\View::escape($username ?? 'User') ?>,</p>
<p>Thank you for registering! Please confirm your email address by clicking the button below:</p>
<div style="text-align: center;">
    <a href="<?= \App\Core\View::escape($verifyLink ?? '#') ?>" class="btn" style="color: #ffffff;">Verify Email Address</a>
</div>
<p>If the button doesn't work, you can copy and paste the following link into your browser:</p>
<p class="text-sm text-muted" style="word-break: break-all;">
    <a href="<?= \App\Core\View::escape($verifyLink ?? '#') ?>"><?= \App\Core\View::escape($verifyLink ?? '#') ?></a>
</p>
<hr class="hr">
<p class="text-sm text-muted">
    If you did not create an account, no further action is required.
</p>

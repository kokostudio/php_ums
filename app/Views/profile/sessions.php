

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?= __('profile.active_sessions') ?></h1>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1"><?= __('profile.manage_sessions_desc') ?></p>
    </div>
    <div>
        <a href="/profile" class="px-4 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm font-medium text-gray-600 dark:text-slate-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-300 hover:border-primary-200 dark:hover:border-primary-800 transition-colors inline-flex items-center">
            <i class="ph ph-arrow-left mr-2"></i> <?= __('profile.back_to_profile') ?>
        </a>
    </div>
</div>

<div class="bg-white dark:bg-black/20 dark:backdrop-blur-md rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden transition-colors">
    <div class="overflow-x-auto w-full">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider"><?= __('profile.device_browser') ?></th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider"><?= __('profile.ip_address') ?></th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider"><?= __('profile.last_activity') ?></th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right"><?= __('profile.action') ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                <?php foreach ($sessions as $session): ?>
                    <?php 
                        $isCurrent = ($session['id'] === $currentSessionId);
                        // Simple parser for user agent to make it readable
                        $ua = $session['user_agent'] ?? '';
                        $device = __('profile.unknown_device');
                        $browser = __('profile.unknown_browser');
                        $icon = 'ph-desktop';

                        if (preg_match('/(Windows|Mac|Linux)/i', $ua, $matches)) $device = $matches[1];
                        if (preg_match('/(iPhone|iPad|Android)/i', $ua, $matches)) {
                            $device = $matches[1];
                            $icon = 'ph-device-mobile';
                        }
                        
                        if (preg_match('/(Chrome|Firefox|Safari|Edge|Opera)/i', $ua, $matches)) $browser = $matches[1];
                    ?>
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                        <td class="py-4 px-6">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 mr-4 shrink-0">
                                    <i class="ph <?= $icon ?> text-xl"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white flex items-center">
                                        <?= htmlspecialchars($device . ' - ' . $browser) ?>
                                        <?php if ($isCurrent): ?>
                                            <span class="ml-2 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-green-700 bg-green-100 dark:text-green-400 dark:bg-green-900/30 rounded"><?= __('profile.this_device') ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-slate-400 truncate max-w-xs" title="<?= htmlspecialchars($ua) ?>">
                                        <?= htmlspecialchars($ua) ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-600 dark:text-slate-300">
                            <?= htmlspecialchars($session['ip_address'] ?? '') ?>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-600 dark:text-slate-300">
                            <?= date('d/m/Y H:i', $session['last_activity']) ?>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <?php if (!$isCurrent): ?>
                                <form action="/profile/sessions/<?= htmlspecialchars($session['id']) ?>/revoke" method="POST" class="inline m-0">
                                    <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">
                                    <button type="button" onclick="confirmRevoke(this)" class="px-3 py-1.5 text-xs font-medium text-white bg-red-500 hover:bg-red-600 rounded transition-colors shadow-sm">
                                        <i class="ph ph-trash mr-1"></i> <?= __('profile.revoke') ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-xs text-gray-400 dark:text-slate-500 italic"><?= __('profile.current_session') ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if (empty($sessions)): ?>
                    <tr>
                        <td colspan="4" class="py-8 text-center text-sm text-gray-500 dark:text-slate-400">
                            <?= __('profile.no_active_sessions') ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function confirmRevoke(button) {
    Swal.fire({
        title: '<?= __('common.confirm') ?>',
        text: '<?= __('profile.revoke_confirm') ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<?= __('common.yes') ?>',
        cancelButtonText: '<?= __('common.cancel') ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            button.closest('form').submit();
        }
    });
}
</script>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จอแสดงคิวรับอาหาร - <?= htmlspecialchars(\App\Core\Settings::get('site_name', 'CORE RESTAURANT')) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { font-family: 'Prompt', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-white min-h-screen flex flex-col select-none overflow-hidden">

    <!-- Top Header Bar -->
    <header class="h-20 bg-slate-900 border-b border-slate-800 px-8 flex items-center justify-between shadow-lg">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center font-black text-2xl shadow-md shadow-amber-500/20">
                <i class="ph ph-bell-ringing"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black tracking-tight text-white uppercase"><?= htmlspecialchars(\App\Core\Settings::get('site_name', 'CORE RESTAURANT')) ?></h1>
                <p class="text-xs text-slate-400 font-semibold tracking-wider">ระบบเรียกคิวรับอาหาร (QSR & ORDER QUEUE)</p>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <button id="voiceToggleBtn" onclick="toggleVoice()" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-amber-400 border border-slate-700 flex items-center gap-1.5 transition-all">
                <i class="ph ph-speaker-high text-base"></i>
                <span id="voiceStatusText">เสียงเรียกคิว: เปิด</span>
            </button>
            <button onclick="testSpeech()" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-300 border border-slate-700 flex items-center gap-1.5 transition-all">
                <i class="ph ph-play-circle text-base"></i>
                <span>ทดสอบเสียง</span>
            </button>
            <div class="text-right pl-2 border-l border-slate-800">
                <div id="liveClock" class="text-3xl font-black font-mono tracking-wider text-amber-400">12:00:00</div>
                <div id="liveDate" class="text-xs text-slate-400 font-medium">วันพฤหัสบดี</div>
            </div>
        </div>
    </header>

    <!-- Main Dual Screen Content -->
    <main class="flex-1 grid grid-cols-2 p-6 gap-6 overflow-hidden">
        
        <!-- Left Column: Preparing -->
        <div class="bg-slate-900/80 rounded-3xl border-2 border-amber-500/30 p-6 flex flex-col shadow-2xl backdrop-blur-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div class="flex items-center space-x-3">
                    <span class="w-4 h-8 rounded-full bg-amber-500 inline-block animate-pulse"></span>
                    <h2 class="text-2xl font-black text-amber-400 uppercase tracking-tight">กำลังเตรียมอาหาร</h2>
                </div>
                <span class="text-sm font-bold text-slate-400 font-mono" id="preparingCount"><?= count($preparing) ?> คิว</span>
            </div>

            <!-- Queue Numbers Grid -->
            <div id="preparingList" class="flex-1 grid grid-cols-2 sm:grid-cols-3 gap-4 pt-6 content-start overflow-y-auto pr-1">
                <?php if (empty($preparing)): ?>
                    <div class="col-span-full py-20 text-center text-slate-600 font-bold text-lg">
                        - ไม่มีรายการที่กำลังเตรียม -
                    </div>
                <?php else: ?>
                    <?php foreach ($preparing as $item): ?>
                        <div class="p-4 rounded-2xl bg-slate-800/80 border border-slate-700/60 text-center shadow-md">
                            <span class="text-3xl font-black text-amber-300 tracking-tight font-mono"><?= htmlspecialchars($item['label']) ?></span>
                            <span class="block text-[11px] text-slate-400 mt-1"><?= $item['order_type'] === 'takeaway' ? 'กลับบ้าน' : 'ทานที่ร้าน' ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Ready for Pickup -->
        <div class="bg-slate-900/80 rounded-3xl border-2 border-emerald-500/50 p-6 flex flex-col shadow-2xl backdrop-blur-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div class="flex items-center space-x-3">
                    <span class="w-4 h-8 rounded-full bg-emerald-500 inline-block animate-ping"></span>
                    <h2 class="text-2xl font-black text-emerald-400 uppercase tracking-tight">พร้อมรับอาหาร</h2>
                </div>
                <span class="text-sm font-bold text-emerald-400 font-mono" id="readyCount"><?= count($ready) ?> คิว</span>
            </div>

            <!-- Ready Numbers Grid -->
            <div id="readyList" class="flex-1 grid grid-cols-2 sm:grid-cols-3 gap-4 pt-6 content-start overflow-y-auto pr-1">
                <?php if (empty($ready)): ?>
                    <div class="col-span-full py-20 text-center text-slate-600 font-bold text-lg">
                        - ไม่มีรายการรอรับ -
                    </div>
                <?php else: ?>
                    <?php foreach ($ready as $item): ?>
                        <div class="p-4 rounded-2xl bg-emerald-950/60 border-2 border-emerald-500 text-center shadow-lg shadow-emerald-950/50 animate-bounce">
                            <span class="text-4xl font-black text-emerald-300 tracking-tight font-mono"><?= htmlspecialchars($item['label']) ?></span>
                            <span class="block text-xs font-bold text-emerald-400 mt-1">เชิญรับอาหาร</span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </main>

    <!-- Bottom Running Ticker -->
    <footer class="h-12 bg-slate-900 border-t border-slate-800 px-8 flex items-center justify-between text-xs text-slate-400">
        <div class="flex items-center space-x-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>กรุณาตรวจสอบหมายเลขคิว หรือหมายเลขโต๊ะบนใบเสร็จ/หน้าจอมือถือของคุณ</span>
        </div>
        <div>
            <span>เมื่ออาหารพร้อมรับ สามารถติดต่อรับอาหารได้ที่เคาน์เตอร์รับอาหาร ขอบคุณครับ</span>
        </div>
    </footer>

<script>
// Digital Clock
function updateClock() {
    const now = new Date();
    document.getElementById('liveClock').textContent = now.toLocaleTimeString('th-TH', { hour12: false });
    document.getElementById('liveDate').textContent = now.toLocaleDateString('th-TH', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
}
setInterval(updateClock, 1000);
updateClock();

// Web Audio Chime on ready queue
let isVoiceEnabled = localStorage.getItem('pos_queue_voice') !== 'false';
let announcedOrderIds = new Set();
let lastReadyCount = <?= count($ready) ?>;

// Initialize already ready order IDs so we don't announce all existing orders on page load
<?php foreach ($ready as $r): ?>
announcedOrderIds.add(<?= (int)$r['id'] ?>);
<?php endforeach; ?>

function toggleVoice() {
    isVoiceEnabled = !isVoiceEnabled;
    localStorage.setItem('pos_queue_voice', isVoiceEnabled);
    updateVoiceButtonUI();
}

function updateVoiceButtonUI() {
    const text = document.getElementById('voiceStatusText');
    const btn = document.getElementById('voiceToggleBtn');
    if (isVoiceEnabled) {
        text.textContent = 'เสียงเรียกคิว: เปิด';
        btn.className = 'px-3 py-1.5 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/40 text-xs font-bold flex items-center gap-1.5 transition-all';
    } else {
        text.textContent = 'เสียงเรียกคิว: ปิด';
        btn.className = 'px-3 py-1.5 rounded-xl bg-slate-800 text-slate-400 border border-slate-700 text-xs font-bold flex items-center gap-1.5 transition-all';
    }
}
updateVoiceButtonUI();

function playPickupChime() {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        
        // Two-tone pleasant chime (Ding-Dong)
        const playTone = (freq, start, duration) => {
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.type = 'triangle';
            osc.frequency.value = freq;
            gain.gain.setValueAtTime(0.3, start);
            gain.gain.exponentialRampToValueAtTime(0.001, start + duration);
            osc.start(start);
            osc.stop(start + duration);
        };

        const t = audioCtx.currentTime;
        playTone(587.33, t, 0.4);       // D5
        playTone(880.00, t + 0.25, 0.6); // A5
    } catch(e) {}
}

function speakThai(text) {
    if (!isVoiceEnabled || !('speechSynthesis' in window)) return;
    
    window.speechSynthesis.cancel(); // Cancel any ongoing speech
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'th-TH';
    utterance.rate = 0.95;
    utterance.pitch = 1.05;

    // Pick a Thai voice if available
    const voices = window.speechSynthesis.getVoices();
    const thaiVoice = voices.find(v => v.lang.includes('th') || v.lang.includes('TH'));
    if (thaiVoice) {
        utterance.voice = thaiVoice;
    }

    window.speechSynthesis.speak(utterance);
}

function announceQueueOrder(label) {
    playPickupChime();
    
    // Format spoken phrase
    // e.g. "Q-01" -> "คิว คิว ศูนย์ หนึ่ง"
    // e.g. "โต๊ะ T01" -> "โต๊ะ ที ศูนย์ หนึ่ง"
    let spokenLabel = label
        .replace(/Q-/i, 'คิว คิว ')
        .replace(/T-/i, 'ที ')
        .replace(/T([0-9]+)/i, 'ที $1')
        .replace(/0/g, 'ศูนย์ ')
        .replace(/1/g, 'หนึ่ง ')
        .replace(/2/g, 'สอง ')
        .replace(/3/g, 'สาม ')
        .replace(/4/g, 'สี่ ')
        .replace(/5/g, 'ห้า ')
        .replace(/6/g, 'หก ')
        .replace(/7/g, 'เจ็ด ')
        .replace(/8/g, 'แปด ')
        .replace(/9/g, 'เก้า ');

    const phrase = `ขอเชิญ ${spokenLabel} รับอาหารที่เคาน์เตอร์ค่ะ`;

    setTimeout(() => {
        speakThai(phrase);
    }, 800);
}

function testSpeech() {
    announceQueueOrder('Q-01');
}

function fetchQueueData() {
    fetch('/pos/queue/data')
    .then(res => res.json())
    .then(res => {
        if (res.success && res.data) {
            const prep = res.data.preparing || [];
            const rdy = res.data.ready || [];

            document.getElementById('preparingCount').textContent = prep.length + ' คิว';
            document.getElementById('readyCount').textContent = rdy.length + ' คิว';

            // Check for new ready items to announce
            rdy.forEach(item => {
                if (!announcedOrderIds.has(item.id)) {
                    announcedOrderIds.add(item.id);
                    announceQueueOrder(item.label);
                }
            });

            // Render Preparing
            const prepEl = document.getElementById('preparingList');
            if (prep.length === 0) {
                prepEl.innerHTML = '<div class="col-span-full py-20 text-center text-slate-600 font-bold text-lg">- ไม่มีรายการที่กำลังเตรียม -</div>';
            } else {
                prepEl.innerHTML = prep.map(item => `
                    <div class="p-4 rounded-2xl bg-slate-800/80 border border-slate-700/60 text-center shadow-md">
                        <span class="text-3xl font-black text-amber-300 tracking-tight font-mono">${escapeHtml(item.label)}</span>
                        <span class="block text-[11px] text-slate-400 mt-1">${item.order_type === 'takeaway' ? 'กลับบ้าน' : 'ทานที่ร้าน'}</span>
                    </div>
                `).join('');
            }

            // Render Ready
            const rdyEl = document.getElementById('readyList');
            if (rdy.length === 0) {
                rdyEl.innerHTML = '<div class="col-span-full py-20 text-center text-slate-600 font-bold text-lg">- ไม่มีรายการรอรับ -</div>';
            } else {
                rdyEl.innerHTML = rdy.map(item => `
                    <div class="p-4 rounded-2xl bg-emerald-950/60 border-2 border-emerald-500 text-center shadow-lg shadow-emerald-950/50">
                        <span class="text-4xl font-black text-emerald-300 tracking-tight font-mono">${escapeHtml(item.label)}</span>
                        <span class="block text-xs font-bold text-emerald-400 mt-1">เชิญรับอาหาร</span>
                    </div>
                `).join('');
            }
        }
    })
    .catch(err => console.log('Queue polling error:', err));
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// Poll every 3 seconds for fast TV display sync
setInterval(fetchQueueData, 3000);
</script>
</body>
</html>

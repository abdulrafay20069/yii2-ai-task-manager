<?php
use yii\helpers\Url;
$this->title = 'AI Assistant';
?>
<h1>AI Assistant</h1>

<div id="log" class="border rounded p-3 mb-3" style="height:320px; overflow-y:auto;"></div>

<div class="input-group mb-2">
    <input id="msg" type="text" class="form-control" placeholder="Kuch poochho, ya mic dabao...">
    <button id="send" class="btn btn-primary">Send</button>
    <button id="mic" class="btn btn-outline-secondary">🎤</button>
</div>
<button id="report" class="btn btn-success">Generate AI task report</button>

<script>
const log = document.getElementById('log');
const input = document.getElementById('msg');
let history = [];

function add(who, text) {
    const div = document.createElement('div');
    div.className = 'mb-2';
    const b = document.createElement('strong');
    b.textContent = who + ': ';
    const s = document.createElement('span');
    s.style.whiteSpace = 'pre-wrap';
    s.textContent = text;
    div.append(b, s);
    log.appendChild(div);
    log.scrollTop = log.scrollHeight;
}

async function call(url, params) {
    const res = await fetch(url, {method: 'POST', body: new URLSearchParams(params || {})});
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || 'Request failed');
    return data.reply;
}

async function send(text) {
    text = (text ?? input.value).trim();
    if (!text) return;
    input.value = '';
    add('You', text);
    history.push({role: 'user', content: text});
    try {
        const reply = await call('<?= Url::to(['ai/chat']) ?>', {history: JSON.stringify(history)});
        history.push({role: 'assistant', content: reply});
        add('AI', reply);
    } catch (e) { add('Error', e.message); }
}

document.getElementById('send').onclick = () => send();
input.addEventListener('keydown', e => { if (e.key === 'Enter') send(); });

document.getElementById('report').onclick = async () => {
    add('You', 'Task report generate karo');
    try { add('AI', await call('<?= Url::to(['ai/report']) ?>')); }
    catch (e) { add('Error', e.message); }
};

// Voice input (Chrome / Edge): bolo -> text -> chat message
const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
const mic = document.getElementById('mic');
if (!SR) { mic.disabled = true; mic.title = 'Chrome ya Edge use karo'; }
else {
    const rec = new SR();
    rec.lang = 'en-US';
    rec.onresult = e => send(e.results[0][0].transcript);
    rec.onend = () => mic.classList.remove('btn-danger');
    rec.onerror = e => add('Error', 'Mic: ' + e.error);
    mic.onclick = () => { mic.classList.add('btn-danger'); rec.start(); };
}
</script>
<nav class="full-box dashboard-Navbar">
  <ul class="full-box list-unstyled text-right">
    <li class="pull-left">
      <a href="#!" class="btn-menu-dashboard"><i class="zmdi zmdi-more-vert"></i></a>
    </li>

    <!-- NOTIFICACIONES -->
    <li class="dropdown" id="bell-notify">
      <a href="#" class="btn-notify dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="position:relative;">
        <i class="zmdi zmdi-notifications"></i>
        <span id="notif-badge" class="badge notif-badge" style="display:none;">0</span>
      </a>

      <ul class="dropdown-menu notif-panel">
        <!-- Header -->
        <li class="np-header">
          <div class="np-title-row">
            <div class="np-h1">Notificaciones</div>
            <div class="np-menu">
              <button id="notif-tab-all" class="np-tab np-tab-active" type="button">Todos</button>
              <button id="notif-tab-unread" class="np-tab" type="button">No Leidos</button>
            </div>
          </div>
        </li>

        <!-- Lista -->
        <li class="np-scroll">
          <div id="notif-container" class="np-body">
            <div id="np-section-new" class="np-section" style="display:none;">
              <div class="np-section-title">Nuevos</div>
              <ul id="notif-list-new" class="np-list"></ul>
            </div>

            <div id="np-section-earlier" class="np-section" style="display:none;">
              <div class="np-section-title">Recientes
                <a href="<?php echo SERVERURL; ?>notificaciones/" class="np-see-all-inline">Ver Todos</a>
              </div>
              <ul id="notif-list-earlier" class="np-list"></ul>
            </div>

            <div id="np-empty" class="np-empty" style="display:none;">No hay notificaciones</div>
          </div>
        </li>

        <!-- Footer -->
        <li class="np-footer">
          <button id="notif-mark-all" class="np-mark-all" type="button" title="Marcar todo como leído">Marcar Todo</button>
          <a href="<?php echo SERVERURL; ?>notificaciones/" class="np-see-all">Ver Todas</a>
        </li>
      </ul>
    </li>
  </ul>
</nav>

<!-- Sonido de notificación -->
<audio id="notif-sound" preload="auto">
  <source src="<?php echo SERVERURL; ?>views/assets/sounds/notify.mp3" type="audio/mpeg">
  <source src="<?php echo SERVERURL; ?>views/assets/sounds/notify.wav" type="audio/wav">
</audio>



<style>
 /* === Navbar y panel === */
.dashboard-Navbar{ overflow:visible; position:relative; z-index:10; }

.dropdown-menu.notif-panel{
  right:0; left:auto; top:100%;
  width:380px; max-height:560px; padding:0;
  display:none; position:absolute; z-index:1000;
  background:#fff; border:1px solid #e5e7eb; border-radius:10px;
  box-shadow:0 8px 24px rgba(0,0,0,.12);
  overflow:hidden;                 /* recorta header/footer; el scroll va en .np-scroll */
}
#bell-notify.open>.dropdown-menu.notif-panel{ display:block; }

/* Badge */
.notif-badge{
  position:absolute; top:5px; right:2px;
  background:#fc0000; color:#fff; min-width:18px; height:18px;
  line-height:18px; border-radius:12px; font-size:11px; padding:0 5px;
}

/* Header */
.np-header{ padding:14px 14px 10px; border-bottom:1px solid #eef1f4; background:#fff; }
.np-title-row{ display:flex; align-items:center; gap:10px; }
.np-h1{ font-size:20px; font-weight:700; color:#111; }
.np-menu{ margin-left:auto; display:flex; gap:8px; }
.np-tab{ border:1px solid #e5e7eb; background:#6d797e; border-radius:16px; padding:6px 12px; font-size:12px; cursor:pointer; }
.np-tab-active{ background:#1b74e4; color:#fff; border-color:#1b74e4; }

/* === SCROLL DEL CUERPO === */
/* El UL del dropdown no debe comerse la barra del hijo */
.dropdown-menu.notif-panel{ overflow: visible; }

/* El área scrolleable (mejor si es un bloque) */
.np-scroll{
  display:block;
  height: 60vh;           /* altura flexible */
  max-height: 460px;      /* límite superior */
  overflow-y: auto;       /* SIEMPRE barra vertical si hace falta */
  overflow-x: hidden;
  -webkit-overflow-scrolling: touch;
  overscroll-behavior: contain;  /* que no “salte” a la página */
  touch-action: pan-y;           /* gestos verticales dentro */
}

.np-body{ padding:0; }

/* Secciones */
.np-section-title{
  padding:10px 14px; color:#65676b; font-size:12px;
  text-transform:uppercase; letter-spacing:.4px; font-weight:700;
  display:flex; align-items:center; justify-content:space-between;
}
.np-see-all-inline{ font-size:12px; font-weight:600; }

/* Lista */
.np-list{ list-style:none; margin:0; padding:0; }
.np-item{
  display:flex; gap:12px; padding:10px 14px;
  cursor:pointer; position:relative; transition:background .15s ease;
}
.np-item:hover{ background:#f5f7fa; }
.np-item.unread{ background:#f0f6ff; }
.np-item.unread:hover{ background:#e9f2ff; }
.np-item.unread .np-title{ font-weight:700; }
.np-item .np-dot-right{
  position:absolute; right:14px; top:50%; transform:translateY(-50%);
  width:10px; height:10px; background:#1b74e4; border-radius:50%;
}

/* Avatar */
.np-avatar{
  width:40px; height:40px; border-radius:50%;
  background:linear-gradient(135deg,#eaf2ff,#f3f6ff);
  display:flex; align-items:center; justify-content:center; flex:0 0 40px;
  overflow:hidden;
}
.np-avatar i{ font-size:20px; color:#1b74e4; }

/* Contenido */
.np-content{ flex:1; min-width:0; }
.np-title{ margin:0; color:#111; font-size:14px; line-height:1.2; }
.np-msg{ margin:2px 0 0; color:#1b74e4; font-size:13px; line-height:1.2; }
.np-meta{ margin-top:4px; color:#8a8d91; font-size:12px; }

/* Empty & Footer */
.np-empty{ padding:20px; color:#777; text-align:center; }
.np-footer{
  padding:10px 14px; border-top:1px solid #eef1f4; background:#fff;
  display:flex; align-items:center; gap:10px;
}
.np-mark-all{ border:1px solid #e5e7eb; background:#3399f1; padding:6px 12px; border-radius:6px; font-size:12px; cursor:pointer; }
.np-see-all{ margin-left:auto; font-weight:600; }

/* Scrollbar WebKit (opcional) */
.np-scroll::-webkit-scrollbar{ width:8px; }
.np-scroll::-webkit-scrollbar-thumb{ background:#c9d4e3; border-radius:4px; }
.np-scroll::-webkit-scrollbar-track{ background:transparent; }
.notif-sound-nudge{
    position: fixed; z-index: 2000; right: 16px; bottom: 16px;
    display: none; padding: 10px 12px; border-radius: 8px;
    background:#1b74e4; color:#fff; box-shadow: 0 8px 24px rgba(0,0,0,.18);
    cursor: pointer; font-size: 14px; line-height: 1.2;
  }
  .notif-sound-nudge b{font-weight:700}



</style>
<div id="notif-sound-nudge" class="notif-sound-nudge" role="button" aria-label="Habilitar sonido de notificaciones">
  🔔 <b>Tienes nuevas notificaciones</b>
</div>
<script>
/* ====================== AUDIO: setup ====================== */
const audioEl = document.getElementById('notif-sound');
let AUDIO_READY = false;                 // ¿ya “primamos” el audio?
let FIRST_FILL_DONE = false;             // para no sonar en la 1ª carga en esta sesión
let SEEN_IDS = new Set();                // ids de no leídas ya vistas (sesión)
const NUDGE = document.getElementById('notif-sound-nudge'); // banner

if (audioEl) {
  audioEl.volume = 1.0;
  audioEl.addEventListener('error', () => {
    console.error('[notif] No se pudo cargar el audio. Revisa la ruta:', audioEl.currentSrc);
  });
} else {
  console.warn('[notif] Falta #notif-sound en el DOM');
}

/** “Primar” el audio con el primer gesto del usuario */
function primeAudio(){
  if (!audioEl || AUDIO_READY) return;
  audioEl.play()
    .then(()=>{ audioEl.pause(); audioEl.currentTime = 0; AUDIO_READY = true; hideNudge(); })
    .catch(()=>{ /* aún bloqueado; seguirá pidiendo gesto */ });
}

document.addEventListener('pointerdown', primeAudio, {once:true});
document.addEventListener('keydown',     primeAudio, {once:true});

/** Reproducir sonido (si ya está primado suena; si no, muestra nudge) */
function playNotifSoundOrNudge(newCount){
  if (!audioEl) return;
  if (AUDIO_READY) {
    audioEl.currentTime = 0;
    audioEl.play().catch(err=>console.warn('[notif] play bloqueado:', err));
  } else {
    // Sin gesto después de un reload: mostramos nudge para pedir click
    showNudge(newCount);
  }
}

function showNudge(newCount){
  if (!NUDGE) return;
  NUDGE.innerHTML = `🔔 <b>Tienes ${newCount} nueva(s) notificación(es)</b>`;
  NUDGE.style.display = 'inline-block';
}
function hideNudge(){
  if (NUDGE) NUDGE.style.display = 'none';
}
if (NUDGE) {
  NUDGE.addEventListener('click', () => {
    primeAudio();  // intenta primar
    // si se pudo primar, sonamos ya mismo:
    setTimeout(()=>{ if (AUDIO_READY) { audioEl.currentTime = 0; audioEl.play().catch(()=>{}); } }, 50);
  });
}


/* =================== Notificaciones nativas (opcional) =================== */
function ensureNotifPermission(){
  if (!('Notification' in window)) return false;
  if (Notification.permission === 'granted') return true;
  if (Notification.permission !== 'denied') Notification.requestPermission();
  return Notification.permission === 'granted';
}
document.addEventListener('pointerdown', ensureNotifPermission, {once:true});
document.addEventListener('keydown',     ensureNotifPermission, {once:true});

function showDesktopNotification(title, body, url){
  if (!('Notification' in window) || Notification.permission !== 'granted') return;
  const n = new Notification(title || 'Nueva notificación', {
    body: body || '',
    icon: "<?php echo SERVERURL; ?>views/assets/img/favicon.png"
  });
  if (url) {
    n.onclick = function(){
      const go = /^https?:/i.test(url) ? url : ("<?php echo SERVERURL; ?>" + url.replace(/^\/+/, ''));
      window.focus(); window.location = go; n.close();
    };
  }
}

/* ============== Persistencia entre recargas (localStorage) ============== */
/* Guardamos un set (hasta 200) de IDs no leídos de la última respuesta */
const LS_KEY_UNREAD_IDS = 'notif_unread_ids_v1';
function loadLastUnreadIdSet(){
  try {
    const raw = localStorage.getItem(LS_KEY_UNREAD_IDS);
    if (!raw) return new Set();
    const arr = JSON.parse(raw);
    return new Set(Array.isArray(arr) ? arr : []);
  } catch(_){ return new Set(); }
}
function saveLastUnreadIdSet(set){
  try {
    const arr = Array.from(set).slice(0, 200);
    localStorage.setItem(LS_KEY_UNREAD_IDS, JSON.stringify(arr));
  } catch(_){}
}

/* ============================== UI / Core ============================== */
$(function(){
  const SERVER = '<?php echo SERVERURL; ?>';
  const EP     = SERVER+'ajax/ajaxNotificaciones.php';

  // Fallback si no está Bootstrap JS
  const hasBootstrap = !!($.fn && $.fn.dropdown);
  if(!hasBootstrap){
    $('#bell-notify .btn-notify').on('click', function(e){
      e.preventDefault();
      $('#bell-notify').toggleClass('open');
      primeAudio(); // también prima al abrir el panel por 1ª vez
    });
    $(document).on('click', function(e){
      if(!$(e.target).closest('#bell-notify').length){
        $('#bell-notify').removeClass('open');
      }
    });
  }

  
  // No cerrar el dropdown al interactuar dentro
  $('#bell-notify .dropdown-menu').on('click wheel touchstart', function(e){ e.stopPropagation(); });

  // Cache de elementos
  const $badge   = $('#notif-badge');
  const $listNew = $('#notif-list-new');
  const $listOld = $('#notif-list-earlier');
  const $newSec  = $('#np-section-new');
  const $oldSec  = $('#np-section-earlier');
  const $empty   = $('#np-empty');

  let TAB = 'all'; // 'all' | 'unread'
  let CACHE = { unread:[], read:[] };

  // Tabs
  $('#notif-tab-all').on('click', function(e){ e.stopPropagation(); setTab('all'); render(); });
  $('#notif-tab-unread').on('click', function(e){ e.stopPropagation(); setTab('unread'); render(); });
  function setTab(t){
    TAB = t;
    $('#notif-tab-all').toggleClass('np-tab-active', t==='all');
    $('#notif-tab-unread').toggleClass('np-tab-active', t==='unread');
  }

    // Mantener abierto y capturar scroll interno
  const $dropdown = $('#bell-notify .dropdown-menu');
  const $scroll = $('.np-scroll');

  $dropdown.on('click wheel touchstart', function(e){
    e.stopPropagation();              // no cierres el dropdown
  });

  // Captura la rueda para que no scrollee el body
  $scroll.on('wheel', function(e){
    e.preventDefault();
    this.scrollTop += e.originalEvent.deltaY;
  });

  // En móviles, evita “arrastrar” la página cuando empiezas dentro del panel
  let startY = 0;
  $scroll.on('touchstart', function(e){ startY = e.originalEvent.touches[0].clientY; });
  $scroll.on('touchmove', function(e){
    const curY = e.originalEvent.touches[0].clientY;
    const goingUp = curY > startY;
    const goingDown = curY < startY;
    const atTop = this.scrollTop === 0;
    const atBottom = this.scrollTop + this.clientHeight >= this.scrollHeight - 1;

    // Solo prevenimos si estamos en un borde para no “arrastrar” el body
    if ((goingUp && atTop) || (goingDown && atBottom)) {
      e.preventDefault();
    }
  });

  // Marcar todas
  $('#notif-mark-all').on('click', function(e){
    e.stopPropagation();
    $.post(EP, {action:'marcar_todas'}, null, 'json').done(r=>{ if(r && r.ok) loadNotifs(); });
  });

  // Refrescar al abrir el icono
  $('#bell-notify > a.btn-notify').on('click', function(){ setTimeout(loadNotifs, 100); });

  // Poll
  loadNotifs();
  setInterval(loadNotifs, 15000);

  // ============================== Core ==============================
  function loadNotifs(){
    $.ajax({url:EP, type:'POST', data:{action:'listar', limit:10}, dataType:'json'})
      .done(r=>{
        if(!r || !r.ok){ paintEmpty(); return; }

        // Partimos no leídas / leídas
        const unread = [], read = [];
        (r.data||[]).forEach(n => (n.leido_en ? read : unread).push(n));

        // Orden por llegada (desc)
        const byDateDesc = (a,b)=> new Date(b.creado_en.replace(' ','T')) - new Date(a.creado_en.replace(' ','T'));
        unread.sort(byDateDesc);
        read.sort(byDateDesc);

        /* ===== Detectar nuevas NO LEÍDAS incluso tras recarga ===== */
        const lastSet = loadLastUnreadIdSet();                // lo último guardado (antes de este load)
        const currentSet = new Set(unread.map(n => String(n.id)));
        let newUnreadCount = 0;
        currentSet.forEach(id => { if (!lastSet.has(id)) newUnreadCount++; });

        // Guardar para la próxima comparación (entre recargas también)
        saveLastUnreadIdSet(currentSet);

        // Dentro de la sesión (SEEN_IDS) para evitar duplicar sonidos en el mismo ciclo
        let sessionNew = 0;
        currentSet.forEach(id => { if (!SEEN_IDS.has(id)) sessionNew++; });
        SEEN_IDS = new Set([...currentSet, ...Array.from(SEEN_IDS)].slice(0, 300));

        // Disparo de sonido:
        // - Si es la primera carga tras recarga y hay nuevas vs localStorage => intentar sonar o mostrar nudge
        // - En cargas siguientes, si hay nuevas vs sesión => sonar/nudge
        const shouldSound = (!FIRST_FILL_DONE && newUnreadCount > 0) || (FIRST_FILL_DONE && sessionNew > 0);
        if (shouldSound) {
          playNotifSoundOrNudge(newUnreadCount || sessionNew);
          const last = unread[0];
          if (last) showDesktopNotification(last.titulo || "Nueva notificación",
                                            last.mensaje || "", last.url || "");
        }
        FIRST_FILL_DONE = true;
        /* ===== FIN detección ===== */

        // Guardamos y pintamos
        CACHE = { unread, read };
        $badge.text(unread.length);
        $badge.toggle(unread.length>0);
        render();
      })
      .fail(xhr=>{
        console.error('notifs FAIL', xhr.status, xhr.responseText);
        paintEmpty();
      });
  }

  function paintEmpty(){
    $listNew.empty(); $listOld.empty();
    $newSec.hide(); $oldSec.hide(); $empty.show();
    $badge.hide();
  }

  function render(){
    let src = (TAB==='unread') ? CACHE.unread.slice() : CACHE.unread.concat(CACHE.read);
    src.sort((a,b)=> new Date(b.creado_en.replace(' ','T')) - new Date(a.creado_en.replace(' ','T')));

    const news = src.filter(n => !n.leido_en);
    const old  = src.filter(n =>  n.leido_en);

    drawList($listNew, news);
    drawList($listOld, old);

    $newSec.toggle(news.length>0);
    $oldSec.toggle(old.length>0);
    $empty.toggle(src.length===0);
  }

  function drawList($ul, arr){
    $ul.empty();
    arr.forEach(n=>{
      const $li = $(itemTemplate(n));
      $li.on('click', function(e){
        e.preventDefault();
        $.post(EP, {action:'marcar_leida', id:n.id}, null, 'json')
          .always(()=>{
            if(n.url && n.url.trim()){
              const go = /^https?:/i.test(n.url) ? n.url : (SERVER + n.url.replace(/^\/+/, ''));
              window.location = go;
            }else{
              loadNotifs();
            }
          });
      });
      $ul.append($li);
    });
  }

  function itemTemplate(n){
    const d = String(n.creado_en||'').replace(' ','T');
    const when = relTime(d);
    const unread = !n.leido_en;
    const cls = 'np-item' + (unread ? ' unread' : '');
    const msg  = escapeHtml(n.mensaje||'');
    const tit  = escapeHtml(n.titulo || 'Notificación');
    return `
      <li class="${cls}" data-id="${n.id}">
        <div class="np-avatar"><i class="zmdi zmdi-notifications"></i></div>
        <div class="np-content">
          <p class="np-title">${tit}</p>
          <p class="np-msg">${msg}</p>
          <div class="np-meta">${when}</div>
        </div>
        ${unread ? '<span class="np-dot-right"></span>' : ''}
      </li>
    `;
  }

  function relTime(dateStr){
    if(!dateStr) return '';
    const d = new Date(dateStr);
    const sec = Math.max(1, Math.floor((Date.now() - (d.getTime()||0))/1000));
    if(sec < 60) return `${sec}s`;
    if(sec < 3600) return `${Math.floor(sec/60)}m`;
    if(sec < 86400) return `${Math.floor(sec/3600)}h`;
    return `${Math.floor(sec/86400)}d`;
  }
  function escapeHtml(s){
    return (s||'').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
  }
});
</script>

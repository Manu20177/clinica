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
            <div class="np-h1">Notifications</div>
            <div class="np-menu">
              <button id="notif-tab-all" class="np-tab np-tab-active" type="button">All</button>
              <button id="notif-tab-unread" class="np-tab" type="button">Unread</button>
            </div>
          </div>
        </li>

        <!-- Lista -->
        <li class="np-scroll">
          <div id="notif-container" class="np-body">
            <div id="np-section-new" class="np-section" style="display:none;">
              <div class="np-section-title">New</div>
              <ul id="notif-list-new" class="np-list"></ul>
            </div>

            <div id="np-section-earlier" class="np-section" style="display:none;">
              <div class="np-section-title">Earlier
                <a href="<?php echo SERVERURL; ?>notificaciones/" class="np-see-all-inline">See all</a>
              </div>
              <ul id="notif-list-earlier" class="np-list"></ul>
            </div>

            <div id="np-empty" class="np-empty" style="display:none;">No notifications</div>
          </div>
        </li>

        <!-- Footer -->
        <li class="np-footer">
          <button id="notif-mark-all" class="np-mark-all" type="button" title="Mark all as read">Mark all</button>
          <a href="<?php echo SERVERURL; ?>notificaciones/" class="np-see-all">See all</a>
        </li>
      </ul>
    </li>
  </ul>
</nav>


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
  position:absolute; top:-4px; right:-6px;
  background:#1b74e4; color:#fff; min-width:18px; height:18px;
  line-height:18px; border-radius:12px; font-size:11px; padding:0 5px;
}

/* Header */
.np-header{ padding:14px 14px 10px; border-bottom:1px solid #eef1f4; background:#fff; }
.np-title-row{ display:flex; align-items:center; gap:10px; }
.np-h1{ font-size:20px; font-weight:700; color:#111; }
.np-menu{ margin-left:auto; display:flex; gap:8px; }
.np-tab{ border:1px solid #e5e7eb; background:#fff; border-radius:16px; padding:6px 12px; font-size:12px; cursor:pointer; }
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
.np-mark-all{ border:1px solid #e5e7eb; background:#fff; padding:6px 12px; border-radius:6px; font-size:12px; cursor:pointer; }
.np-see-all{ margin-left:auto; font-weight:600; }

/* Scrollbar WebKit (opcional) */
.np-scroll::-webkit-scrollbar{ width:8px; }
.np-scroll::-webkit-scrollbar-thumb{ background:#c9d4e3; border-radius:4px; }
.np-scroll::-webkit-scrollbar-track{ background:transparent; }

</style>
<script>
$(function(){
  const SERVER = '<?php echo SERVERURL; ?>';
  const EP     = SERVER+'ajax/ajaxNotificaciones.php';

  // Si no está Bootstrap JS, manejamos el dropdown a mano
  const hasBootstrap = !!($.fn && $.fn.dropdown);
  if(!hasBootstrap){
    $('#bell-notify .btn-notify').on('click', function(e){
      e.preventDefault();
      $('#bell-notify').toggleClass('open');
    });
    $(document).on('click', function(e){
      if(!$(e.target).closest('#bell-notify').length){
        $('#bell-notify').removeClass('open');
      }
    });
  }

  // Mantener ABIERTO el dropdown aunque haya clicks/scroll internos
  $('#bell-notify .dropdown-menu').on('click wheel touchstart', function(e){ e.stopPropagation(); });
  $('#notif-tab-all, #notif-tab-unread, #notif-mark-all').on('click', function(e){ e.stopPropagation(); });

  const $badge   = $('#notif-badge');
  const $newSec  = $('#np-section-new');
  const $oldSec  = $('#np-section-earlier');
  const $listNew = $('#notif-list-new');
  const $listOld = $('#notif-list-earlier');
  const $empty   = $('#np-empty');

  let TAB = 'all'; // 'all' | 'unread'
  let CACHE = { unread:[], read:[] };

  // Tabs
  $('#notif-tab-all').on('click', ()=>{ setTab('all'); render(); });
  $('#notif-tab-unread').on('click', ()=>{ setTab('unread'); render(); });
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

  // Refrescar al abrir
  $('#bell-notify > a.btn-notify').on('click', function(){ setTimeout(loadNotifs, 100); });

  // Marcar todas
  $('#notif-mark-all').on('click', function(){
    $.post(EP, {action:'marcar_todas'}, null, 'json').done(r=>{ if(r && r.ok) loadNotifs(); });
  });

  // Poll
  loadNotifs();
  setInterval(loadNotifs, 15000);

  // === Core ===
  function loadNotifs(){
    $.ajax({url:EP, type:'POST', data:{action:'listar', limit:10}, dataType:'json'})
      .done(r=>{
        if(!r || !r.ok){ paintEmpty(); return; }

        // Normalizamos: leídas vs no leídas por leido_en
        const unread = [], read = [];
        (r.data||[]).forEach(n => (n.leido_en ? read : unread).push(n));

        // Orden por llegada (desc) en ambos buckets
        const byDateDesc = (a,b)=> new Date(b.creado_en.replace(' ','T')) - new Date(a.creado_en.replace(' ','T'));
        unread.sort(byDateDesc);
        read.sort(byDateDesc);

        CACHE = { unread, read };

        // Badge = no leídas
        $badge.text(unread.length);
        $badge.toggle(unread.length>0);

        render();
      })
      .fail(xhr=>{ console.error('notifs FAIL', xhr.status, xhr.responseText); paintEmpty(); });
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

    drawList($('#notif-list-new'), news);
    drawList($('#notif-list-earlier'), old);

    $('#np-section-new').toggle(news.length>0);
    $('#np-section-earlier').toggle(old.length>0);
    $('#np-empty').toggle(src.length===0);
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
              const go = n.url.match(/^https?:/i) ? n.url : (SERVER + n.url.replace(/^\/+/, ''));
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
    const when = relTime(n.creado_en);
    const unread = !n.leido_en;
    const cls = 'np-item' + (unread ? ' unread' : '');
    const msg  = escapeHtml(n.mensaje||'');
    const tit  = escapeHtml(n.titulo||'Notification');
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
    const d = new Date(String(dateStr).replace(' ', 'T'));
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

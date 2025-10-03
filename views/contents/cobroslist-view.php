<?php if($_SESSION['userType']=="Secretaria"): ?>
<div class="container-fluid">
  <div class="page-header">
    <h1 class="text-titles"><i class="zmdi zmdi-money zmdi-hc-fw"></i> Cobros</h1>
  </div>
  <p class="lead">
    En esta sección puede ver el listado de cobros (comprobantes) registrados. Puede imprimir el comprobante o anularlo cuando corresponda.
  </p>
</div>

<div class="container-fluid">
  <ul class="breadcrumb breadcrumb-tabs">
    <li>
      <a href="<?php echo SERVERURL; ?>cobros/" class="btn btn-info">
        <i class="zmdi zmdi-plus"></i> Nuevo
      </a>
    </li>
    <li class="active">
      <a href="<?php echo SERVERURL; ?>cobroslist/" class="btn btn-success">
        <i class="zmdi zmdi-format-list-bulleted"></i> Lista
      </a>
    </li>
  </ul>
</div>

<?php
  require_once "./controllers/pagosController.php";
  $pagosCtrl = new pagosController();

  // Si viniera un POST clásico para anular (no AJAX), puedes manejarlo aquí:
  if(isset($_POST['anular_id']) && isset($_POST['anular_razon'])){
    echo $pagosCtrl->anular_pago_controller($_POST['anular_id'], $_POST['anular_razon']);
  }
?>

<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12">
      <div class="panel panel-success">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="zmdi zmdi-format-list-bulleted"></i> Lista de Cobros</h3>
        </div>
        <div class="panel-body">
          <div class="table-responsive">
            <?php
              // Debe devolver una tabla <table id="tabla-global">...</table>
              // con columnas: #, Factura, Fecha, Paciente, Sucursal, Método, Subtotal, Impuesto, Total, Estado, Acciones
              echo $pagosCtrl->listado_pagos_controller();
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Anulación vía AJAX
(function(){
  const SERVER = '<?php echo SERVERURL; ?>';
  $(document).on('click', '.btn-anular-pago', function(e){
    e.preventDefault();
    const id = $(this).data('id');
    if(!id) return;

    if(!confirm('¿Seguro desea ANULAR el comprobante '+id+'?')) return;
    const razon = prompt('Ingrese el motivo de la anulación:','');
    if(razon===null) return; // canceló
    if(!razon || !razon.trim()){
      alert('Debe indicar un motivo.');
      return;
    }
    const btn = $(this).prop('disabled', true);

    $.post(SERVER+'ajax/ajaxPagos.php', { action:'anular_pago', id:id, razon:razon }, null, 'json')
      .done(function(r){
        if(r && r.ok){
          alert('Comprobante anulado.');
          // Refresca la fila o recarga:
          location.reload();
        }else{
          alert((r && r.error) ? r.error : 'No se pudo anular');
          btn.prop('disabled', false);
        }
      })
      .fail(function(xhr){
        alert('Error de comunicación ('+xhr.status+')');
        btn.prop('disabled', false);
      });
  });
})();
</script>

<?php 
  else:
    $logout2 = new loginController();
    echo $logout2->login_session_force_destroy_controller(); 
  endif;
?>

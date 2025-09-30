<?php
if($actionsRequired){
  require_once "../core/mainModel.php";
}else{
  require_once "./core/mainModel.php";
}

class pagosModel extends mainModel {



  public function buscar_pacientes_model($q){
        $pdo = self::connect();
        $sql = "SELECT id_paciente, cedula, nombres, apellidos
                  FROM pacientes
                 WHERE cedula    LIKE :q
                    OR nombres   LIKE :q
                    OR apellidos LIKE :q
                 ORDER BY nombres ASC
                 LIMIT 12";
        $st = $pdo->prepare($sql);
        $like = "%{$q}%";
        $st->bindParam(':q', $like, PDO::PARAM_STR);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
  /*************************************************
   *  Insert cabecera de pago
   *************************************************/
  /* ========== Transacción: insertar pago + items ========== */
  public function crear_pago_tx_model($pago, $items){
    $db = self::connect();
    try{
      $db->beginTransaction();

      $sql = "INSERT INTO pagos (
        id_factura,id_cita,paciente_id,sucursal_id,fecha_pago,metodo,referencia,
        subtotal,impuesto,total,moneda,estado,notas,creado_por,creado_en
      ) VALUES (
        :id_factura,:id_cita,:paciente_id,:sucursal_id,NOW(),:metodo,:referencia,
        :subtotal,:impuesto,:total,:moneda,:estado,:notas,:creado_por,NOW()
      )";
      $st = $db->prepare($sql);
      $st->execute([
        ':id_factura'  => $pago['id_factura'],   // string ok
        ':id_cita'     => $pago['id_cita'],      // string o null
        ':paciente_id' => $pago['paciente_id'],  // string
        ':sucursal_id' => $pago['sucursal_id'],  // string
        ':metodo'      => $pago['metodo'],
        ':referencia'  => $pago['referencia'],
        ':subtotal'    => $pago['subtotal'],
        ':impuesto'    => $pago['impuesto'],
        ':total'       => $pago['total'],
        ':moneda'      => $pago['moneda'],
        ':estado'      => $pago['estado'],
        ':notas'       => $pago['notas'],
        ':creado_por'  => $pago['creado_por']
      ]);

      $sti = $db->prepare("INSERT INTO pago_items
        (factura_id, descripcion, cantidad, precio_unit, importe)
        VALUES (:factura_id,:descripcion,:cantidad,:precio_unit,:importe)");
      foreach($items as $it){
        $sti->execute([
          ':factura_id'  => $it['factura_id'],
          ':descripcion' => $it['descripcion'],
          ':cantidad'    => $it['cantidad'],
          ':precio_unit' => $it['precio_unit'],
          ':importe'     => $it['importe']
        ]);
      }

      $db->commit();
      return true;
    }catch(Exception $e){
      if($db->inTransaction()) $db->rollBack();
      return false;
    }
  }

  /*************************************************
   *  Insert de 1 ítem
   *************************************************/
  public function crear_item_model($factura_id, $item){
    $sql = "INSERT INTO pago_items
              (factura_id, descripcion, cantidad, precio_unit, importe)
            VALUES
              (:factura_id, :descripcion, :cantidad, :precio_unit, :importe)";
    $stm = self::connect()->prepare($sql);
    $stm->bindValue(':factura_id', $factura_id, PDO::PARAM_STR);
    $stm->bindValue(':descripcion', $item['descripcion'], PDO::PARAM_STR);
    $stm->bindValue(':cantidad', $item['cantidad']);
    $stm->bindValue(':precio_unit', $item['precio_unit']);
    $stm->bindValue(':importe', $item['importe']);
    $stm->execute();
    return $stm;
  }

  /*************************************************
   *  (Opcional) Bulk insert de items
   *************************************************/
  public function crear_items_bulk_model($factura_id, array $items){
    $pdo = self::connect();
    $sql = "INSERT INTO pago_items
              (factura_id, descripcion, cantidad, precio_unit, importe)
            VALUES
              (:factura_id, :descripcion, :cantidad, :precio_unit, :importe)";
    $stm = $pdo->prepare($sql);
    foreach($items as $it){
      $stm->execute([
        ':factura_id'  => $factura_id,
        ':descripcion' => $it['descripcion'],
        ':cantidad'    => $it['cantidad'],
        ':precio_unit' => $it['precio_unit'],
        ':importe'     => $it['importe'],
      ]);
    }
    return true;
  }

  /*************************************************
   *  Anular pago
   *************************************************/
  public function anular_pago_model($id_factura, $usuario, $razon){
    $sql = "UPDATE pagos
               SET estado='ANULADO',
                   anulado_por=:usr,
                   anulado_en=NOW(),
                   anulado_razon=:rz
             WHERE id_factura=:id
             LIMIT 1";
    $stm = self::connect()->prepare($sql);
    $stm->execute([
      ':usr'=>$usuario,
      ':rz'=>$razon,
      ':id'=>$id_factura
    ]);
    return $stm;
  }

  /*************************************************
   *  Detalle de pago (cabecera + items)
   *************************************************/
  public function detalle_pago_model($id_factura){
    $pdo = self::connect();

    $h = $pdo->prepare("SELECT * FROM pagos WHERE id_factura=:id LIMIT 1");
    $h->execute([':id'=>$id_factura]);
    $cab = $h->fetch(PDO::FETCH_ASSOC);

    if(!$cab) return [null, []];

    $d = $pdo->prepare("SELECT id, descripcion, cantidad, precio_unit, importe
                          FROM pago_items
                         WHERE factura_id=:id
                      ORDER BY id ASC");
    $d->execute([':id'=>$id_factura]);
    $items = $d->fetchAll(PDO::FETCH_ASSOC);

    return [$cab, $items];
  }

  /*************************************************
   *  Listado de pagos (filtros opcionales)
   *************************************************/
  public function listar_pagos_model($desde=null, $hasta=null, $sucursal_id=null, $estado=null, $paciente_id=null){
    $pdo = self::connect();
    $w = [];
    $p = [];
    if($desde){ $w[]="p.fecha_pago >= :d"; $p[':d']=$desde; }
    if($hasta){ $w[]="p.fecha_pago <= :h"; $p[':h']=$hasta; }
    if($sucursal_id){ $w[]="p.sucursal_id = :s"; $p[':s']=$sucursal_id; }
    if($estado){ $w[]="p.estado = :e"; $p[':e']=$estado; }
    if($paciente_id){ $w[]="p.paciente_id = :pa"; $p[':pa']=$paciente_id; }
    $where = $w ? ('WHERE '.implode(' AND ',$w)) : '';

    $sql = "SELECT p.*,
                   CONCAT(pa.nombres,' ',pa.apellidos) AS paciente
              FROM pagos p
         LEFT JOIN pacientes pa ON pa.id_paciente = p.paciente_id
              $where
          ORDER BY p.fecha_pago DESC, p.id_factura DESC";
    $stm = $pdo->prepare($sql);
    $stm->execute($p);
    return $stm->fetchAll(PDO::FETCH_ASSOC);
  }


  public function citas_por_paciente_model($paciente_id, $sucursal_id=null, $q=''){
      $pdo = self::connect();

      // Base: citas del paciente (pendientes/confirmadas/atendidas, excluye canceladas si quieres)
      $sql = "
          SELECT c.id, c.fecha_inicio, c.fecha_fin, c.estado, e.nombre AS especialidad, m.Nombres AS mnombres, m.Apellidos AS mapellidos FROM citas c LEFT JOIN medico_especialidad me ON me.id_especialidad = c.id_especialidad_med LEFT JOIN especialidades e on e.id=me.especialidad_id LEFT JOIN usuarios m ON m.Codigo = me.medico_codigo
          WHERE c.paciente_id = :paciente_id AND (c.estado IN ('RESERVADO','CONFIRMADA','ATENDIDA'))
      ";

      $params = [':paciente_id' => $paciente_id];

      if(!empty($sucursal_id)){
          $sql .= " AND c.sucursal_id = :sucursal_id";
          $params[':sucursal_id'] = $sucursal_id;
      }

      // Filtro textual opcional sobre especialidad / médico / fecha
      if($q !== ''){
          $sql .= " AND (
              e.nombre LIKE :q
              OR m.Nombres LIKE :q
              OR m.Apellidos LIKE :q
              OR DATE(c.fecha_inicio) LIKE :q
          )";
          $params[':q'] = '%'.$q.'%';
      }

      $sql .= " ORDER BY c.fecha_inicio ASC LIMIT 50";

      $st = $pdo->prepare($sql);
      foreach($params as $k=>$v){
          $st->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
      }
      $st->execute();
      return $st->fetchAll(PDO::FETCH_ASSOC);
  }


}

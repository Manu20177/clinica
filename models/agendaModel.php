<?php
if($actionsRequired){
    require_once "../core/mainModel.php";
}else{
    require_once "./core/mainModel.php";
}

class agendaModel extends mainModel {

    /* --- Buscar pacientes por cédula/nombres/apellidos --- */
    protected function buscar_pacientes_model($q){
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

    /* --- Cargar especialidades activas --- */
    protected function load_especialidades_model($sucursal_id=null){
        $pdo = self::connect();
        // Si tienes tabla sucursal_especialidad, filtra aquí
        $sql = "SELECT id, nombre
                  FROM especialidades
                 WHERE estado = 'Activa'
                 ORDER BY nombre ASC";
        $st = $pdo->query($sql);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /* --- Cargar médicos por especialidad --- */
    protected function load_medicos_model($especialidad_id, $sucursal_id=null){
        $pdo = self::connect();
        $sql = "SELECT u.Codigo AS medico_codigo,
                       u.nombre_completo
                  FROM medico_especialidad me
                  INNER JOIN usuarios u ON u.Codigo = me.medico_codigo
                 WHERE me.especialidad_id = :esp
                   AND u.rol = 'MEDICO'
                   AND u.activo = 1
                 ORDER BY u.nombre_completo ASC";
        $st = $pdo->prepare($sql);
        $st->bindValue(':esp', $especialidad_id, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /* --- Verificar solape de citas --- */
    protected function hay_solape_model($medico_codigo, $start, $end){
        $pdo = self::connect();
        $sql = "SELECT id
                  FROM citas
                 WHERE medico_codigo = :medico
                   AND estado IN ('PENDIENTE','CONFIRMADA')
                   AND NOT (fecha_fin <= :start OR fecha_inicio >= :end)
                 LIMIT 1";
        $st = $pdo->prepare($sql);
        $st->bindValue(':medico', $medico_codigo, PDO::PARAM_STR);
        $st->bindValue(':start',  $start, PDO::PARAM_STR);
        $st->bindValue(':end',    $end,   PDO::PARAM_STR);
        $st->execute();
        return $st->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    /* --- Listar citas de un médico en rango (para FullCalendar) --- */
    protected function listar_citas_model($medico_codigo, $start, $end){
        $pdo = self::connect();
        $sql = "SELECT id, fecha_inicio, fecha_fin
                  FROM citas
                 WHERE medico_codigo = :medico
                   AND estado IN ('PENDIENTE','CONFIRMADA')
                   AND NOT (fecha_fin <= :start OR fecha_inicio >= :end)
                 ORDER BY fecha_inicio ASC";
        $st = $pdo->prepare($sql);
        $st->bindValue(':medico', $medico_codigo, PDO::PARAM_STR);
        $st->bindValue(':start',  $start, PDO::PARAM_STR);
        $st->bindValue(':end',    $end,   PDO::PARAM_STR);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /* --- Crear cita --- */
    protected function crear_cita_model($data){
        $pdo = self::connect();
        $sql = "INSERT INTO citas
                (paciente_id, sucursal_id, especialidad_id, medico_codigo,
                 fecha_inicio, fecha_fin, estado, origen, derivacion_id,
                 notas, creada_por, creado_en)
                VALUES
                (:paciente_id, :sucursal_id, :especialidad_id, :medico_codigo,
                 :fecha_inicio, :fecha_fin, :estado, :origen, :derivacion_id,
                 :notas, :creada_por, NOW())";
        $st = $pdo->prepare($sql);
        $st->bindValue(':paciente_id',     $data['paciente_id'],     PDO::PARAM_STR);
        $st->bindValue(':sucursal_id',     $data['sucursal_id'],     PDO::PARAM_STR);
        $st->bindValue(':especialidad_id', $data['especialidad_id'], PDO::PARAM_INT);
        $st->bindValue(':medico_codigo',   $data['medico_codigo'],   PDO::PARAM_STR);
        $st->bindValue(':fecha_inicio',    $data['fecha_inicio'],    PDO::PARAM_STR);
        $st->bindValue(':fecha_fin',       $data['fecha_fin'],       PDO::PARAM_STR);
        $st->bindValue(':estado',          $data['estado'],          PDO::PARAM_STR);
        $st->bindValue(':origen',          $data['origen'],          PDO::PARAM_STR);
        if($data['derivacion_id']===null){
            $st->bindValue(':derivacion_id', null, PDO::PARAM_NULL);
        }else{
            $st->bindValue(':derivacion_id', $data['derivacion_id'], PDO::PARAM_INT);
        }
        $st->bindValue(':notas',           $data['notas'],           PDO::PARAM_STR);
        $st->bindValue(':creada_por',      $data['creada_por'],      PDO::PARAM_STR);
        return $st->execute();
    }

    /* --- Marcar derivación como AGENDADA --- */
    protected function marcar_derivacion_agendada_model($derivacion_id){
        $pdo = self::connect();
        $sql = "UPDATE derivaciones SET estado='AGENDADA' WHERE id=:id";
        $st = $pdo->prepare($sql);
        $st->bindValue(':id', $derivacion_id, PDO::PARAM_INT);
        return $st->execute();
    }
}

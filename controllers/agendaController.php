<?php
if($actionsRequired){
    require_once "../models/agendaModel.php";
}else{
    require_once "./models/agendaModel.php";
}

class agendaController extends agendaModel {

    /* Buscar pacientes (devuelve <li> para algún dropdown legacy) */
    public function buscar_pacientes_controller($q){
        if(mb_strlen($q) < 2) return '';
        $rows = self::buscar_pacientes_model($q);
        if(!$rows){ return '<li class="list-group-item" data-id="">Sin resultados</li>'; }

        $out = '';
        foreach($rows as $r){
            $ced = $r['cedula'] ?? '';
            $nom = trim(($r['nombres'] ?? '').' '.($r['apellidos'] ?? ''));
            $txt = trim(($ced ? $ced.' - ' : '').$nom);
            $out .= '<li class="list-group-item" data-id="'.self::clean_string($r['id_paciente']).'">'.
                        htmlspecialchars($txt, ENT_QUOTES, 'UTF-8').
                    '</li>';
        }
        return $out;
    }

    /* Buscar pacientes (JSON para Select2) */
    public function buscar_pacientes_json_controller($q){
        if(mb_strlen($q) < 2){
            return json_encode([]);
        }
        $rows = self::buscar_pacientes_model($q);
        if(!$rows){ return json_encode([]); }

        $out = [];
        foreach($rows as $r){
            $id  = $r['id_paciente'];
            $ced = $r['cedula'] ?? '';
            $nom = trim(($r['nombres'] ?? '').' '.($r['apellidos'] ?? ''));
            $text = trim(($ced ? $ced.' - ' : '').$nom);
            $out[] = ["id"=>$id, "text"=>$text];
        }
        return json_encode($out, JSON_UNESCAPED_UNICODE);
    }

    /* Crear cita desde el formulario */
      /* Guardar cita (ajustado a tu tabla) */
    public function add_cita_controller(){
        $paciente_id         = self::clean_string($_POST['paciente_id'] ?? '');
        $estadoc         = self::clean_string($_POST['estadoc'] ?? '');
        $sucursal_id         = self::clean_string($_POST['sucursal_id'] ?? '');
        $id_especialidad_med = intval($_POST['id_especialidad_med'] ?? 0);
        $fecha               = self::clean_string($_POST['fecha'] ?? '');
        $hora_inicio         = self::clean_string($_POST['hora_inicio'] ?? '');
        $hora_fin            = self::clean_string($_POST['hora_fin'] ?? '');
        $creada_por          = $_SESSION['userKey'] ?? 'SYSTEM';

        if($paciente_id==='' || $sucursal_id==='' || $id_especialidad_med<=0 ||
        $fecha==='' || $hora_inicio==='' || $hora_fin===''){
        $dataAlert = [
            "title"=>"Datos incompletos",
            "text"=>"Complete paciente, sucursal, médico y horario",
            "type"=>"error"
        ];
        return self::sweet_alert_single($dataAlert);
        }

        // Timestamps
        $fecha_inicio = $fecha." ".$hora_inicio.":00";
        $fecha_fin    = $fecha." ".$hora_fin.":00";

        // No permitir fin <= inicio
        if(strtotime($fecha_fin) <= strtotime($fecha_inicio)){
        $dataAlert = [
            "title"=>"Rango inválido",
            "text"=>"La hora fin debe ser mayor a la hora inicio",
            "type"=>"error"
        ];
        return self::sweet_alert_single($dataAlert);
        }

        // Solape por id_especialidad_med
        if(self::hay_solape_model($id_especialidad_med, $fecha_inicio, $fecha_fin)){
        $dataAlert = [
            "title"=>"Conflicto de horario",
            "text"=>"Ya existe una cita en ese rango",
            "type"=>"error"
        ];
        return self::sweet_alert_single($dataAlert);
        }

        $data = [
        "paciente_id"         => $paciente_id,
        "sucursal_id"         => $sucursal_id,
        "id_especialidad_med" => $id_especialidad_med,
        "fecha_inicio"        => $fecha_inicio,
        "fecha_fin"           => $fecha_fin,
        "estado"              => $estadoc,
        "creada_por"          => $creada_por
        ];

        if(self::crear_cita_model($data)){
        $dataAlert = [
            "title"=>"Cita registrada",
            "text"=>"La cita se registró con éxito",
            "type"=>"success"
        ];
        return self::sweet_alert_single($dataAlert);
        }else{
        $dataAlert = [
            "title"=>"Error inesperado",
            "text"=>"No se pudo registrar la cita",
            "type"=>"error"
        ];
        return self::sweet_alert_single($dataAlert);
        }
    }

    public function add_lista_espera_controller(){
        $id_cita         = self::clean_string($_POST['id_cita'] ?? '');
        $sql = "UPDATE citas 
                SET estado = 'LISTA_ESPERA'
                    
                WHERE id = :id
                LIMIT 1";
        $st = $this->connect()->prepare($sql);
        $st->bindValue(':id', $id_cita, PDO::PARAM_INT);
       
        return $st->execute();
       

    }

    public function confirmar_desde_espera_controller(int $id_cita){

        // 1) Traer cita
        $cita = self::get_cita_by_id($id_cita);
        if(!$cita){ return ['ok'=>false,'error'=>'Cita no encontrada']; }

        $estado = strtoupper(trim((string)$cita['estado']));
        if($estado !== 'LISTA_ESPERA'){
        return ['ok'=>false,'error'=>'La cita no está en lista de espera'];
        }

        // 2) Validar datos base
        $hi    = $cita['fecha_inicio'] ?? null;
        $hf    = $cita['fecha_fin'] ?? null;
        $idEM  = isset($cita['id_especialidad_med']) ? (int)$cita['id_especialidad_med'] : 0;
        

        if(empty($hi) || empty($hf) || $idEM<=0){
        return ['ok'=>false,'error'=>'Faltan datos para confirmar (fecha/hora/médico)'];
        }

        // 3) Verificar conflicto con RESERVADO/CONFIRMADA en ese rango (excluyendo la propia cita)
        $hayConflicto = self::existe_conflicto_horario($idEM,$hi, $hf, $id_cita);
        if($hayConflicto){
        return ['ok'=>false,'error'=>'El horario está ocupado. No se puede confirmar.'];
        }

        // 4) Confirmar
        $ok = self::update_estado_cita($id_cita, 'CONFIRMADA');

        if(!$ok){ return ['ok'=>false,'error'=>'No se pudo actualizar el estado']; }

        return ['ok'=>true, 'msg'=>'Cita confirmada', 'id'=>$id_cita];
    }

    /* Cargar especialidades (devuelve <option>) */
    public function load_especialidades_controller($sucursal_id=null){
        $rows = self::load_especialidades_model($sucursal_id);
        if(!$rows) return '<option value="">No hay especialidades</option>';

        $out = '<option value="">Seleccione</option>';
        foreach($rows as $r){
            $out .= '<option value="'.$r['id'].'">'.htmlspecialchars($r['nombre'], ENT_QUOTES, 'UTF-8').'</option>';
        }
        return $out;
    }

    /* Cargar médicos por especialidad (devuelve <option>) */
    public function load_medicos_controller($especialidad_id, $sucursal_id=null){
        if($especialidad_id<=0) return '<option value="">Seleccione especialidad</option>';

        $rows = self::load_medicos_model($especialidad_id, $sucursal_id);
        if(!$rows) return '<option value="">No hay médicos disponibles</option>';

        $out = '<option value="">Seleccione</option>';
        foreach($rows as $r){
            $out .= '<option value="'.$r['cod_esp_med'].'">'.
                        htmlspecialchars($r['nombre_completo'], ENT_QUOTES, 'UTF-8').
                    '</option>';
        }
        return $out;
    }

    /* Check de disponibilidad (JSON) */
    public function check_disponibilidad_controller($medico, $fecha, $hi, $hf){
        if($medico==='' || $fecha==='' || $hi==='' || $hf===''){
            return json_encode(['disponible'=>false,'msg'=>'Datos incompletos']);
        }
        if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$fecha) ||
           !preg_match('/^\d{2}:\d{2}$/',$hi) ||
           !preg_match('/^\d{2}:\d{2}$/',$hf)){
            return json_encode(['disponible'=>false,'msg'=>'Formato inválido']);
        }

        $start = $fecha.' '.$hi.':00';
        $end   = $fecha.' '.$hf.':00';

        $haySolape = self::hay_solape_model($medico, $start, $end);
        return json_encode(['disponible'=> $haySolape ? false : true ]);
    }

    /* Listar citas del médico en rango (JSON FullCalendar) */
   /* Listar citas del médico en rango (JSON FullCalendar) */
    public function listar_citas_controller($id_especialidad_med, $startISO, $endISO){
        // Validaciones mínimas
        $id = (int)$id_especialidad_med;
        if($id <= 0 || $startISO==='' || $endISO===''){
            header('Content-Type: application/json; charset=utf-8');
            return json_encode([]);
        }

        // Normaliza rango recibido del calendario
        $start = date('Y-m-d H:i:s', strtotime($startISO));
        $end   = date('Y-m-d H:i:s', strtotime($endISO));

        // Consulta
        $rows = self::listar_citas_model($id, $start, $end);

        header('Content-Type: application/json; charset=utf-8');

        if(!$rows){ 
            return json_encode([]); 
        }

        $events = [];
        foreach($rows as $r){
            // Título más descriptivo (opcional)
            $doc  = trim(($r['apellidos'] ?? '').' '.($r['nombres'] ?? ''));
            $esp  = $r['especialidad'] ?? '';
            $pac  = $r['paciente_id'] ?? '';
            $title = $r['estado'];
            if($esp !== '')  { $title .= " · $esp"; }
            if($doc !== '')  { $title .= " · Dr(a). $doc"; }
            if($pac !== '')  { $title .= " · Pac: $pac"; }

            // Fechas → ISO 8601 (compatibles con FullCalendar)
            $startIso = date('c', strtotime($r['fecha_inicio']));
            $endIso   = date('c', strtotime($r['fecha_fin']));

            // Clases de estado
            $cls     = ['reservado'];
            $estado  = strtoupper($r['estado'] ?? '');
            if($estado === 'CONFIRMADA')     $cls[] = 'estado-confirmada';
            elseif($estado === 'CANCELADA')  $cls[] = 'estado-cancelada';
            else                             $cls[] = 'estado-pendiente'; // PENDIENTE/otros

            $events[] = [
                "id"           => (int)$r['id'],
                "title"        => $title,
                "start"        => $startIso,
                "end"          => $endIso,
                "classNames"   => $cls,      // v6: arreglo
                "display"      => "block",
                "extendedProps"=> [
                    "estado"              => $r['estado'] ?? '',
                    "id_especialidad_med" => (int)($r['id_especialidad_med'] ?? $id)
                ]
            ];
        }

        return json_encode($events, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }


    public function listar_citas_todas_controller($startISO, $endISO, $sucursal_id = null){
        if($startISO==='' || $endISO===''){ return json_encode([]); }

        $start = date('Y-m-d H:i:s', strtotime($startISO));
        $end   = date('Y-m-d H:i:s', strtotime($endISO));

        $rows = self::listar_citas_todas_model($start, $end, $sucursal_id);
        if(!$rows){ return json_encode([]); }

        $events = [];
        foreach($rows as $r){
            // título opcional más descriptivo
            $doc = trim(($r['apellidos'] ?? '').' '.($r['nombres'] ?? ''));
            $esp = $r['especialidad'] ?? '';
            $pac = $r['paciente_id'] ?? '';
            $title = $esp ? "Reservado · $esp" : "Reservado";
            if($doc !== '') $title .= " · Dr(a). $doc";
            if($pac !== '') $title .= " · Pac: $pac";

            // colorear según estado (opcional)
            $cls = ['reservado'];
            $estado = strtoupper($r['estado'] ?? '');
            if($estado === 'CONFIRMADA') $cls[] = 'estado-confirmada';
            elseif($estado === 'CANCELADA') $cls[] = 'estado-cancelada';
            else $cls[] = 'estado-pendiente'; // PENDIENTE u otros

            $events[] = [
                "id"         => (int)$r['id'],
                "title"      => $title,
                "start"      => date('c', strtotime($r['fecha_inicio'])),
                "end"        => date('c', strtotime($r['fecha_fin'])),
                "classNames" => $cls,
                "extendedProps" => [
                    "estado" => $r['estado'],
                    "id_especialidad_med" => (int)$r['id_especialidad_med']
                ]
            ];
        }
        return json_encode($events, JSON_UNESCAPED_UNICODE);
    }

    public function detalle_cita_controller($id_cita){
        $id = (int)$id_cita;
        if ($id <= 0) return ['ok'=>false,'error'=>'ID inválido'];

        $row = self::detalle_cita_model($id);
        if (!$row) return ['ok'=>false,'error'=>'Cita no encontrada'];

        // Normaliza fechas/horas
        $fecha = $horaI = $horaF = '';
        if (!empty($row['fecha_inicio'])) {
            $tsI = strtotime($row['fecha_inicio']);
            $fecha = date('Y-m-d', $tsI);
            $horaI = date('H:i',   $tsI);
        }
        if (!empty($row['fecha_fin'])) {
            $tsF = strtotime($row['fecha_fin']);
            $horaF = date('H:i', $tsF);
        }

        return [
            'ok'                => true,
            'id'                => (int)$row['id'],
            'estado'            => $row['estado'],
            'fecha'             => $fecha,
            'hora'              => $horaI,
            'hora_inicio'       => $horaI,
            'hora_fin'          => $horaF,
            'paciente_id'       => $row['paciente_id'],
            'paciente_cedula'   => $row['paciente_cedula'] ?? '',
            'paciente_nombre'   => $row['pnombres'] ?? '',
            'paciente_apellido' => $row['papellidos'] ?? '',
            'medico_codigo'     => $row['medico_codigo'] ?? '',
            'medico_nombre'     => $row['mnombres'] ?? '',
            'medico_apellido'   => $row['mapellidos'] ?? '',
            'especialidad'      => $row['especialidad'] ?? '',
            'sucursal'          => $row['sucursal'] ?? '',
            'creada_por'        => $row['creada_por'] ?? '',
            'creado_en'         => $row['creado_en'] ?? '',
        ];
    }

   public function cancelar_cita_controller($id, $razon){
        $pdo = $this->connect(); // adapta a tu conexión
        $usuario = isset($_SESSION['usuarioNombre']) ? $_SESSION['usuarioNombre'] : 'sistema';

        $sql = "UPDATE citas 
                SET estado = 'CANCELADA',
                    notas = TRIM(CONCAT(IFNULL(notas,''), 
                            CASE WHEN IFNULL(notas,'')='' THEN '' ELSE ' | ' END,
                            'Cancelada: ', :razon, ' (', :usuario, ' ', NOW(), ')'))
                WHERE id = :id
                LIMIT 1";
        $st = $pdo->prepare($sql);
        $st->bindValue(':id', $id, PDO::PARAM_INT);
        $st->bindValue(':razon', $razon, PDO::PARAM_STR);
        $st->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        return $st->execute();
    }





}

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
        "estado"              => "PENDIENTE",
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
    public function listar_citas_controller($medico_codigo, $startISO, $endISO){
        if($medico_codigo==='' || $startISO==='' || $endISO===''){
            return json_encode([]);
        }

        $start = date('Y-m-d H:i:s', strtotime($startISO));
        $end   = date('Y-m-d H:i:s', strtotime($endISO));

        $rows = self::listar_citas_model($medico_codigo, $start, $end);
        if(!$rows){ return json_encode([]); }

        $events = [];
        foreach($rows as $r){
            $events[] = [
                "id"         => (int)$r['id'],
                "title"      => "Reservado",
                // ← fechas en ISO 8601 (con “T”)
                "start"      => date('c', strtotime($r['fecha_inicio'])),
                "end"        => date('c', strtotime($r['fecha_fin'])),
                // ← v6 usa classNames (array)
                "classNames" => ["reservado"],
                // por si ayuda al render
                "display"    => "block"
            ];
        }
        return json_encode($events, JSON_UNESCAPED_UNICODE);
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




}

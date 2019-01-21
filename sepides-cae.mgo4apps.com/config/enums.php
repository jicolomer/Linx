<?php

return [

    // CONTROL ACCESOS
    'tipos_control_acceso' => [
        'M' => 'Máquina',
        'T' => 'Trabajador',
    ],

    // AMBITOS para DOCUMENTOS
    'doc_scopes' => [
        '' => '(no definido)',
        'EMP' => 'Empresa principal',
        'CEN' => 'Centro Trabajo',
        'CTA' => 'Contratista',
        'TRA' => 'Trabajador',
        'MAQ' => 'Maquinaria',
    ],

    // MODALIDADES PREVENTIVAS
    'modalidades_preventivas' => [
        'EMP' => 'Asumida por el empresario',
        'TRA' => 'Trabajador designado',
        'SPP' => 'Servicio de prevención propio',
        'SPM' => 'Servicio de prevención mancomunado',
        'SPA' => 'Servicio de prevención ajeno',
    ],

    // CADUCIDADES
    'tipos_caducidad' => [
        'N' => 'No caduca',
        'V' => 'Vencimiento',
        'M' => 'Mensual',
        'T' => 'Trimestral',
        'S' => 'Semestral',
        'A' => 'Anual',
    ],

    // STATUS PERMISOS
    'status_permiso' => [
        0 => 'No evaluado',
        1 => 'Aceptado',
        2 => 'Rechazado',
    ],

    // TIPOS DOCUMENTOS TRABAJADORES
    'tipos_documentos_trabajadores' => [
        'FOR' => 'Formación',
        'INF' => 'Información',
        'EPI' => 'EPIS',
        'VIS' => 'Vigilancia de la salud',
        'OTR' => 'Otros',
    ],

    // ACTIVIDADES CONSTRUCCION
    'actividades_construccion' => [
        1 => 'Excavación',
        2 => 'Movimiento de tierras',
        3 => 'Construcción',
        4 => 'Montaje y desmontaje de elementos prefabricados',
        6 => 'Acondicionamientos o instalaciones',
        7 => 'Transformación',
        8 => 'Rehabilitación',
        9 => 'Reparación',
        10 => 'Desmantelamiento',
        11 => 'Derribo',
        12 => 'Mantenimiento',
        13 => 'Conservación y trabajos de pintura y limpieza',
        14 => 'Saneamiento',
    ],

];

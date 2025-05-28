<?php

namespace App\Validation;

use Modalidad;
use DateTime;

class EventRules
{
    public function validarDuracion(string $str, string &$error = null, array $data = []): bool
    {
        if (isset($data['modality']) && ($data['modality'] == Modalidad::Virtual || $data['modality'] == Modalidad::Hibrida)) {
            if (empty($str)) {
                $error = 'La duración del evento es requerida para modalidades Virtual o Hibrida.';
                return false;
            }

            if (!is_numeric($str)) {
                $error = 'La duración del evento debe ser un valor numérico.';
                return false;
            }
        }

        return true;
    }
    public function fecha_evento(string $valor, &$error = null): bool
    {
        $fechaEvento = new DateTime($valor);
        $hoy = new DateTime();

        if ($fechaEvento <= $hoy) {
            $error = 'La fecha del evento debe ser una fecha futura';
            return false;
        }

        return true;
    }
    public function fecha_inicio_inscripcion($value, string $params, array $data, &$error = null): bool
    {
        // Obtenemos la fecha de inicio y la fecha de fin de inscripciones
        $fechaInicio = new DateTime($value);
        $fechaFin = isset($data['registrations_end_date']) ? new DateTime($data['registrations_end_date']) : null;
        $fechaEvento = isset($data['event_date']) ? new DateTime($data['event_date']) : null;
        $hoy = new DateTime();
        $hoy = $hoy->format('Y-m-d');
        $fechaInicio= $fechaInicio->format('Y-m-d');
        $fechaFin= $fechaFin->format('Y-m-d');
        $fechaEvento= $fechaEvento->format('Y-m-d');
        // Validar que la fecha de inicio de inscripción no esté en el pasado
        if ($fechaInicio < $hoy) {
            $error = 'La fecha de inicio de la inscripción no puede estar en el pasado.';
            return false;
        }

        // Validar que la fecha de inicio de inscripción sea anterior a la fecha de fin de inscripción
        if ($fechaFin && $fechaInicio >= $fechaFin) {
            $error = 'La fecha de inicio de la inscripción debe ser anterior a la fecha de fin de inscripción.';
            return false;
        }

        if ($fechaEvento && $fechaInicio >= $fechaEvento) {
            $error = 'La fecha de inicio de la inscripción debe ser menor a la fecha del evento';
            return false;
        }

        return true;
    }
    public function fecha_fin_inscripcion($value, string $params, array $data, &$error = null): bool
    {
        // Obtenemos la fecha de inicio y la fecha de fin de inscripciones
        $fechaFin = new DateTime($value);
        $fechaInicio = isset($data['registrations_start_date']) ? new DateTime($data['registrations_start_date']) : null;
        $fechaEvento = isset($data['event_date']) ? new DateTime($data['event_date']) : null;
        $hoy = new DateTime();
        $hoy = $hoy->format('Y-m-d');
        $fechaInicio= $fechaInicio->format('Y-m-d');
        $fechaFin= $fechaFin->format('Y-m-d');
        $fechaEvento= $fechaEvento->format('Y-m-d');

        if ($fechaFin < $hoy) {
            $error = 'La fecha de finalización de la inscripción no puede ser menor a la fecha actual.';
            return false;
        }

        if ($fechaEvento && !($fechaFin <= $fechaEvento)) {
            $error = 'La fecha de finalización de la inscripción debe ser menor o igual a la fecha del evento';
            return false;
        }

        if ($fechaInicio && $fechaFin <= $fechaInicio) {
            $error = 'La fecha de finalización de la inscripción debe ser mayor a la fecha de inicio de la inscripción.';
            return false;
        }

        return true;
    }
}

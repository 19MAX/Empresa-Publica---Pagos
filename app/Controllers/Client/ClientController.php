<?php
namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\EventsModel;
use App\Services\ApiPrivadaService;

class ClientController extends BaseController
{
    private $apiPrivadaService;
    public function __construct()
    {
        $this->apiPrivadaService = new ApiPrivadaService();
    }

    public function index()
    {
        helper('date');

        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $eventModel = new EventsModel();
        $active_events = $eventModel->getActiveAndCurrentEvents();

        // Formatear las fechas de los eventos
        foreach ($active_events as &$event) {
            $event['formatted_event_date'] = format_event_date($event['event_date']);
        }

        $data = [
            'events' => $active_events,
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
        ];
        return view('client/home', $data);
    }

    public function authors()
    {
        helper('date');

        $flashValidation = session()->getFlashdata('flashValidation');
        $flashMessages = session()->getFlashdata('flashMessages');
        $last_data = session()->getFlashdata('last_data');
        $last_action = session()->getFlashdata('last_action');

        $eventModel = new EventsModel();
        $active_events = $eventModel->getActiveAndCurrentEvents();

        // Formatear las fechas de los eventos
        foreach ($active_events as &$event) {
            $event['formatted_event_date'] = format_event_date($event['event_date']);
        }

        $data = [
            'authors' => $eventModel->getAuthorsWithEventCount(),
            'events' => $active_events, // Agregar esta línea
            'last_action' => $last_action,
            'last_data' => $last_data,
            'validation' => $flashValidation,
            'flashMessages' => $flashMessages,
        ];
        return view('client/events/authors', $data);
    }


}
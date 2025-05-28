<?= $this->extend('layouts/admin_layout'); ?>

<?= $this->section('title') ?>
Eventos
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url("assets/css/rounded.css") ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header sty-one">
        <h1 class="text-black">Eventos</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li class="sub-bread"><i class="fa fa-angle-right"></i> Eventos</li>
            <li><i class="fa fa-angle-right"></i> Lista</li>
        </ol>
    </div>
    <div class="content">
        <div class="info-box">
            <div class="table-responsive">
                <table id="event" class="table datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Evento</th>
                            <th class="exclude-view">Descripción del evento</th>
                            <th>Fecha del evento</th>
                            <th class="exclude-view">Dirección del evento</th>
                            <th class="exclude-view">Categorías del evento</th>
                            <th>Fecha de inicio de la inscripción</th>
                            <th>Fecha de finalización de la inscripción</th>
                            <th class="exclude-column">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $key => $event): ?>
                            <tr>
                                <td
                                    class="<?= isset($event['event_status']) && $event['event_status'] == 'Activo' ? 'border-success-start' : 'border-danger-start' ?>">
                                    <div class="d-flex justify-content-start align-items-center">
                                        <div class="avatar-wrapper mr-3">
                                            <div class="avatar rounded-2">
                                                <img src="<?= base_url("") . $event["image"] ?>" alt="Img"
                                                    class="rounded-2 img-fluid event-img" style="width:100px; height:50px">
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span
                                                class="text-heading fw-medium event-name"><?= $event["event_name"] ?></span>
                                            <small class="text-truncate d-none d-sm-block">
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $event["short_description"] ?></td>
                                <td><?= $event["event_date"] ?></td>
                                <td><?= $event["address"] ?></td>
                                <td>
                                    <?php
                                    $categories = explode(',', $event['categories']);
                                    $prices = explode(',', $event['prices']);
                                    foreach ($categories as $index => $category) {
                                        echo $category . ' ($' . ($prices[$index] ?? 'N/A') . ')<br>';
                                    }
                                    ?>
                                </td>
                                <td><?= $event["registrations_start_date"] ?></td>
                                <td><?= $event["registrations_end_date"] ?></td>
                                <td class="d-flex">
                                    <form action="<?= base_url('admin/event/edit/' . $event['id']) ?>">
                                        <button class="js-mytooltip btn btn-outline-warning m-1" title="Editar"
                                            data-mytooltip-custom-class="align-center" data-mytooltip-direction="top"
                                            data-mytooltip-theme="warning" data-mytooltip-content="Editar"><i
                                                class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i></button>
                                    </form>


                                    <button class="js-mytooltip btn btn-outline-danger btn-delete m-1" title="Eliminar"
                                        data-toggle="modal" data-target="#delete" data-mytooltip-custom-class="align-center"
                                        data-mytooltip-direction="top" data-mytooltip-theme="danger"
                                        data-mytooltip-content="Eliminar" data-event-name="<?= $event['event_name'] ?>"
                                        data-event-id="<?= $event['id'] ?>">
                                        <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
                                    </button>

                                    <a href="<?= base_url('admin/event/inscritos/' . $event['id']) ?>"
                                        class="js-mytooltip btn btn-outline-info m-1" title="Ver Inscritos"
                                        data-mytooltip-custom-class="align-center" data-mytooltip-direction="top"
                                        data-mytooltip-theme="info" data-mytooltip-content="Ver Inscritos">
                                        <i class="fa fa-users fa-lg" aria-hidden="true"></i>
                                    </a>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal-->
    <div class="modal fade" id="delete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-2">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Eliminar</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url("admin/event/delete") ?>" id="formPago" method="post">
                        <div class="row mb-3">
                            <div class="col">
                                <p>Estas seguro de eliminar el evento : <span class="text-danger"
                                        id="text-event"></span></p>
                            </div>
                        </div>
                        <input type="hidden" name="id" id="id_pago">
                        <input type="hidden" name="cedula" id="cedula">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button form="formPago" type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
    $('.btn-delete').on('click', function () {
        var eventName = $(this).data('event-name');
        var eventId = $(this).data('event-id');

        $('#text-event').text(eventName);
        $('#formPago #id_pago').val(eventId);
    });

</script>
<?= $this->endSection() ?>
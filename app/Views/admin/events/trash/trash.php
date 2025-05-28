<?= $this->extend('layouts/admin_layout'); ?>

<?= $this->section('title') ?>
Eventos eliminados
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url("assets/css/rounded.css") ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header sty-one">
        <h1 class="text-black">Eventos eliminados</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li class="sub-bread"><i class="fa fa-angle-right"></i> Eventos eliminados</li>
        </ol>
    </div>
    <div class="content">
        <div class="info-box">
            <div class="table-responsive">
                <table id="eventTrash" class="table datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Evento</th>
                            <th class="exclude-view">Descripción del evento</th>
                            <th>Fecha del evento</th>
                            <th class="exclude-view">Dirección del evento</th>
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
                                <td><?= $event["registrations_start_date"] ?></td>
                                <td><?= $event["registrations_end_date"] ?></td>
                                <td class="d-flex">

                                    <button class="js-mytooltip btn btn-outline-success m-1 btn-restore" data-toggle="modal"
                                        data-target="#recover" data-event-name="<?= $event['event_name'] ?>"
                                        data-event-id="<?= $event['id'] ?>" data-mytooltip-custom-class="align-center"
                                        data-mytooltip-direction="top" data-mytooltip-theme="success"
                                        data-mytooltip-content="Recuperar" title="Recuperar evento">

                                        <i class="fa fa-recycle fa-lg" aria-hidden="true"></i>
                                    </button>


                                    <button class="js-mytooltip btn btn-outline-danger btn-delete m-1"
                                        title="Eliminar definitivamente" data-toggle="modal" data-target="#delete"
                                        data-mytooltip-custom-class="align-center" data-mytooltip-direction="top"
                                        data-mytooltip-theme="danger" data-mytooltip-content="Eliminar definitivamente"
                                        data-event-name="<?= $event['event_name'] ?>" data-event-id="<?= $event['id'] ?>">
                                        <i class="fa fa-ban" aria-hidden="true"></i>
                                    </button>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="recover" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-2">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Restaurar</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url("admin/event/trash/restore") ?>" id="formPago" method="post">
                    <div class="row mb-3">
                        <div class="col">
                            <p>Estas seguro de restaurar el evento : <span class="text-danger"
                                    id="text-event-restore"></span></p>
                        </div>
                    </div>
                    <input type="hidden" name="id" id="id_event_restore">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button form="formPago" type="submit" class="btn btn-success">Restaurar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal-->
<div class="modal fade" id="delete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-2">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Eliminar definitivamente</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url("admin/event/deleteAll") ?>" id="formDelete" method="post">
                    <div class="row mb-3">
                        <div class="col">
                            <p>Estas seguro de eliminar el evento : <span class="text-danger" id="text-event"></span>
                                con todas sus inscripciones y pagos?
                            </p>
                        </div>
                    </div>
                    <input type="hidden" name="id" id="id_event_delete">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button form="formDelete" type="submit" class="btn btn-danger">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
    $('.btn-restore').on('click', function () {
        var eventName = $(this).data('event-name');
        var eventId = $(this).data('event-id');

        $('#text-event-restore').text(eventName);
        $('#id_event_restore').val(eventId);
    });

    $('.btn-delete').on('click', function () {
        var eventName = $(this).data('event-name');
        var eventId = $(this).data('event-id');

        $('#text-event').text(eventName);
        $('#formDelete #id_event_delete').val(eventId);
    });
</script>
<?= $this->endSection() ?>
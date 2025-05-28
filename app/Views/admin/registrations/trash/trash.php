<?= $this->extend('layouts/admin_layout'); ?>

<?= $this->section('title') ?>
Inscripciones eliminadas
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url("assets/css/rounded.css") ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header sty-one">
        <h1 class="text-black">Inscripciones eliminadas</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li class="sub-bread"><i class="fa fa-angle-right"></i> Inscripciones eliminadas</li>
        </ol>
    </div>
    <div class="content">
        <div class="info-box">
            <!-- Botón para eliminar todos los registros eliminados -->
            <div class="d-flex justify-content-end">
                <button class="js-mytooltip btn btn-outline-danger m-1" data-toggle="modal" data-target="#delete"
                    data-mytooltip-custom-class="align-center" data-mytooltip-direction="left"
                    data-mytooltip-theme="danger" data-mytooltip-content="Eliminar todos los registros permanentemente"
                    title="Eliminar todos los registros permanentemente">

                    <i class="fa fa-ban fa-lg" aria-hidden="true"></i>
                </button>
            </div>
            <div class="table-responsive">
                <table id="inscripcionesTrash" class="table datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Usuario inscrito</th>
                            <th class="exclude-view">Cédula</th>
                            <th>Dirección</th>
                            <th class="exclude-view">Teléfono</th>
                            <th>Email</th>
                            <th>Evento</th>
                            <th>Categoría</th>
                            <th class="exclude-column">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registrations as $key => $registro): ?>
                            <tr>
                                <td><?= $registro['full_name_user'] ?></td>
                                <td><?= $registro["ic"] ?></td>
                                <td><?= $registro["address"] ?></td>
                                <td><?= $registro["phone"] ?></td>
                                <td><?= $registro["email"] ?></td>
                                <td><?= $registro["event_name"] ?></td>
                                <td><?= $registro["monto_category"] ?></td>
                                <td>
                                    <div class="d-flex">
                                        <button class="js-mytooltip btn btn-outline-success m-1 btn-restore"
                                            data-toggle="modal" data-target="#recover"
                                            data-event-name="<?= $registro['event_name'] ?>"
                                            data-inscrito-id="<?= $registro['id'] ?>"
                                            data-category-name="<?= $registro['monto_category'] ?>"
                                            data-user-name="<?= $registro['full_name_user'] ?>"
                                            data-mytooltip-custom-class="align-center" data-mytooltip-direction="top"
                                            data-mytooltip-theme="success" data-mytooltip-content="Recuperar"
                                            title="Recuperar evento">

                                            <i class="fa fa-recycle fa-lg" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
                    <form action="<?= base_url("admin/inscritos/restore") ?>" id="formPago" method="post">
                        <div class="row mb-3">
                            <div class="col">
                                <p>Estas seguro de restaurar la inscripción del usuario : <strong
                                        id="text-user-restore"></strong><br /> para el evento : <strong
                                        id="text-event-restore"></strong> <br /> con el monto de : <strong
                                        id="text-category-restore"></strong></p>
                            </div>
                        </div>
                        <input type="hidden" name="id" id="id_inscrito_restore">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button form="formPago" type="submit" class="btn btn-success">Restaurar</button>
                </div>
            </div>
        </div>
    </div>

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
                    <form action="<?= base_url('admin/inscritos/deleteAll') ?>" method="post" id="formDelete" >
                        <div class="row text-center">
                            <div class="col">
                                <p> <strong class="text-danger">¿Estas seguro de eliminar todos los registros permanentemente ?</strong></p>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button form="formDelete" type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
    $('.btn-restore').on('click', function () {
        var eventName = $(this).data('event-name');
        var categoryName = $(this).data('category-name');
        var userName = $(this).data('user-name');
        var inscritoId = $(this).data('inscrito-id');

        $('#text-event-restore').text(eventName);
        $('#text-category-restore').text(categoryName);
        $('#text-user-restore').text(userName);
        $('#id_inscrito_restore').val(inscritoId);
    });

</script>
<?= $this->endSection() ?>
<?= $this->extend('layouts/payments_layout'); ?>

<?= $this->section('title') ?>
Filtrado
<?= $this->endSection() ?>


<?= $this->section('css') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">

    <div class="content-header sty-one">
        <h1 class="text-black">Buscar Inscripciones por Cédula</h1>
        <ol class="breadcrumb">
            <li><a href="#">Home</a></li>
            <li><i class="fa fa-angle-right"></i> Buscar</li>
        </ol>
    </div>

    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-gray ">
                        <h5 class="text-dark m-b-0">Filtrado</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url('punto/pago/buscar') ?>" method="post" class="mt-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="cedula">Número de Cédula<span class="text-danger"> *</span></label>
                                        <input class="form-control" type="text" id="cedula" name="cedula"
                                            value="<?= isset($last_data) ? display_data($last_data, 'cedula') : '' ?>"
                                            required>

                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'cedula') : '' ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="estado">Estado</label>
                                        <select id="estado" class="form-control" name="estado">
                                            <option value="" disabled>Seleccione una categoría</option>
                                                <option value="1" selected <?= isset($last_data['estado'])&&$last_data['estado']==1 ? 'selected' : '' ?>>
                                                    Pendiente
                                                </option>
                                                <option value="2" <?= isset($last_data['estado'])&&$last_data['estado']==2 ? 'selected' : '' ?>>
                                                    Completado
                                                </option>
                                        </select>
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'estado') : '' ?></span>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success">Buscar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>



<?= $this->section('scripts') ?>
<?= $this->endSection() ?>
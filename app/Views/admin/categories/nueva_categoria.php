<?= $this->extend('layouts/admin_layout'); ?>

<?= $this->section('title') ?>
Agregar categorías
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<!-- Select 2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-wrapper bg-white">
    <div class="content-header sty-one shadow">
        <h1 class="text-black">Nueva categoría</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li class="sub-bread"><i class="fa fa-angle-right"></i> Categoría</li>
            <li><i class="fa fa-angle-right"></i> Nueva categoría</li>
        </ol>
    </div>


    <!-- Main content -->

    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">

                    <div class="card-header bg-gray">

                        <h5 class="pull-left text-dark m-b-0">Agregar categoría</h5>
                        <div class="pull-right">
                            <div class="row">
                                <label class="control-label">Monto de comisión <span class="text-primary">$<?= $additional_charge ?> USD</span></label>
                            </div>
                        </div>

                    </div>

                    <div class="card-body">

                        <form action="<?= base_url("admin/category/add") ?>" method="POST"
                            enctype="multipart/form-data">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Nombre de la categoría</label>
                                        <input class="form-control" name="category_name" placeholder=""
                                            value="<?= isset($last_data) ? display_data($last_data, 'category_name') : '' ?>"
                                            type="text">
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'category_name') : '' ?></span>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Valor de la categoría</label>
                                        <input class="form-control" name="category_value" placeholder=""
                                            value="<?= isset($last_data) ? display_data($last_data, 'cantidad_dinero') : '' ?>"
                                            type="number">
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'cantidad_dinero') : '' ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Descripción de la categoría</label>
                                        <textarea class="form-control" name="short_description" id="" rows="4"
                                            placeholder=""><?= isset($last_data) ? display_data($last_data, 'short_description') : '' ?></textarea>
                                        <span
                                            class="text-danger"><?= isset($validation) ? display_data($validation, 'short_description') : '' ?></span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-success">Crear categoría nueva</button>
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
<!-- Select 2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        $('.select2').select2();
    });
</script>

<?= $this->endSection() ?>
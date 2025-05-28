<?= $this->extend('layouts/payments_layout'); ?>

<?= $this->section('title') ?>
Mi perfil
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header sty-one">
        <h1 class="text-black">Mi perfil</h1>
        <ol class="breadcrumb">
            <li><a href="#">Inicio</a></li>
            <li class="sub-bread"><i class="fa fa-angle-right"></i>Mi perfil</li>
        </ol>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="row">
            <div class="col-lg-4">
                <div class="user-profile-box m-b-3">
                    <div class="box-profile text-white"> <img class="profile-user-img img-responsive img-circle m-b-2"
                            src="<?= base_url('assets/images/user_pagos.jpg') ?>" alt="Usuario Image">
                        <h3 class="profile-username text-center">
                            <?= $user[0]['first_name'] . " " . $user[0]['last_name'] ?>
                        </h3>
                        <p class="text-center">&copy; <?= $user[0]['first_name'] ?></p>
                        <p class="text-center"></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="info-box">
                    <div class="card tab-style1">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs profile-tab" role="tablist">
                            <li class="nav-item"> <a class="nav-link <?= !isset($last_action) || $last_action === '' ? 'active' : '' ?>" data-toggle="tab" href="#perfil" role="tab"
                                    aria-expanded="false">Perfil</a> </li>
                            <li class="nav-item"> <a class="nav-link <?= isset($last_action) && $last_action === 'update' ? 'active' : '' ?>" data-toggle="tab" href="#config"
                                    role="tab">Configuración</a> </li>
                            <li class="nav-item"> <a class="nav-link <?= isset($last_action) && $last_action === 'recover' ? 'active' : '' ?>" data-toggle="tab" href="#password"
                                    role="tab">Contraseña</a> </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <!--second tab-->
                            <div class="tab-pane <?= !isset($last_action) || $last_action === '' ? 'active' : '' ?>" id="perfil" role="tabpanel" aria-expanded="false">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6 col-xs-6 b-r"> <strong>Cédula</strong> <br>
                                            <p class="text-muted">
                                                <?= $user[0]['ic'] ?>
                                            </p>
                                        </div>

                                        <div class="col-lg-6 col-xs-6 b-r"> <strong>Correo Electrónico</strong> <br>
                                            <p class="text-muted"><?= $user[0]['email'] ?></p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-6 col-xs-6 b-r"> <strong>Nombres</strong> <br>
                                            <p class="text-muted">
                                                <?= $user[0]['first_name'] ?>
                                            </p>
                                        </div>
                                        <div class="col-lg-6 col-xs-6 b-r"> <strong>Apellidos</strong> <br>
                                            <p class="text-muted">
                                                <?= $user[0]['last_name'] ?>
                                            </p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-6 col-xs-6 b-r"> <strong>Teléfono</strong> <br>
                                            <p class="text-muted">
                                                <?= $user[0]['phone_number'] ?>
                                            </p>
                                        </div>
                                        <div class="col-lg-6 col-xs-6 b-r"> <strong>Dirección</strong> <br>
                                            <p class="text-muted">
                                                <?= $user[0]['address'] ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane <?= isset($last_action) && $last_action === 'update' ? 'active' : '' ?>" id="config" role="tabpanel">
                                <div class="card-body">
                                    <form method="post" action="<?= base_url("punto/pago/user/update") ?>"
                                        class="form-horizontal form-material">
                                        <div class="row">
                                            <div class="form-group col-md-6 has-feedback">
                                                <label class="col-md-12">Cédula</label>
                                                <div class="col-md-12">
                                                    <input class="form-control form-control-line" type="text"
                                                        name="cedula"
                                                        value="<?= $user[0]['ic'] ?>">
                                                    <span
                                                        class="text-danger"><?= isset($validation) ? display_data($validation, 'ic') : '' ?></span>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 has-feedback">
                                                <label for="example-email" class="col-md-12">Correo Electrónico</label>
                                                <div class="col-md-12">
                                                    <input class="form-control form-control-line" name="email"
                                                        id="example-email" type="email"
                                                        value="<?= $user[0]['email'] ?>">
                                                    <span
                                                        class="text-danger"><?= isset($validation) ? display_data($validation, 'email') : '' ?></span>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 has-feedback">
                                                <label class="col-md-12">Nombres</label>
                                                <div class="col-md-12">
                                                    <input class="form-control form-control-line" type="text"
                                                        name="first_name"
                                                        value="<?= $user[0]['first_name'] ?>">
                                                    <span
                                                        class="text-danger"><?= isset($validation) ? display_data($validation, 'first_name') : '' ?></span>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 has-feedback">
                                                <label class="col-md-12">Apellidos</label>
                                                <div class="col-md-12">
                                                    <input class="form-control form-control-line" type="text"
                                                        name="last_name"
                                                        value="<?= $user[0]['last_name'] ?>">
                                                    <span
                                                        class="text-danger"><?= isset($validation) ? display_data($validation, 'last_name') : '' ?></span>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 has-feedback">
                                                <label class="col-md-12">Teléfono</label>
                                                <div class="col-md-12">
                                                    <input class="form-control form-control-line" type="text"
                                                        name="phone_number"
                                                        value="<?= $user[0]['phone_number'] ?>">
                                                    <span
                                                        class="text-danger"><?= isset($validation) ? display_data($validation, 'phone_number') : '' ?></span>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 has-feedback">
                                                <label class="col-md-12">Dirección</label>
                                                <div class="col-md-12">
                                                    <input class="form-control form-control-line" type="text"
                                                        name="address"
                                                        value="<?= $user[0]['address'] ?>">
                                                    <span
                                                        class="text-danger"><?= isset($validation) ? display_data($validation, 'address') : '' ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <button class="btn btn-success">Actualizar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="tab-pane <?= isset($last_action) && $last_action === 'recover' ? 'active' : '' ?>" id="password" role="tabpanel">
                                <div class="card-body">
                                    <form method="post" action="<?= base_url("punto/pago/user/recoverPassword") ?>"
                                        class="form-horizontal form-material">
                                        <div class="row">
                                            <div class="form-group col-6 has-feedback">
                                                <label class="col-md-12">Nueva contraseña</label>
                                                <div class="col-md-12">
                                                    <input class="form-control form-control-line" type="text"
                                                        name="password"
                                                        value="<?= isset($last_data) ? display_data($last_data, 'password') : '' ?>">
                                                    <span
                                                        class="text-danger"><?= isset($validation) ? display_data($validation, 'password') : '' ?></span>
                                                </div>
                                            </div>
                                            <div class="form-group col-6 has-feedback">
                                                <label class="col-md-12">Repetir contraseña</label>
                                                <div class="col-md-12">
                                                    <input class="form-control form-control-line" type="password"
                                                        name="password_repeat"
                                                        value="<?= isset($last_data) ? display_data($last_data, 'password_repeat') : '' ?>">
                                                    <span
                                                        class="text-danger"><?= isset($validation) ? display_data($validation, 'password_repeat') : '' ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <button class="btn btn-success">Actualizar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main row -->
    </div>
    <!-- /.content -->
</div>
<?= $this->endSection() ?>
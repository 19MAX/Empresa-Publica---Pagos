<?= $this->extend('layouts/login_layout'); ?>

<?= $this->section('title') ?>
Crear cuenta
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="auth-wrapper auth-v1 px-2">
    <div class="auth-inner py-2">
        <!-- Register v1 -->
        <div class="card mb-0">
            <div class="card-body">
                <div class="brand-logo">
                    <h2 class="brand-text text-primary ml-1">Crear cuenta</h2>
                </div>
                <form class="auth-register-form mt-2" action="" method="POST">
                    <div class="form-group">
                        <label for="register-username" class="form-label">Nombre de usuario</label>
                        <input type="text" class="form-control" id="register-username" name="register-username"
                            placeholder="johndoe" aria-describedby="register-username" tabindex="1" autofocus />
                    </div>
                    <div class="form-group">
                        <label for="register-email" class="form-label">Correo electrónico</label>
                        <input type="text" class="form-control" id="register-email" name="register-email"
                            placeholder="john@example.com" aria-describedby="register-email" tabindex="2" />
                    </div>

                    <div class="form-group">
                        <label for="register-password" class="form-label">Contraseña</label>

                        <div class="input-group input-group-merge form-password-toggle">
                            <input type="password" class="form-control form-control-merge" id="register-password"
                                name="register-password"
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                aria-describedby="register-password" tabindex="3" />
                            <div class="input-group-append">
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-block" tabindex="5">Crear cuenta</button>
                </form>

                <p class="text-center mt-2">
                    <span>Ya tienes una cuenta?</span>
                    <a href="<?=base_url("login")?>">
                        <span>Iniciar sesión</span>
                    </a>
                </p>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url() ?>app-assets/js/scripts/pages/page-auth-register.js"></script>
<?= $this->endSection() ?>
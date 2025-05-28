<?= $this->extend('layouts/login_layout'); ?>

<?= $this->section('title') ?>
Recuperar contraseña
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="auth-wrapper auth-v1 px-2">
    <div class="auth-inner py-2">
        <!-- Forgot Password v1 -->
        <div class="card mb-0">
            <div class="card-body">
                <div class="brand-logo">
                    <h2 class="brand-text text-primary ml-1">Recuperar contraseña</h2>
                </div>

                <h4 class="card-title mb-1">Olvido su contraseña? 🔒</h4>
                <p class="card-text mb-2">Ingresa tu correo electrónico y te enviaremos instrucciones para restablecer tu contraseña</p>

                <form class="auth-forgot-password-form mt-2" action="" method="POST">
                    <div class="form-group">
                        <label for="forgot-password-email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="forgot-password-email" name="forgot-password-email"
                            placeholder="john@example.com" aria-describedby="forgot-password-email" tabindex="1"
                            autofocus />
                    </div>
                    <button class="btn btn-primary btn-block" tabindex="2">Enviar enlace de reinicio</button>
                </form>

                <p class="text-center mt-2">
                    <a href="<?=base_url("login")?>"> <i data-feather="chevron-left"></i> Regresar al login</a>
                </p>
            </div>
        </div>
        <!-- /Forgot Password v1 -->
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url() ?>app-assets/js/scripts/pages/page-auth-forgot-password.js"></script>
<?= $this->endSection() ?>
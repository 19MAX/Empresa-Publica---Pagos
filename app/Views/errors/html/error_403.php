<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Error 403</title>
    <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="<?= base_url("dist/bootstrap/css/bootstrap.min.css") ?>">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link rel="icon" href="<?= base_url("assets/images/icono.jpeg"); ?>" type="image/jpeg">

    <link rel="stylesheet" href="<?= base_url("dist/css/style.css") ?>">
    <link rel="stylesheet" href="<?= base_url("dist/css/font-awesome/css/font-awesome.min.css") ?>">
    <link rel="stylesheet" href="<?= base_url("dist/css/et-line-font/et-line-font.css") ?>">
    <link rel="stylesheet" href="<?= base_url("dist/css/themify-icons/themify-icons.css") ?>">
    <!-- Preloader -->
    <link rel="stylesheet" href="<?= base_url("assets/css/preloader.css") ?>">

</head>

<body class="hold-transition lockscreen">
    <!-- Preloader HTML -->
    <div id="preloader" style="display: none;">
        <div class="spinner"></div>
        <p class="loading-text">Cargando<span class="dot">.</span><span class="dot">.</span><span class="dot">.</span>
        </p>
    </div>
    <div>
        <div class="error-page text-center">
            <h2 class="headline" style="color: #0C244B;"> 403</h2>
            <div>
                <h3><i class="fa fa-warning text-warning"></i> ¡Oops! Acceso prohibido.</h3>
                <p>No tienes permiso para acceder a esta página.
                    Mientras tanto, puedes <a href="<?= base_url("/") ?>">volver al inicio</a></p>
            </div>
        </div>
        <div class="lockscreen-footer text-center m-t-3">
            Copyright 2025 &copy; PROSERVI UEB-EP|
            <a href="" class="text-decoration-none link-light">Softec Apps S.A.S</a>

        </div>
    </div>

    <script src="<?= base_url("dist/js/jquery.min.js") ?>"></script>
    <script src="<?= base_url("dist/bootstrap/js/bootstrap.min.js") ?>"></script>
    <script src="<?= base_url("dist/js/niche.js") ?>"></script>
    <script src="<?= base_url("assets/js/preloader.js") ?>"></script>
</body>

</html>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pre-registro de Evento</title>
    <style>
      body {
        font-family: "Segoe UI", Tahoma, sans-serif;
        background-color: #f4f6fa;
        margin: 0;
        padding: 0;
        color: #333;
      }

      .container {
        max-width: 600px;
        margin: 0 auto;
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      }

      .header {
        background-color: #0c244b;
        color: white;
        text-align: center;
        padding: 25px 15px;
      }

      .header img {
        max-width: 90px;
        margin-bottom: 10px;
      }

      h1 {
        font-size: 1.3rem;
        margin: 0;
      }

      .content {
        padding: 25px;
        text-align: center;
        border-left: 1px solid #0c244b;
        border-right: 1px solid #0c244b;
      }

      p {
        line-height: 1.5;
        margin: 10px 0;
      }

      .highlight-box {
        background-color: #e5e8ff;
        color: #0c244b;
        border-radius: 8px;
        padding: 6px 10px;
        display: inline-block;
        font-weight: bold;
        margin: 3px;
      }

      .alert {
        background-color: #ffe5e5;
        border: 1px solid #c3171b;
        color: #c3171b;
        border-radius: 8px;
        padding: 10px;
        font-weight: bold;
        margin-top: 15px;
      }

      .alert-success {
        background-color: #f4ffe5;
        border: 1px solid #1dc317;
        color: #1dc317;
        border-radius: 8px;
        padding: 10px;
        font-weight: bold;
        margin-top: 15px;
      }

      .btn {
        display: inline-block;
        background-color: #0c244b;
        color: white !important;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        margin: 15px auto 0 auto;
        text-align: center;
        font-size: 0.8rem;
        max-width: 90%;
      }

      /* ====== ESTILO DE TABLA ====== */
      .bank-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        font-size: 0.95rem;
      }

      .bank-table th,
      .bank-table td {
        text-align: left;
        padding: 10px 12px;
        border-bottom: 1px solid #e0e6f2;
      }

      .bank-table th {
        width: 40%;
        background-color: #f8f9ff;
        color: #0c244b;
      }

      .bank-table tr:nth-child(1) th {
        background-color: #ffdd00;
        color: #000;
        font-weight: bold;
      }

      .bank-table tr:hover {
        background-color: #f3f6ff;
      }

      .footer {
        background-color: #0c244b;
        color: white;
        text-align: center;
        padding: 15px;
        font-size: 0.85rem;
      }

      @media (max-width: 600px) {
        .content {
          padding: 20px;
        }
        .btn {
          display: block;
          width: 100%;
        }
      }
    </style>
  </head>

  <body>
    <div class="container">
      <div class="header">
        <img src="<?= base_url('assets/images/email/logo-ep.png') ?>" alt="Logo" />
        <h1>Pre-registro confirmado</h1>
      </div>

      <div class="content">
        <p style="text-align: left">Hola <strong><?= $user ?></strong>,</p>

        <p style="text-align: left">
          Has realizado tu <strong>pre-registro</strong> para el evento
          <span class="highlight-box"><?= $evento ?></span> en la categoría
          <span class="highlight-box"><?= $categoria ?></span><br />
          El monto a cancelar es
          <span class="highlight-box">$<?= number_format($precio, 2) ?></span>
        </p>

        Tu código de pago es
        <span class="highlight-box"><?= $codigoPago ?></span>

        <div class="alert">
          Tu inscripción se completará cuando realices el pago.<br />
          Tienes 48 horas para hacerlo.
        </div>

        <h3 style="text-align: left; color: #0c244b; margin-top: 25px">
          💳 Puedes realizar el pago en la siguiente cuenta:
        </h3>

        <table class="bank-table">
          <tbody>
            <tr>
              <th scope="row">Banco</th>
              <td>Banco de Pichincha</td>
            </tr>
            <tr>
              <th scope="row">Tipo de Cuenta</th>
              <td>Corriente</td>
            </tr>
            <tr>
              <th scope="row">Nombre</th>
              <td>EMPRESA PUBLICA PROSERVI UEB EP</td>
            </tr>
            <tr>
              <th scope="row">Cuenta Bancaria</th>
              <td>2100238825</td>
            </tr>
            <tr>
              <th scope="row">RUC</th>
              <td>0260024190001</td>
            </tr>
          </tbody>
        </table>

        <a
          href="<?= base_url('?modal=metodo&codigoPago=' . $codigoPago) ?>"
          class="btn"
        >
          COMPLETAR INSCRIPCIÓN
        </a>

        <div class="alert-success">
          Te recomendamos pagar con <strong>tarjeta</strong> o en
          <strong>puntos físicos</strong> para que tu inscripción se confirme de
          inmediato.
        </div>
<!-- 
        <h3 style="text-align: left; color: #0c244b; margin-top: 25px">
          ¿Cómo pagar?
        </h3> -->
      </div>


        <!-- <div class="payment-cards">
          <a
            href="https://wa.me/+593989026071"
            class="payment-card"
          >
            <table
              width="100%"
              cellpadding="0"
              cellspacing="0"
              border="0"
              style="vertical-align: middle"
            >
              <tr>
                <td style="vertical-align: middle">
                  <strong>💳 Pago con tarjeta</strong><br />
                  <span class="payment-subtext">
                    Confirma tu inscripción de inmediato
                  </span>
                </td>
                <td align="right" style="vertical-align: middle; width: 20px">
                  <img
                    src="<?= base_url('assets/images/icons/arrow-right.png') ?>"
                    alt="→"
                    width="14"
                    height="14"
                    style="display: block"
                  />
                </td>
              </tr>
            </table>
          </a>

          <a
            href="https://wa.me/+593989026071"
            class="payment-card"
          >
            <table
              width="100%"
              cellpadding="0"
              cellspacing="0"
              border="0"
              style="vertical-align: middle"
            >
              <tr>
                <td style="vertical-align: middle">
                  <strong>💵 Pago en efectivo</strong><br />
                  <span class="payment-subtext">
                    Confirma tu inscripción al instante
                  </span>
                </td>
                <td align="right" style="vertical-align: middle; width: 20px">
                  <img
                    src="<?= base_url('assets/images/icons/arrow-right.png') ?>"
                    alt="→"
                    width="14"
                    height="14"
                    style="display: block"
                  />
                </td>
              </tr>
            </table>
          </a>

          <a
            href="https://wa.me/+593989026071"
            class="payment-card"
          >
            <table
              width="100%"
              cellpadding="0"
              cellspacing="0"
              border="0"
              style="vertical-align: middle"
            >
              <tr>
                <td style="vertical-align: middle">
                  <strong>🏦 Depósito o transferencia</strong><br />
                  <span class="payment-subtext">
                    Validación hasta 72 h hábiles
                  </span>
                </td>
                <td align="right" style="vertical-align: middle; width: 20px">
                  <img
                    src="<?= base_url('assets/images/icons/arrow-right.png') ?>"
                    alt="→"
                    width="14"
                    height="14"
                    style="display: block"
                  />
                </td>
              </tr>
            </table>
          </a>
        </div> -->

      <div class="footer">
        © 2025 PROSERVI UEB-EP |
        <a
          href="https://softecsa.com"
          target="_blank"
          style="color: #ffffff; text-decoration: none; font-weight: bold"
        >
          Softec Apps
        </a>
        . Todos los derechos reservados.
      </div>
    </div>
  </body>
</html>

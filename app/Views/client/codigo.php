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
        border: none;
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

      .btn {
        display: inline-block;
        background-color: #0c244b;
        color: white !important;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        margin-top: 15px;
      }

      .payment-cards {
        margin-top: 20px;
      }

      .payment-card {
        display: block;
        text-decoration: none;
        border: 1px solid #d9e1f0;
        border-radius: 10px;
        background-color: #ffffff;
        padding: 15px 18px;
        margin-bottom: 10px;
        color: #0c244b;
        text-align: left;
      }

      .payment-subtext {
        font-size: 0.9rem;
        color: #555;
      }

      .icon-arrow {
        width: 14px;
        height: 14px;
        display: block;
      }

      .footer {
        background-color: #0c244b;
        color: white;
        text-align: center;
        padding: 15px;
        font-size: 0.85rem;
        border: none;
      }

      @media (max-width: 600px) {
        .content {
          padding: 20px;
        }
        .btn {
          display: block;
          width: 100%;
          text-align: center;
        }
      }
    </style>
  </head>

  <body>
    <div class="container">
      <div class="header">
        <img
          src="<?= base_url('assets/images/email/logo-ep.png') ?>"
          alt="Logo"
        />
        <h1>Pre-registro confirmado</h1>
      </div>

      <div class="content">
        <p>
          Hola <strong><?= $user ?></strong>,
        </p>

        <p>
          Has realizado tu <strong>pre-registro</strong> para el evento
          <span class="highlight-box"><?= $evento ?></span>
          en la categoría
          <span class="highlight-box"><?= $categoria ?></span>. El monto a
          cancelar es
          <span class="highlight-box">$<?= number_format($precio, 2) ?></span
          ><br />
          Tu código de pago es
          <span class="highlight-box"><?= $codigoPago ?></span>
        </p>

        <div class="alert">
          Tu inscripción se completará cuando realices el pago.<br />
          Tienes 48 horas para hacerlo.
        </div>

        <p>
          <a
            href="<?= base_url('?modal=metodo&codigoPago=' . $codigoPago) ?>"
            class="btn"
          >
            COMPLETAR INSCRIPCIÓN
          </a>
        </p>

        <h3 style="text-align: center; color: #0c244b; margin-bottom: 8px">
          Videos de cómo pagar
        </h3>
        <p
          style="
            text-align: center;
            color: #555;
            font-size: 0.95rem;
            margin: 0 0 15px;
          "
        >
          Te recomendamos pagar con <strong>tarjeta</strong> o en
          <strong>puntos físicos</strong> para que tu inscripción se confirme de
          inmediato.
        </p>

        <div class="payment-cards">
          <!-- Pago con tarjeta -->
          <a href="https://www.youtube.com/watch?v=tkv-ai0Grno" class="payment-card">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="vertical-align: middle;">
              <tr>
                <td style="vertical-align: middle;">
                  <strong>💳 Pago con tarjeta</strong><br />
                  <span class="payment-subtext">
                    Confirma tu inscripción de inmediato
                  </span>
                </td>
                <td align="right" style="vertical-align: middle; width: 20px;">
                  <img
                    src="<?= base_url('assets/images/icons/arrow-right.png') ?>"
                    alt="→"
                    width="14"
                    height="14"
                    style="display: block;"
                  />
                </td>
              </tr>
            </table>
          </a>

          <!-- Pago en efectivo -->
          <a href="https://www.youtube.com/watch?v=tkv-ai0Grno" class="payment-card">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="vertical-align: middle;">
              <tr>
                <td style="vertical-align: middle;">
                  <strong>💵 Pago en efectivo</strong><br />
                  <span class="payment-subtext">
                    Confirma tu inscripción al instante
                  </span>
                </td>
                <td align="right" style="vertical-align: middle; width: 20px;">
                  <img
                    src="<?= base_url('assets/images/icons/arrow-right.png') ?>"
                    alt="→"
                    width="14"
                    height="14"
                    style="display: block;"
                  />
                </td>
              </tr>
            </table>
          </a>

          <!-- Depósito o transferencia -->
          <a href="https://www.youtube.com/watch?v=tkv-ai0Grno" class="payment-card">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="vertical-align: middle;">
              <tr>
                <td style="vertical-align: middle;">
                  <strong>🏦 Depósito o transferencia</strong><br />
                  <span class="payment-subtext">
                    Validación hasta 72 h hábiles
                  </span>
                </td>
                <td align="right" style="vertical-align: middle; width: 20px;">
                  <img
                    src="<?= base_url('assets/images/icons/arrow-right.png') ?>"
                    alt="→"
                    width="14"
                    height="14"
                    style="display: block;"
                  />
                </td>
              </tr>
            </table>
          </a>
        </div>
      </div>

      <div class="footer">
        © 2025 EVENTO PAGOS |
        <a
          href="https://softecsa.com"
          target="_blank"
          style="color: #ffffff; text-decoration: none; font-weight: bold"
        >
          Softec Apps
        </a>. Todos los derechos reservados.
      </div>
    </div>
  </body>
</html>

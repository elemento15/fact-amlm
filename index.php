<?php
  session_start();

  if(empty($_SESSION['username'])) {
    header('Location: login.html');
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Facturas App</title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">

    <!-- styles -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="css/dashboard.css" rel="stylesheet">
    <link href="css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="css/jquery.toastmessage.css" rel="stylesheet">
    <link href="css/estilos.css" rel="stylesheet">

    <!-- scripts -->
    <script src="lib/jquery1.11.2.min.js"></script>
    <script src="lib/jquery.blockUI.js"></script>
    <script src="lib/jquery.dataTables.min.js"></script>
    <script src="lib/jquery.toastmessage.js"></script>
    <script src="lib/bootstrap.min.js"></script>
    <script src="lib/moment.min.js"></script>
    <script src="lib/locales.js"></script>
    <script src="lib/bootstrap-datetimepicker.min.js"></script>
    <script src="lib/underscore1.8.2.min.js"></script>
    <script src="lib/canvasjs.min.js"></script>

    <script src="js/functions.js"></script>
    <script src="js/moduleGeneral.js"></script>
    <script src="js/main-menu.js"></script>
  </head>
  
  <body>
    <nav class="navbar navbar-fixed-top bg-primary">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar" aria-expanded="true" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Facturas App</a>
        </div>
        <div style="" aria-expanded="true" id="navbar" class="navbar-collapse collapse in">
          <ul class="nav navbar-nav navbar-right">
            <li class="opt-navbar" tpl="inicio/main" modulo="inicioModulo"><a href="#">Inicio</a></li>
            <li class="opt-navbar" tpl="clientes/main" modulo="clientesModulo"><a href="#">Clientes</a></li>
            <li class="opt-navbar" tpl="facturas/main" modulo="facturasModulo"><a href="#">Facturas</a></li>
            <li class="opt-navbar" tpl="pagos/main" modulo="pagosModulo"><a href="#">Pagos SAT</a></li>
            <li class="opt-navbar" tpl="facturas/reportes" modulo="reportesModulo"><a href="#">Reportes</a></li>
            <li class="opt-navbar" tpl="configuracion/main" modulo="configuracionModulo"><a href="#">Configuración</a></li>
            <li class="opt-navbar" opt="close-session"><a href="#">Cerrar</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <!-- Main -->
        <div class="col-sm-10 col-sm-offset-1 main"></div>
      </div>
    </div>
  </body>
</html>
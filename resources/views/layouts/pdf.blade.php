<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('nombre_archivo')</title>
    <link href="css/pdf.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-grid-only@1.0.0/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
    <div id="marca-de-agua">
        @yield('marca-de-agua')
    </div>
    <footer>
        @yield('footer-content')
    </footer>
    <div class="container">
        <div id="header" class="row">
            <div class="logo col-xs-2 col-md-2 text-center">
                <div class="mx-auto">
                    <img src="img/escudounam_azul.jpg" height="150" width="127" alt="Escudo UNAM">
                </div>
            </div>
            <div class="col-xs-8 col-md-8 text-center">
                Universidad Nacional Autónoma de México <br>
                Facultad de Ingeniería <br>
                @yield('academia_division') <br>
                @yield('contenido_encabezado')
            </div>
            <div class="logo col-xs-2 col-md-2 text-center">
                <div class="mx-auto">
                    <img src="img/escudofi_azul.jpg" height="150" width="127" alt="Escudo FI">
                </div>
            </div>
            <div class="col-xs-12 col-md-12 text-center">
            </div>
        </div>
        @yield('contenido_documento')
    </div>
</body>
</html>

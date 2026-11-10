<?php
require_once __DIR__ . '/funciones.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PaseFútbol | Entradas para vivir el fútbol</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header class="barra">
    <div class="contenedor barra-contenido">
        <a class="marca" href="index.php">
            <span class="marca-icono">PF</span>
            PaseFútbol
        </a>
        <nav class="acciones">
            <a class="link-accion" href="login.php">Iniciar sesión</a>
            <a class="btn" href="registro.php">Registrarme</a>
        </nav>
    </div>
</header>
<main>
    <section class="hero">
        <div class="contenedor hero-contenido">
            <p class="etiqueta">EL FÚTBOL SE VIVE EN LA CANCHA</p>
            <h1>Tu lugar está en la tribuna.</h1>
            <p>Conocé los partidos, organizá tus datos y preparate para alentar a tu equipo.</p>
            <div class="acciones">
                <a class="btn" href="registro.php">Crear una cuenta</a>
                <a class="btn btn-secundario" href="login.php">Ya tengo una cuenta</a>
            </div>
        </div>
    </section>
    <section class="seccion contenedor">
        <div class="encabezado-seccion">
            <h2>Próximos partidos</h2>
            <p>Ejemplos ilustrativos para el trabajo práctico. No representan eventos ni ventas reales.</p>
        </div>
        <div class="partidos">
            <article class="partido-card">
                <span class="liga">Liga Argentina · Fecha 12</span>
                <div class="versus">
                    <span class="escudo">R</span>
                    <strong>River Plate</strong>
                    <span class="vs">VS</span>
                    <span class="escudo">B</span>
                    <strong>Boca Juniors</strong>
                </div>
                <div class="partido-pie">Domingo · 17:00 hs · Estadio Monumental</div>
            </article>
            <article class="partido-card">
                <span class="liga">Liga Argentina · Fecha 12</span>
                <div class="versus">
                    <span class="escudo">R</span>
                    <strong>Racing</strong>
                    <span class="vs">VS</span>
                    <span class="escudo">I</span>
                    <strong>Independiente</strong>
                </div>
                <div class="partido-pie">Sábado · 19:30 hs · Cilindro de Avellaneda</div>
            </article>
            <article class="partido-card">
                <span class="liga">Liga Argentina · Fecha 12</span>
                <div class="versus">
                    <span class="escudo">S</span>
                    <strong>San Lorenzo</strong>
                    <span class="vs">VS</span>
                    <span class="escudo">H</span>
                    <strong>Huracán</strong>
                </div>
                <div class="partido-pie">Lunes · 20:00 hs · Nuevo Gasómetro</div>
            </article>
        </div>
    </section>
</main>
<footer class="footer">PaseFútbol · Trabajo práctico de PHP y MySQL</footer>
</body>
</html>

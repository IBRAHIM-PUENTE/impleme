<?php
session_start();

$logueado = isset($_SESSION["autenticado"])
    && $_SESSION["autenticado"] === true;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="GymManagerFit - Control Administrativo y Portal Deportivo">
    <title>GymManagerFit | Supera tus límites</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<!-- HEADER -->
<header class="header">
    <nav class="container" style="width: 100%; display: flex; justify-content: space-between; align-items: center; max-width: 1300px; margin: 0 auto;">

        <a href="index.php" class="logo">
            GYM<span>MANAGERFIT</span>
        </a>

        <button class="menu-btn" id="menuBtn" aria-label="Abrir menú" style="background: transparent; border: none; color: white; font-size: 24px; cursor: pointer; display: none;">
            ☰
        </button>

        <ul class="navbar" id="navLinks">
            <li><a href="index.php">INICIO</a></li>
            <li><a href="servicios.php">SERVICIOS</a></li>
            <li><a href="nosotros.php">NOSOTROS</a></li>
            <li><a href="planes.php">PLANES</a></li>
            <li>
                <a href="<?php echo $logueado ? 'dashboard.php' : 'login.php'; ?>" class="btn-login">
                    <?php echo $logueado ? 'MI PANEL' : 'INICIAR SESIÓN'; ?>
                </a>
            </li>
        </ul>

    </nav>
</header>


<!-- HERO -->
<section class="hero inner-hero" id="inicio" style="background-image: linear-gradient(90deg, rgba(11,15,25,.95), rgba(11,15,25,.4)), url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=1920&q=85');">
    <div class="container" style="max-width: 1300px; margin: 0 auto; padding: 0 20px; width: 100%;">
        <div class="hero-content" style="max-width: 700px;">

            <div class="eyebrow" style="color: var(--primary); font-weight: 800; letter-spacing: 4px; font-size: 13px; margin-bottom: 15px;">
                TU MEJOR VERSIÓN COMIENZA AQUÍ
            </div>

            <h1 style="font-size: clamp(40px, 5vw, 70px); font-weight: 800; line-height: 1.1; margin-bottom: 20px;">
                SUPERA TUS<br>
                <span style="color: var(--primary);">LÍMITES.</span><br>
                TRANSFORMA<br>
                TU VIDA.
            </h1>

            <p style="color: var(--text-secondary); max-width: 550px; line-height: 1.8; font-size: 15px; margin-bottom: 30px;">
                No se trata solamente de entrenar. Se trata de construir disciplina, fortalecer tu mente
                y alcanzar la mejor versión de ti mismo. Tu transformación comienza hoy.
            </p>

            <div class="hero-buttons" style="display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="#planes" class="btn btn-primary">
                    DESCUBRE NUESTROS PLANES →
                </a>
                <a href="#nosotros" class="btn btn-secondary">
                    CONÓCENOS
                </a>
            </div>

        </div>
    </div>
</section>


<!-- ESTADÍSTICAS -->
<section class="stats" style="padding: 60px 0; background: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
    <div class="container" style="max-width: 1300px; margin: 0 auto; padding: 0 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; text-align: center;">
        <div class="stat">
            <h2 style="font-size: 36px; color: var(--primary); font-weight: 800;">01</h2>
            <p style="color: var(--text-secondary); font-size: 12px; font-weight: 600; letter-spacing: 1px;">ENTRENAMIENTO</p>
        </div>
        <div class="stat">
            <h2 style="font-size: 36px; color: var(--primary); font-weight: 800;">02</h2>
            <p style="color: var(--text-secondary); font-size: 12px; font-weight: 600; letter-spacing: 1px;">DISCIPLINA</p>
        </div>
        <div class="stat">
            <h2 style="font-size: 36px; color: var(--primary); font-weight: 800;">03</h2>
            <p style="color: var(--text-secondary); font-size: 12px; font-weight: 600; letter-spacing: 1px;">SUPERACIÓN</p>
        </div>
    </div>
</section>


<!-- SERVICIOS -->
<section class="section" id="servicios" style="padding: 90px 20px; max-width: 1300px; margin: 0 auto;">
    <div class="section-title" style="text-align: center; margin-bottom: 50px;">
        <small style="color: var(--primary); font-weight: 800; letter-spacing: 3px; font-size: 12px;">LO QUE OFRECEMOS</small>
        <h2 style="font-size: 32px; margin: 10px 0; font-weight: 800;">ENTRENA A TU MANERA</h2>
        <p style="color: var(--text-secondary); font-size: 14px;">Encuentra el entrenamiento que mejor se adapte a tus objetivos y lleva tu rendimiento al siguiente nivel.</p>
    </div>

    <div class="services" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">
        <article class="service-card" style="overflow: hidden; padding: 0;">
            <img src="https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=900" alt="Musculación" style="width: 100%; height: 220px; object-fit: cover;">
            <div class="service-content" style="padding: 24px;">
                <h3 style="margin-bottom: 10px; font-size: 20px;">Musculación</h3>
                <p style="color: var(--text-secondary); font-size: 13.5px; line-height: 1.6;">Desarrolla fuerza, mejora tu resistencia y trabaja cada grupo muscular con disciplina.</p>
            </div>
        </article>

        <article class="service-card" style="overflow: hidden; padding: 0;">
            <img src="https://images.unsplash.com/photo-1534258936925-c58bed479fcb?w=900" alt="Cardio" style="width: 100%; height: 220px; object-fit: cover;">
            <div class="service-content" style="padding: 24px;">
                <h3 style="margin-bottom: 10px; font-size: 20px;">Cardio y resistencia</h3>
                <p style="color: var(--text-secondary); font-size: 13.5px; line-height: 1.6;">Mejora tu condición física, resistencia y capacidad cardiovascular.</p>
            </div>
        </article>

        <article class="service-card" style="overflow: hidden; padding: 0;">
            <img src="https://images.unsplash.com/photo-1518611012118-696072aa579a?w=900" alt="Entrenamiento funcional" style="width: 100%; height: 220px; object-fit: cover;">
            <div class="service-content" style="padding: 24px;">
                <h3 style="margin-bottom: 10px; font-size: 20px;">Entrenamiento funcional</h3>
                <p style="color: var(--text-secondary); font-size: 13.5px; line-height: 1.6;">Desarrolla movilidad, coordinación, equilibrio y rendimiento físico.</p>
            </div>
        </article>
    </div>
</section>


<!-- NOSOTROS -->
<section class="section about" id="nosotros" style="padding: 90px 20px; background: var(--bg-secondary);">
    <div class="container" style="max-width: 1300px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; align-items: center;">
        <div class="about-image">
            <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1000" alt="Instalaciones" style="width: 100%; border-radius: var(--radius-lg); height: 400px; object-fit: cover; border: 1px solid var(--border-color);">
        </div>
        <div class="about-content">
            <small style="color: var(--primary); font-weight: 800; letter-spacing: 3px; font-size: 12px;">SOMOS GYMMANAGERFIT</small>
            <h2 style="font-size: clamp(26px, 3vw, 36px); margin: 15px 0; font-weight: 800; line-height: 1.2;">NO EXISTEN LÍMITES CUANDO TIENES DETERMINACIÓN.</h2>
            <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.8; margin-bottom: 20px;">
                En GymManagerFit creemos que cada persona tiene el potencial de superarse. Nuestro objetivo es acompañarte en el camino hacia una vida más activa y saludable.
            </p>
            <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 25px; color: var(--text-primary); font-size: 13.5px;">
                <div><span style="color: var(--primary); font-weight: bold; margin-right: 8px;">✔</span> Espacio dedicado al entrenamiento ERP.</div>
                <div><span style="color: var(--primary); font-weight: bold; margin-right: 8px;">✔</span> Diferentes modalidades de ejercicio.</div>
                <div><span style="color: var(--primary); font-weight: bold; margin-right: 8px;">✔</span> Entrena a tu propio ritmo con control digital.</div>
            </div>
            <a href="#planes" class="btn btn-primary">QUIERO COMENZAR →</a>
        </div>
    </div>
</section>


<!-- PLANES -->
<section class="section" id="planes" style="padding: 90px 20px; max-width: 1300px; margin: 0 auto;">
    <div class="section-title" style="text-align: center; margin-bottom: 50px;">
        <small style="color: var(--primary); font-weight: 800; letter-spacing: 3px; font-size: 12px;">ELIGE TU CAMINO</small>
        <h2 style="font-size: 32px; margin: 10px 0; font-weight: 800;">ENCUENTRA TU PLAN IDEAL</h2>
        <p style="color: var(--text-secondary); font-size: 14px;">Selecciona la modalidad que mejor se adapte a tus objetivos de entrenamiento.</p>
    </div>

    <div class="plans" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">
        <article class="plan-card" style="text-align: center;">
            <h3 style="font-size: 18px; margin-bottom: 15px;">PLAN BÁSICO</h3>
            <div class="price" style="font-size: 24px; font-weight: 800; color: var(--primary); margin: 15px 0;">BÁSICO</div>
            <p style="color: var(--text-secondary); font-size: 13px; margin-bottom: 25px; min-height: 40px;">Una opción para comenzar tu camino hacia una vida más activa.</p>
            <a href="login.php" class="btn btn-secondary" style="width: 100%;">CONSULTAR PLAN</a>
        </article>

        <article class="plan-card featured" style="text-align: center; position: relative;">
            <div class="popular" style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%);">MÁS SOLICITADO</div>
            <h3 style="font-size: 18px; margin-bottom: 15px; margin-top: 10px;">PLAN PREMIUM</h3>
            <div class="price" style="font-size: 24px; font-weight: 800; color: var(--primary); margin: 15px 0;">PREMIUM</div>
            <p style="color: var(--text-secondary); font-size: 13px; margin-bottom: 25px; min-height: 40px;">Una alternativa para quienes buscan llevar su entrenamiento al siguiente nivel.</p>
            <a href="login.php" class="btn btn-primary" style="width: 100%;">CONSULTAR PLAN</a>
        </article>

        <article class="plan-card" style="text-align: center;">
            <h3 style="font-size: 18px; margin-bottom: 15px;">PLAN PERSONALIZADO</h3>
            <div class="price" style="font-size: 24px; font-weight: 800; color: var(--primary); margin: 15px 0;">A MEDIDA</div>
            <p style="color: var(--text-secondary); font-size: 13px; margin-bottom: 25px; min-height: 40px;">Una propuesta de entrenamiento adaptada a tus necesidades y objetivos.</p>
            <a href="login.php" class="btn btn-secondary" style="width: 100%;">CONSULTAR PLAN</a>
        </article>
    </div>
</section>


<!-- LLAMADA A LA ACCIÓN -->
<section class="cta" style="padding: 90px 20px; text-align: center; background: linear-gradient(rgba(11,15,25,.9), rgba(11,15,25,.9)), url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1600'); background-size: cover; background-position: center; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
    <div class="container" style="max-width: 800px; margin: 0 auto;">
        <h2 style="font-size: clamp(26px, 4vw, 40px); font-weight: 800; margin-bottom: 15px;">EL CAMBIO COMIENZA CONTIGO.</h2>
        <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 30px;">Deja de esperar el momento perfecto. Haz que hoy sea el primer día de tu transformación.</p>
        <a href="login.php" class="btn btn-primary" style="padding: 14px 28px; font-size: 14px;">COMIENZA AHORA →</a>
    </div>
</section>


<!-- FOOTER -->
<footer class="footer">
    <div class="container" style="max-width: 1300px; margin: 0 auto;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; text-align: left; margin-bottom: 40px;">
            <div>
                <h3 class="logo" style="font-size: 20px; margin-bottom: 12px;">GYM<span>MANAGERFIT</span></h3>
                <p style="color: var(--text-secondary); font-size: 12.5px; line-height: 1.6;">Entrena con propósito. Supera tus límites. Construye una mejor versión de ti mismo.</p>
            </div>
            <div>
                <h3 style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px; letter-spacing: 1px;">NAVEGACIÓN</h3>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="#inicio" style="color: var(--text-secondary); text-decoration: none; font-size: 12.5px;">Inicio</a>
                    <a href="#servicios" style="color: var(--text-secondary); text-decoration: none; font-size: 12.5px;">Servicios</a>
                    <a href="#nosotros" style="color: var(--text-secondary); text-decoration: none; font-size: 12.5px;">Nosotros</a>
                    <a href="#planes" style="color: var(--text-secondary); text-decoration: none; font-size: 12.5px;">Planes</a>
                </div>
            </div>
            <div>
                <h3 style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px; letter-spacing: 1px;">CUENTA</h3>
                <a href="login.php" style="color: var(--text-secondary); text-decoration: none; font-size: 12.5px;">Iniciar sesión</a>
            </div>
        </div>
        <div class="copyright" style="padding-top: 25px; border-top: 1px solid var(--border-color); color: var(--text-muted); font-size: 11.5px; text-align: center;">
            © <?php echo date("Y"); ?> GymManagerFit. Todos los derechos reservados.
        </div>
    </div>
</footer>


<!-- MENÚ RESPONSIVE JS -->
<script>
    const menuBtn = document.getElementById("menuBtn");
    const navLinks = document.getElementById("navLinks");

    if (menuBtn) {
        menuBtn.addEventListener("click", function() {
            navLinks.classList.toggle("active");
        });
    }

    document.querySelectorAll(".navbar a").forEach(link => {
        link.addEventListener("click", function() {
            navLinks.classList.remove("active");
        });
    });
</script>

</body>
</html>
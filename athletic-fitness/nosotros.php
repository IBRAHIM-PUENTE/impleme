<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros | GymManagerFit</title>

    <!-- Enlazamos tu hoja de estilos unificada en rojo -->
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>

<!-- HEADER -->
<header class="header">
    <nav class="container" style="width: 100%; display: flex; justify-content: space-between; align-items: center; max-width: 1300px; margin: 0 auto; padding: 0 20px;">

        <a href="index.php" class="logo">
            GYM<span>MANAGERFIT</span>
        </a>

        <button class="menu-btn" onclick="toggleMenu()" style="background: transparent; border: none; color: white; font-size: 24px; cursor: pointer; display: none;">☰</button>

        <ul class="navbar" id="navLinks">
            <li><a href="index.php">INICIO</a></li>
            <li><a href="servicios.php">SERVICIOS</a></li>
            <li><a href="nosotros.php">NOSOTROS</a></li>
            <li><a href="planes.php">PLANES</a></li>
            <li><a href="login.php" class="btn-login">INICIAR SESIÓN</a></li>
        </ul>

    </nav>
</header>

<!-- HERO -->
<section class="inner-hero" style="background-image: linear-gradient(90deg, rgba(9,13,22,.97), rgba(15,23,42,.85)), url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=2000');">
    <div class="container" style="max-width: 1300px; margin: 0 auto; padding: 0 20px; width: 100%;">

        <span class="hero-label" style="color: var(--primary); font-weight: 800; letter-spacing: 4px; font-size: 12px; margin-bottom: 25px; text-transform: uppercase; display: inline-block;">
            NUESTRA IDENTIDAD
        </span>

        <h1 style="font-size: clamp(40px, 6vw, 78px); font-weight: 800; line-height: 1.05; max-width: 850px; text-transform: uppercase; margin-bottom: 25px;">
            NO SOLO ENTRENAMOS.<br>
            <span style="color: var(--primary);">TRANSFORMAMOS VIDAS.</span>
        </h1>

        <p style="max-width: 580px; color: var(--text-secondary); font-size: 15px; line-height: 1.9; margin-bottom: 35px;">
            En GymManagerFit creemos que cada persona tiene el potencial de superar sus límites. Somos un espacio donde la disciplina, la constancia y la pasión se convierten en resultados.
        </p>

        <div class="hero-buttons" style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="#historia" class="btn btn-primary">
                CONOCE NUESTRA HISTORIA →
            </a>
            <a href="servicios.php" class="btn btn-secondary">
                DESCUBRE NUESTROS SERVICIOS
            </a>
        </div>

        <div class="hero-breadcrumb" style="margin-top: 40px; color: var(--text-muted); font-size: 12px;">
            <a href="index.php" style="color: var(--text-primary); text-decoration: none;">Inicio</a>
            <span style="color: var(--primary); margin: 0 12px;">/</span>
            Nosotros
        </div>

    </div>
</section>

<main class="page-content" style="background: var(--bg-primary); min-height: 80vh; padding-bottom: 90px;">

    <!-- HISTORIA -->
    <section class="page-section" id="historia" style="padding: 100px 20px; max-width: 1300px; margin: 0 auto;">
        <div class="container">
            <div class="highlight-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); align-items: center; gap: 65px;">
                <div class="highlight-image" style="position: relative;">
                    <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1000" alt="Instalaciones deportivas" style="width: 100%; height: 480px; object-fit: cover; border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
                </div>
                <div class="highlight-info">
                    <small style="color: var(--primary); font-weight: 800; letter-spacing: 3px; font-size: 12px; text-transform: uppercase;">QUIÉNES SOMOS</small>
                    <h2 style="font-size: clamp(28px, 4vw, 40px); line-height: 1.2; margin: 20px 0; font-weight: 800;">
                        UN ESPACIO PARA CONSTRUIR TU MEJOR VERSIÓN.
                    </h2>
                    <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.9; margin-bottom: 20px;">
                        GymManagerFit nace con una idea clara: ofrecer un espacio digital y físico donde las personas puedan trabajar por sus objetivos de gestión corporal y personales.
                    </p>
                    <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.9; margin-bottom: 20px;">
                        Creemos que el entrenamiento es mucho más que levantar pesas o realizar ejercicios. Es desarrollar disciplina, fortalecer la confianza y aprender a superar los desafíos.
                    </p>
                    <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.9; margin-bottom: 25px;">
                        Queremos que cada visita al gimnasio represente una oportunidad avanzada para progresar.
                    </p>
                    <a href="planes.php" class="btn btn-primary">
                        COMIENZA TU CAMBIO →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- MISIÓN Y VISIÓN -->
    <section class="highlight-section" style="background: var(--bg-secondary); padding: 90px 20px; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
        <div class="container" style="max-width: 1300px; margin: 0 auto;">
            <div class="section-heading" style="text-align: center; max-width: 750px; margin: 0 auto 60px;">
                <small style="color: var(--primary); font-size: 12px; font-weight: 800; letter-spacing: 4px; text-transform: uppercase;">NUESTRO PROPÓSITO</small>
                <h2 style="font-size: clamp(30px, 4vw, 48px); font-weight: 800; text-transform: uppercase; margin: 18px 0;">LO QUE NOS DEFINE</h2>
                <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.9;">
                    Trabajamos con una filosofía centrada en el crecimiento, la disciplina y el bienestar integral.
                </p>
            </div>

            <div class="values-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">
                <article class="value-card" style="background: var(--bg-primary); padding: 40px 30px; border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
                    <div class="value-icon" style="font-size: 30px; color: var(--primary); margin-bottom: 20px;">◎</div>
                    <h3 style="font-size: 20px; margin-bottom: 15px; font-weight: 800;">Nuestra misión</h3>
                    <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.9;">
                        Promover un estilo de vida activo y saludable ofreciendo un espacio de entrenamiento moderno donde las personas puedan desarrollar sus capacidades y alcanzar objetivos.
                    </p>
                </article>

                <article class="value-card" style="background: var(--bg-primary); padding: 40px 30px; border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
                    <div class="value-icon" style="font-size: 30px; color: var(--primary); margin-bottom: 20px;">◈</div>
                    <h3 style="font-size: 20px; margin-bottom: 15px; font-weight: 800;">Nuestra visión</h3>
                    <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.9;">
                        Ser un gimnasio líder reconocido por fomentar la disciplina, la superación personal y una cultura de entrenamiento basada en el compromiso y la constancia.
                    </p>
                </article>

                <article class="value-card" style="background: var(--bg-primary); padding: 40px 30px; border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
                    <div class="value-icon" style="font-size: 30px; color: var(--primary); margin-bottom: 20px;">✦</div>
                    <h3 style="font-size: 20px; margin-bottom: 15px; font-weight: 800;">Nuestra filosofía</h3>
                    <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.9;">
                        Creemos que los grandes resultados se construyen con pequeños esfuerzos diarios. Cada entrenamiento es una oportunidad para ser mejor.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <!-- VALORES -->
    <section class="page-section" style="padding: 100px 20px; max-width: 1300px; margin: 0 auto;">
        <div class="container">
            <div class="section-heading" style="text-align: center; max-width: 750px; margin: 0 auto 60px;">
                <small style="color: var(--primary); font-size: 12px; font-weight: 800; letter-spacing: 4px; text-transform: uppercase;">NUESTROS PRINCIPIOS</small>
                <h2 style="font-size: clamp(30px, 4vw, 48px); font-weight: 800; text-transform: uppercase; margin: 18px 0;">LOS VALORES QUE NOS IMPULSAN</h2>
                <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.9;">
                    Nuestra identidad se construye sobre principios que motivan cada sesión de entrenamiento.
                </p>
            </div>

            <div class="values-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">
                <article class="value-card" style="background: var(--bg-secondary); padding: 35px 25px; border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
                    <div class="value-icon" style="font-size: 24px; color: var(--primary); font-weight: 900; margin-bottom: 15px;">01</div>
                    <h3 style="font-size: 18px; margin-bottom: 12px; font-weight: 800;">Disciplina</h3>
                    <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.8;">Entendemos que la constancia es la base para conseguir resultados duraderos.</p>
                </article>

                <article class="value-card" style="background: var(--bg-secondary); padding: 35px 25px; border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
                    <div class="value-icon" style="font-size: 24px; color: var(--primary); font-weight: 900; margin-bottom: 15px;">02</div>
                    <h3 style="font-size: 18px; margin-bottom: 12px; font-weight: 800;">Compromiso</h3>
                    <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.8;">Nos esforzamos por crear un entorno donde cada socio trabaje por sus metas.</p>
                </article>

                <article class="value-card" style="background: var(--bg-secondary); padding: 35px 25px; border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
                    <div class="value-icon" style="font-size: 24px; color: var(--primary); font-weight: 900; margin-bottom: 15px;">03</div>
                    <h3 style="font-size: 18px; margin-bottom: 12px; font-weight: 800;">Superación</h3>
                    <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.8;">Buscamos motivar a nuestros miembros a superar sus propios límites.</p>
                </article>

                <article class="value-card" style="background: var(--bg-secondary); padding: 35px 25px; border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
                    <div class="value-icon" style="font-size: 24px; color: var(--primary); font-weight: 900; margin-bottom: 15px;">04</div>
                    <h3 style="font-size: 18px; margin-bottom: 12px; font-weight: 800;">Respeto</h3>
                    <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.8;">Promovemos un ambiente positivo, inclusivo y respetuoso para todos.</p>
                </article>

                <article class="value-card" style="background: var(--bg-secondary); padding: 35px 25px; border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
                    <div class="value-icon" style="font-size: 24px; color: var(--primary); font-weight: 900; margin-bottom: 15px;">05</div>
                    <h3 style="font-size: 18px; margin-bottom: 12px; font-weight: 800;">Constancia</h3>
                    <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.8;">Sabemos que cada pequeño avance cuenta en el camino físico.</p>
                </article>

                <article class="value-card" style="background: var(--bg-secondary); padding: 35px 25px; border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
                    <div class="value-icon" style="font-size: 24px; color: var(--primary); font-weight: 900; margin-bottom: 15px;">06</div>
                    <h3 style="font-size: 18px; margin-bottom: 12px; font-weight: 800;">Bienestar</h3>
                    <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.8;">Creemos en el entrenamiento como herramienta de calidad de vida.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="page-cta" style="padding: 100px 20px; text-align: center; background: linear-gradient(rgba(9,13,22,.9), rgba(9,13,22,.9)), url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1600'); background-size: cover; background-position: center; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
        <div class="container" style="max-width: 800px; margin: 0 auto;">
            <h2 style="font-size: clamp(26px, 4vw, 40px); font-weight: 800; margin-bottom: 15px;">EL CAMBIO COMIENZA CONTIGO</h2>
            <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 35px;">
                Atrévete a dar el primer paso y descubre todo lo que puedes lograr.
            </p>
            <a href="planes.php" class="btn btn-primary" style="padding: 14px 28px; font-size: 14px;">
                CONOCE NUESTROS PLANES →
            </a>
        </div>
    </section>

</main>

<!-- FOOTER -->
<footer class="footer">
    <div class="container" style="max-width: 1300px; margin: 0 auto;">
        <div class="footer-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; text-align: left; margin-bottom: 40px;">
            <div>
                <h3 class="logo" style="font-size: 20px; margin-bottom: 15px;">
                    GYM<span>MANAGERFIT</span>
                </h3>
                <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.8;">
                    Entrena con disciplina, supera tus límites y construye tu mejor versión.
                </p>
            </div>
            <div>
                <h3 style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 15px; letter-spacing: 1px;">NAVEGACIÓN</h3>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="index.php" style="color: var(--text-secondary); text-decoration: none; font-size: 13px;">Inicio</a>
                    <a href="servicios.php" style="color: var(--text-secondary); text-decoration: none; font-size: 13px;">Servicios</a>
                    <a href="nosotros.php" style="color: var(--text-secondary); text-decoration: none; font-size: 13px;">Nosotros</a>
                    <a href="planes.php" style="color: var(--text-secondary); text-decoration: none; font-size: 13px;">Planes</a>
                </div>
            </div>
            <div>
                <h3 style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 15px; letter-spacing: 1px;">CUENTA</h3>
                <a href="login.php" style="color: var(--text-secondary); text-decoration: none; font-size: 13px;">Iniciar sesión</a>
            </div>
        </div>
        <div class="copyright" style="text-align: center; padding-top: 25px; border-top: 1px solid var(--border-color); color: var(--text-muted); font-size: 11px;">
            © <?php echo date("Y"); ?> GymManagerFit. Todos los derechos reservados.
        </div>
    </div>
</footer>

<script>
function toggleMenu() {
    document.getElementById("navLinks").classList.toggle("active");
}
</script>

</body>
</html>
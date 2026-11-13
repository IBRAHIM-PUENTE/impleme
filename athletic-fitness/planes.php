<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planes y Membresías | GymManagerFit</title>

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
<section class="inner-hero" style="background-image: linear-gradient(90deg, rgba(9,13,22,.97), rgba(15,23,42,.85)), url('https://images.unsplash.com/photo-1534258936925-c58bed479fcb?w=2000');">
    <div class="container" style="max-width: 1300px; margin: 0 auto; padding: 0 20px; width: 100%;">

        <span class="hero-label" style="color: var(--primary); font-weight: 800; letter-spacing: 4px; font-size: 12px; margin-bottom: 25px; text-transform: uppercase; display: inline-block;">
            TU CAMBIO COMIENZA HOY
        </span>

        <h1 style="font-size: clamp(40px, 6vw, 78px); font-weight: 800; line-height: 1.05; max-width: 850px; text-transform: uppercase; margin-bottom: 25px;">
            INVIERTE EN TI.<br>
            <span style="color: var(--primary);">SUPERA TUS LÍMITES.</span>
        </h1>

        <p style="max-width: 580px; color: var(--text-secondary); font-size: 15px; line-height: 1.9; margin-bottom: 35px;">
            Tu transformación merece el mejor comienzo. Descubre nuestras opciones de membresía y encuentra el plan que mejor se adapta a tus objetivos.
        </p>

        <div class="hero-buttons" style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="#membresias" class="btn btn-primary">
                EXPLORAR MEMBRESÍAS →
            </a>
            <a href="servicios.php" class="btn btn-secondary">
                CONOCER SERVICIOS
            </a>
        </div>

        <div class="hero-breadcrumb" style="margin-top: 40px; color: var(--text-muted); font-size: 12px;">
            <a href="index.php" style="color: var(--text-primary); text-decoration: none;">Inicio</a>
            <span style="color: var(--primary); margin: 0 12px;">/</span>
            Planes
        </div>

    </div>
</section>

<main class="page-content" style="background: var(--bg-primary); min-height: 80vh; padding-bottom: 90px;">

    <!-- MEMBRESÍAS -->
    <section class="page-section" id="membresias" style="padding: 100px 20px; max-width: 1300px; margin: 0 auto;">
        
        <div class="section-heading" style="text-align: center; max-width: 750px; margin: 0 auto 60px;">
            <small style="color: var(--primary); font-size: 12px; font-weight: 800; letter-spacing: 4px; text-transform: uppercase;">ELIGE TU EXPERIENCIA</small>
            <h2 style="font-size: clamp(30px, 4vw, 48px); font-weight: 800; text-transform: uppercase; margin: 18px 0;">ENCUENTRA TU PLAN IDEAL</h2>
            <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.9;">
                Tenemos diferentes alternativas para acompañarte en tu camino hacia una vida más activa. Elige la membresía que mejor se adapte a ti.
            </p>
        </div>

        <div class="plans-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; align-items: stretch;">

            <!-- PLAN BASICO -->
            <article class="plan-card" style="position: relative; background: var(--bg-secondary); padding: 45px 30px; text-align: center; border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
                <h3 style="font-size: 21px; margin-bottom: 20px; font-weight: 800;">PLAN BÁSICO</h3>
                <div class="plan-price" style="color: var(--primary); font-size: 35px; font-weight: 800; margin: 20px 0;">
                    S/ 59
                </div>
                <p class="plan-description" style="color: var(--text-secondary); font-size: 13px; line-height: 1.8; min-height: 70px;">
                    Ideal para quienes desean comenzar a construir hábitos saludables.
                </p>
                <ul class="plan-features" style="list-style: none; text-align: left; margin: 30px 0;">
                    <li style="padding: 13px 0; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-primary);"><span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span> Acceso a zona de musculación</li>
                    <li style="padding: 13px 0; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-primary);"><span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span> Equipamiento cardiovascular</li>
                    <li style="padding: 13px 0; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-primary);"><span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span> Acceso en horario regular</li>
                    <li style="padding: 13px 0; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-primary);"><span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span> Rutina inicial de entrenamiento</li>
                    <li style="padding: 13px 0; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-primary);"><span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span> Vigencia mensual</li>
                </ul>
                <a href="login.php" class="btn btn-secondary" style="width: 100%;">
                    ELEGIR PLAN
                </a>
            </article>

            <!-- PLAN PREMIUM -->
            <article class="plan-card featured" style="position: relative; background: linear-gradient(180deg, rgba(220,38,38,.12), var(--bg-secondary) 50%); padding: 45px 30px; text-align: center; border: 2px solid var(--primary); border-radius: var(--radius-lg);">
                <div class="plan-tag" style="display: inline-block; background: var(--primary); color: white; padding: 6px 14px; font-size: 10px; font-weight: 800; border-radius: var(--radius-sm); letter-spacing: 1px; margin-bottom: 25px;">
                    MÁS ELEGIDO
                </div>
                <h3 style="font-size: 21px; margin-bottom: 20px; font-weight: 800;">PLAN PREMIUM</h3>
                <div class="plan-price" style="color: var(--primary); font-size: 35px; font-weight: 800; margin: 20px 0;">
                    S/ 89
                </div>
                <p class="plan-description" style="color: var(--text-secondary); font-size: 13px; line-height: 1.8; min-height: 70px;">
                    Para quienes buscan una experiencia de entrenamiento más completa.
                </p>
                <ul class="plan-features" style="list-style: none; text-align: left; margin: 30px 0;">
                    <li style="padding: 13px 0; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-primary);"><span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span> Todos los beneficios del Básico</li>
                    <li style="padding: 13px 0; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-primary);"><span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span> Acceso a entrenamiento funcional</li>
                    <li style="padding: 13px 0; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-primary);"><span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span> Orientación de entrenamiento</li>
                    <li style="padding: 13px 0; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-primary);"><span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span> Mayor flexibilidad horaria</li>
                    <li style="padding: 13px 0; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-primary);"><span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span> Seguimiento de objetivos</li>
                </ul>
                <a href="login.php" class="btn btn-primary" style="width: 100%;">
                    ELEGIR PLAN →
                </a>
            </article>

            <!-- PLAN ELITE -->
            <article class="plan-card" style="position: relative; background: var(--bg-secondary); padding: 45px 30px; text-align: center; border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
                <h3 style="font-size: 21px; margin-bottom: 20px; font-weight: 800;">PLAN ELITE</h3>
                <div class="plan-price" style="color: var(--primary); font-size: 35px; font-weight: 800; margin: 20px 0;">
                    S/ 129
                </div>
                <p class="plan-description" style="color: var(--text-secondary); font-size: 13px; line-height: 1.8; min-height: 70px;">
                    Para quienes desean llevar su entrenamiento al siguiente nivel.
                </p>
                <ul class="plan-features" style="list-style: none; text-align: left; margin: 30px 0;">
                    <li style="padding: 13px 0; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-primary);"><span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span> Todos los beneficios del Premium</li>
                    <li style="padding: 13px 0; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-primary);"><span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span> Asesoramiento personalizado</li>
                    <li style="padding: 13px 0; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-primary);"><span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span> Evaluación de progreso</li>
                    <li style="padding: 13px 0; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-primary);"><span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span> Atención prioritaria</li>
                    <li style="padding: 13px 0; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-primary);"><span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span> Planificación de entrenamiento</li>
                </ul>
                <a href="login.php" class="btn btn-secondary" style="width: 100%;">
                    ELEGIR PLAN
                </a>
            </article>

        </div>

        <p style="text-align:center; color: var(--text-muted); font-size: 11px; margin-top: 30px;">
            * Precios ilustrativos. Confirma las tarifas y condiciones vigentes directamente con GymManagerFit.
        </p>

    </section>

    <!-- BENEFICIOS -->
    <section class="highlight-section" style="background: var(--bg-secondary); padding: 90px 20px; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
        <div class="container" style="max-width: 1300px; margin: 0 auto;">
            <div class="highlight-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); align-items: center; gap: 65px;">
                <div class="highlight-image" style="position: relative;">
                    <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1000" alt="Instalaciones de gimnasio" style="width: 100%; height: 480px; object-fit: cover; border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
                </div>
                <div class="highlight-info">
                    <small style="color: var(--primary); font-weight: 800; letter-spacing: 3px; font-size: 12px; text-transform: uppercase;">ENTRENA CON NOSOTROS</small>
                    <h2 style="font-size: clamp(28px, 4vw, 40px); line-height: 1.2; margin: 20px 0; font-weight: 800;">
                        MÁS QUE UNA MEMBRESÍA, UNA NUEVA FORMA DE VIDA.
                    </h2>
                    <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.9; margin-bottom: 25px;">
                        En GymManagerFit queremos que cada entrenamiento sea una oportunidad para avanzar. Nuestras alternativas están pensadas para diferentes necesidades y objetivos.
                    </p>
                    <div class="feature-item" style="display: flex; gap: 15px; margin: 20px 0; align-items: center; font-size: 13.5px;">
                        <div class="feature-icon" style="min-width: 35px; height: 35px; background: rgba(220,38,38,.15); color: var(--primary); display: grid; place-items: center; font-weight: 900; border-radius: var(--radius-sm);">✓</div>
                        Diferentes opciones de entrenamiento.
                    </div>
                    <div class="feature-item" style="display: flex; gap: 15px; margin: 20px 0; align-items: center; font-size: 13.5px;">
                        <div class="feature-icon" style="min-width: 35px; height: 35px; background: rgba(220,38,38,.15); color: var(--primary); display: grid; place-items: center; font-weight: 900; border-radius: var(--radius-sm);">✓</div>
                        Equipamiento para trabajar tu condición física.
                    </div>
                    <div class="feature-item" style="display: flex; gap: 15px; margin: 20px 0; align-items: center; font-size: 13.5px;">
                        <div class="feature-icon" style="min-width: 35px; height: 35px; background: rgba(220,38,38,.15); color: var(--primary); display: grid; place-items: center; font-weight: 900; border-radius: var(--radius-sm);">✓</div>
                        Un entorno enfocado en la superación personal.
                    </div>
                    <a href="servicios.php" class="btn btn-primary" style="margin-top: 15px;">
                        DESCUBRIR SERVICIOS →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- PREGUNTAS FRECUENTES -->
    <section class="page-section" style="padding: 100px 20px; max-width: 1300px; margin: 0 auto;">
        <div class="container">
            <div class="section-heading" style="text-align: center; max-width: 750px; margin: 0 auto 60px;">
                <small style="color: var(--primary); font-size: 12px; font-weight: 800; letter-spacing: 4px; text-transform: uppercase;">RESOLVEMOS TUS DUDAS</small>
                <h2 style="font-size: clamp(30px, 4vw, 48px); font-weight: 800; text-transform: uppercase; margin: 18px 0;">PREGUNTAS FRECUENTES</h2>
                <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.9;">
                    Conoce algunos detalles importantes antes de elegir tu membresía.
                </p>
            </div>

            <div class="faq-container">
                <details class="faq-item">
                    <summary>¿Cómo puedo inscribirme?</summary>
                    <p>Puedes acercarte a nuestras instalaciones para conocer las opciones disponibles y completar tu inscripción de forma digital o presencial.</p>
                </details>
                <details class="faq-item">
                    <summary>¿Puedo cambiar de plan?</summary>
                    <p>Puedes consultar con nuestro personal las condiciones disponibles para cambiar de membresía en tu siguiente ciclo.</p>
                </details>
                <details class="faq-item">
                    <summary>¿Necesito experiencia para entrenar?</summary>
                    <p>No. Puedes comenzar desde cero. Lo importante es elegir una actividad adecuada a tu condición y objetivos.</p>
                </details>
                <details class="faq-item">
                    <summary>¿Qué métodos de pago aceptan?</summary>
                    <p>Consulta directamente con nuestro personal o en el sistema ERP los métodos de pago digitales y presenciales disponibles.</p>
                </details>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="page-cta" style="padding: 100px 20px; text-align: center; background: linear-gradient(rgba(9,13,22,.9), rgba(9,13,22,.9)), url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=1600'); background-size: cover; background-position: center; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
        <div class="container" style="max-width: 800px; margin: 0 auto;">
            <h2 style="font-size: clamp(26px, 4vw, 40px); font-weight: 800; margin-bottom: 15px;">EL MEJOR MOMENTO PARA COMENZAR ES AHORA</h2>
            <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 35px;">
                No esperes al momento perfecto. Empieza a trabajar por tu mejor versión.
            </p>
            <a href="login.php" class="btn btn-primary" style="padding: 14px 28px; font-size: 14px;">
                COMENZAR AHORA →
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

<style>
/* PREGUNTAS FRECUENTES ADAPTADAS AL TEMA ROJO */
.faq-container {
    max-width: 850px;
    margin: auto;
}

.faq-item {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    margin-bottom: 15px;
    padding: 23px 25px;
    transition: var(--transition);
}

.faq-item:hover {
    border-color: var(--primary);
}

.faq-item summary {
    cursor: pointer;
    font-size: 14px;
    font-weight: 800;
    list-style: none;
    position: relative;
    padding-right: 30px;
    color: var(--text-primary);
}

.faq-item summary::-webkit-details-marker {
    display: none;
}

.faq-item summary::after {
    content: "+";
    position: absolute;
    right: 0;
    color: var(--primary);
    font-size: 22px;
    top: -5px;
}

.faq-item[open] summary::after {
    content: "−";
}

.faq-item p {
    color: var(--text-secondary);
    font-size: 13px;
    line-height: 1.9;
    padding-top: 20px;
}
</style>

<script>
function toggleMenu() {
    document.getElementById("navLinks").classList.toggle("active");
}
</script>

</body>
</html>
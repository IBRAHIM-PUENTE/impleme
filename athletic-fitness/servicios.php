<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios | GymManagerFit</title>

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
<section class="inner-hero" style="background-image: linear-gradient(90deg, rgba(9,13,22,.97), rgba(15,23,42,.85)), url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=2000');">
    <div class="container" style="max-width: 1300px; margin: 0 auto; padding: 0 20px; width: 100%;">

        <span class="hero-label" style="color: var(--primary); font-weight: 800; letter-spacing: 4px; font-size: 12px; margin-bottom: 25px; text-transform: uppercase; display: inline-block;">
            ENTRENA SIN LÍMITES
        </span>

        <h1 style="font-size: clamp(40px, 6vw, 78px); font-weight: 800; line-height: 1.05; max-width: 850px; text-transform: uppercase; margin-bottom: 25px;">
            CONSTRUYE TU<br>
            <span style="color: var(--primary);">MEJOR VERSIÓN.</span>
        </h1>

        <p style="max-width: 580px; color: var(--text-secondary); font-size: 15px; line-height: 1.9; margin-bottom: 35px;">
            En GymManagerFit encontrarás todo lo que necesitas para transformar tu cuerpo, mejorar tu rendimiento y superar tus propios límites.
        </p>

        <div class="hero-buttons" style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="#servicios" class="btn btn-primary">
                DESCUBRIR SERVICIOS →
            </a>
            <a href="planes.php" class="btn btn-secondary">
                VER MEMBRESÍAS
            </a>
        </div>

        <div class="hero-breadcrumb" style="margin-top: 40px; color: var(--text-muted); font-size: 12px;">
            <a href="index.php" style="color: var(--text-primary); text-decoration: none;">Inicio</a>
            <span style="color: var(--primary); margin: 0 12px;">/</span>
            Servicios
        </div>

    </div>
</section>

<main class="page-content" style="background: var(--bg-primary); min-height: 80vh; padding-bottom: 90px;">

    <!-- INTRODUCCIÓN -->
    <section class="page-section" id="servicios" style="padding: 100px 20px; max-width: 1300px; margin: 0 auto;">
        
        <div class="section-heading" style="text-align: center; max-width: 750px; margin: 0 auto 60px;">
            <small style="color: var(--primary); font-size: 12px; font-weight: 800; letter-spacing: 4px; text-transform: uppercase;">ENTRENAMIENTO DE CALIDAD</small>
            <h2 style="font-size: clamp(30px, 4vw, 48px); font-weight: 800; text-transform: uppercase; margin: 18px 0;">ENTRENA A TU MANERA</h2>
            <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.9;">
                Disponemos de diferentes modalidades de entrenamiento para ayudarte a conseguir tus objetivos. Encuentra la actividad que mejor se adapte a ti.
            </p>
        </div>

        <!-- TARJETAS DE SERVICIOS -->
        <div class="services-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px;">

            <article class="service-card" style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden; transition: var(--transition);">
                <div class="service-image" style="position: relative; height: 250px; overflow: hidden;">
                    <img src="https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=900" alt="Musculación" style="width: 100%; height: 100%; object-fit: cover;">
                    <span class="service-number" style="position: absolute; bottom: 15px; left: 20px; font-size: 35px; font-weight: 900; color: rgba(255,255,255,.9);">01</span>
                </div>
                <div class="service-info" style="padding: 30px;">
                    <h3 style="font-size: 20px; margin-bottom: 12px; font-weight: 800;">Musculación</h3>
                    <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.8;">
                        Desarrolla fuerza, aumenta tu masa muscular y trabaja cada grupo muscular con ejercicios específicos y equipamiento especializado.
                    </p>
                </div>
            </article>

            <article class="service-card" style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden; transition: var(--transition);">
                <div class="service-image" style="position: relative; height: 250px; overflow: hidden;">
                    <img src="https://images.unsplash.com/photo-1534258936925-c58bed479fcb?w=900" alt="Cardiovascular" style="width: 100%; height: 100%; object-fit: cover;">
                    <span class="service-number" style="position: absolute; bottom: 15px; left: 20px; font-size: 35px; font-weight: 900; color: rgba(255,255,255,.9);">02</span>
                </div>
                <div class="service-info" style="padding: 30px;">
                    <h3 style="font-size: 20px; margin-bottom: 12px; font-weight: 800;">Cardiovascular</h3>
                    <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.8;">
                        Mejora tu resistencia, fortalece tu corazón y aumenta tu capacidad física mediante ejercicios cardiovasculares avanzados.
                    </p>
                </div>
            </article>

            <article class="service-card" style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden; transition: var(--transition);">
                <div class="service-image" style="position: relative; height: 250px; overflow: hidden;">
                    <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=900" alt="Entrenamiento funcional" style="width: 100%; height: 100%; object-fit: cover;">
                    <span class="service-number" style="position: absolute; bottom: 15px; left: 20px; font-size: 35px; font-weight: 900; color: rgba(255,255,255,.9);">03
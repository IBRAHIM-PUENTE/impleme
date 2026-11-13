
<?php
// Panel exclusivamente demostrativo.
// No representa una sesión autenticada.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
session_start();

if (
    !isset($_SESSION["autenticado"]) ||
    $_SESSION["autenticado"] !== true
) {
    header("Location: login.php");
    exit();
}
?>
    <title>Panel | Athletic Fitness</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #111;
            color: white;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #181818;
            padding: 30px 20px;
        }

        .sidebar h2 {
            margin-bottom: 40px;
        }

        .sidebar span {
            color: #ed2939;
        }

        .sidebar a {
            display: block;
            color: #ccc;
            text-decoration: none;
            padding: 16px 12px;
            margin-bottom: 10px;
        }

        .sidebar a:hover {
            background: #ed2939;
        }

        .main {
            flex: 1;
            padding: 35px;
        }

        .welcome {
            background: linear-gradient(120deg, #ed2939, #74121c);
            padding: 35px;
            border-radius: 8px;
            margin: 30px 0;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .card {
            background: #202020;
            padding: 30px;
            border: 1px solid #333;
        }

        .card h3 {
            color: #aaa;
            margin-bottom: 20px;
        }

        .card h2 {
            color: #ed2939;
        }

        .notice {
            margin-top: 25px;
            padding: 15px;
            background: #302516;
            color: #ffc66d;
            font-size: 13px;
        }

        @media(max-width: 750px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

            .cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <aside class="sidebar">

        <h2>ATHLETIC<span>FITNESS</span></h2>

        <a href="dashboard.php">🏠 Inicio</a>
        <a href="#">👥 Clientes</a>
        <a href="#">💳 Membresías</a>
        <a href="#">📊 Estadísticas</a>
        <a href="index.php">🌐 Página principal</a>
        <a href="logout.php">🚪 Salir</a>

    </aside>

    <main class="main">

        <h1>Panel administrativo</h1>

        <section class="welcome">

            <h2>¡Bienvenido a Athletic Fitness!</h2>

            <p>
                Has ingresado correctamente al panel
                de demostración del gimnasio.
            </p>

        </section>

        <section class="cards">

            <div class="card">
                <h3>Total de clientes</h3>
                <h2>0</h2>
            </div>

            <div class="card">
                <h3>Membresías activas</h3>
                <h2>0</h2>
            </div>

            <div class="card">
                <h3>Ingresos del mes</h3>
                <h2>S/ 0.00</h2>
            </div>

        </section>

        
    </main>

</body>
</html>

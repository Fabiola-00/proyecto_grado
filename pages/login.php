<?php
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: dashboard/inicio.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Credenciales hardcodeadas (admin / pass)
    if ($username === 'admin' && $password === 'pass') {
        // Inicio de sesión exitoso
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;

        header("Location: dashboard/inicio.php");
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos. Intente nuevamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SBRAB - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../icons/escudo.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style_login.css">
    <style>
        .error-message {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!-- Header simplificado -->
    <header class="header">
        <div class="header-container">
            <a href="../index.html"> <img src="../img/sbrab_escudo.png" alt="Escudo SBRAB" class="logo"></a>
        </div>
    </header>

    <!-- Main con formulario de login -->

    <main class="login-main">
        <div class="login-container">
            <div class="login-card">
                <div class="login-icon">
                    <img src="../icons/user_icon.png" alt="rescue" class="res">
                </div>
                <h2>SICOSE</h2>

                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form id="loginForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="input-group">
                        <label for="username">Usuario</label>
                        <input type="text" id="username" name="username" placeholder="Ingrese su usuario"
                            autocomplete="off" required value="<?php echo htmlspecialchars($username ?? ''); ?>">
                    </div>

                    <div class="input-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="Ingrese su contraseña"
                            autocomplete="off" required>
                    </div>

                    <button type="submit" class="login-btn">Ingresar</button>

                    <div class="login-footer">
                        <p>¿Problemas para ingresar? <a href="../pages/404.html">Contacte al administrador</a></p>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-column">
                <h3>Contacto</h3>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Av. Montes, La Paz</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>(591) 99999999</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>info@sbrab.bo</span>
                    </div>
                </div>
            </div>

            <div class="footer-column">
                <h3>Enlaces rápidos</h3>
                <ul>
                    <li>
                        <a href="#"><i class="fas fa-chevron-right"></i> Enlace 1</a>
                    </li>
                    <li>
                        <a href="#"><i class="fas fa-chevron-right"></i> Enlace 2</a>
                    </li>
                    <li>
                        <a href="#"><i class="fas fa-chevron-right"></i> Enlace 3</a>
                    </li>
                    <li>
                        <a href="#"><i class="fas fa-chevron-right"></i> Calendario</a>
                    </li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>Redes Sociales</h3>
                <p>Síguenos en nuestras redes:</p>
                <a href="https://www.facebook.com/profile.php?id=100064365202308" title="Facebook">
                    <i class="fab fa-facebook-f"></i></a>
                <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
            </div>
        </div>

        <div class="footer-column">
            <h3>Horarios de Instrucción</h3>
            <ul>
                <!-- <li><strong>Lunes a Viernes:</strong> 8:00 - 20:00</li> -->
                <li><strong>Sábados:</strong> 7:00 - 19:00</li>
            </ul>


        </div>
        </div>

        <div class="copyright">
            &copy; 2025 Servicio de Búsqueda y Rescate de la Armada Boliviana - Todos los derechos reservados
        </div>
    </footer>

</body>

</html>
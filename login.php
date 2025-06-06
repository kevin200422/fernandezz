<?php
require_once 'conexion.php'; // Asegúrate de que $pdo esté correctamente configurado
session_start();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rol = $_POST['rol'] ?? '';

    // Validación básica
    if (empty($email) || empty($password) || empty($rol)) {
        $error = 'Todos los campos son obligatorios';
    } else {
        try {
            // Preparar la consulta usando PDO
            $stmt = $pdo->prepare('SELECT * FROM pacientes WHERE email = :email LIMIT 1');
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];

                // Verificar rol
                if ($rol === 'medico') {
                    $error = 'No tienes acceso como médico. Verifica tu rol.';
                } else {
                    // Recordar usuario con una cookie
                    if (isset($_POST['remember']) && $_POST['remember'] === 'on') {
                        setcookie('user_email', $email, time() + (86400 * 30), "/"); // 30 días
                    }

                    // Redirección al dashboard
                    header('Location: paciente_dashboard.php');
                    exit;
                }
            } else {
                $error = 'Credenciales incorrectas. Verifique su correo o contraseña.';
            }
        } catch (Exception $e) {
            // Captura errores de la base de datos
            error_log("Error de base de datos: " . $e->getMessage());
            $error = 'Error interno. Por favor, intente más tarde.';
        }
    }
}

// Recuperar email si existe la cookie
$saved_email = $_COOKIE['user_email'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Médico - Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --background-color: #f0f9ff;
            --error-color: #dc2626;
            --text-color: #1f2937;
            --border-color: #e5e7eb;
        }

        body {
            background-color: var(--background-color);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            color: var(--text-color);
        }

        .login-container {
            max-width: 440px;
            width: 100%;
            margin: auto;
        }

        .login-card {
            background: white;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            color: var(--text-color);
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #6b7280;
            font-size: 0.975rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-color);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 2.5rem;
            cursor: pointer;
            color: #6b7280;
        }

        .btn-login {
            background-color: var(--primary-color);
            color: white;
            width: 100%;
            padding: 0.875rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-color: var(--secondary-color);
            transform: translateY(-1px);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin: 1rem 0;
            gap: 0.5rem;
        }

        .remember-me input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
        }

        .error-message {
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            color: var(--error-color);
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .role-selector {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .role-option {
            flex: 1;
            text-align: center;
        }

        .role-option input[type="radio"] {
            display: none;
        }

        .role-option label {
            display: block;
            padding: 1rem;
            border: 2px solid var(--border-color);
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .role-option input[type="radio"]:checked + label {
            border-color: var(--primary-color);
            background-color: #eff6ff;
            color: var(--primary-color);
        }

        .footer-links {
            text-align: center;
            margin-top: 1.5rem;
        }

        .footer-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.875rem;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 1.5rem;
            }
            
            .login-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Bienvenido</h1>
                <p>Ingresa tus credenciales para continuar</p>
            </div>
            
            <form method="POST" id="loginForm">
                <?php if (isset($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($saved_email); ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           required>
                    <span class="password-toggle" onclick="togglePassword()">
                        <i class="far fa-eye" id="toggleIcon"></i>
                    </span>
                </div>

                <div class="role-selector">
                    <div class="role-option">
                        <input type="radio" id="medico" name="rol" value="medico" required>
                        <label for="medico">
                            <i class="fas fa-user-md"></i>
                            <div>Médico</div>
                        </label>
                    </div>
                    <div class="role-option">
                        <input type="radio" id="paciente" name="rol" value="paciente" required>
                        <label for="paciente">
                            <i class="fas fa-user"></i>
                            <div>Paciente</div>
                        </label>
                    </div>
                </div>

                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Recordar mis datos</label>
                </div>

                <button type="submit" class="btn-login">
                    Iniciar Sesión
                </button>

                <div class="footer-links">
                    <a href="#">¿Olvidaste tu contraseña?</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'far fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'far fa-eye';
            }
        }

        // Validación del formulario
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const role = document.querySelector('input[name="rol"]:checked');
            
            if (!email || !password || !role) {
                e.preventDefault();
                alert('Por favor, complete todos los campos');
            }
        });
    </script>
</body>
</html>

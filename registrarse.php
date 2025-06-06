<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registro - Sistema de Salud</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <style>
    .form-container {
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      padding: 2rem;
      background-color: #fff;
    }
    .form-floating {
      margin-bottom: 1rem;
    }
    .password-requirements {
      font-size: 0.8rem;
      color: #6c757d;
    }
    .toggle-form {
      cursor: pointer;
      color: #198754;
      text-decoration: underline;
    }
    body {
      background-color: #f8f9fa;
    }
  </style>
</head>
<body>

  <?php include 'conexion.php'; ?>

  <div class="container mt-5 mb-5">
    <h1 class="text-center text-success mb-4">¡Bienvenido a Nuestro Sistema de Salud!</h1>

    <div class="text-center mb-4">
      <button class="btn btn-outline-success me-2" onclick="toggleForm('paciente')">Registro de Paciente</button>
      <button class="btn btn-outline-success" onclick="toggleForm('medico')">Registro de Médico</button>
    </div>

    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">

        <!-- Formulario de Paciente -->
        <div id="form-paciente" class="form-container">
          <h3 class="text-center mb-4">Registro de Paciente</h3>
          <form id="pacienteForm" action="procesar_registro.php" method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="rol" value="paciente">

            <div class="form-floating">
              <input type="text" class="form-control" id="nombrePaciente" name="nombre" placeholder="Nombre Completo" required pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]{2,100}">
              <label for="nombrePaciente">Nombre Completo</label>
              <div class="invalid-feedback">Por favor ingrese un nombre válido</div>
            </div>

            <div class="form-floating">
              <input type="email" class="form-control" id="emailPaciente" name="email" placeholder="nombre@ejemplo.com" required>
              <label for="emailPaciente">Correo Electrónico</label>
              <div class="invalid-feedback">Por favor ingrese un correo válido</div>
            </div>

            <div class="form-floating">
              <input type="password" class="form-control" id="passwordPaciente" name="password" placeholder="Contraseña" required pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$">
              <label for="passwordPaciente">Contraseña</label>
              <div class="password-requirements">La contraseña debe tener al menos 8 caracteres, incluyendo letras y números</div>
            </div>

            <div class="form-floating">
              <input type="tel" class="form-control" id="telefonoPaciente" name="telefono" placeholder="Teléfono" required pattern="[0-9]{10}">
              <label for="telefonoPaciente">Teléfono</label>
              <div class="invalid-feedback">Ingrese un número de teléfono válido (10 dígitos)</div>
            </div>

            <div class="form-floating">
              <textarea class="form-control" id="direccionPaciente" name="direccion" placeholder="Dirección" style="height: 100px" required></textarea>
              <label for="direccionPaciente">Dirección</label>
            </div>

            <div class="form-floating">
              <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" required>
              <label for="fechaNacimiento">Fecha de Nacimiento</label>
            </div>

            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="terminosPaciente" required>
              <label class="form-check-label" for="terminosPaciente">
                Acepto los términos y condiciones
              </label>
            </div>

            <button type="submit" class="btn btn-success w-100">Registrarme como Paciente</button>
          </form>
        </div>

        <!-- Formulario de Médico -->
        <div id="form-medico" class="form-container" style="display: none;">
          <h3 class="text-center mb-4">Registro de Médico</h3>
          <form id="medicoForm" action="procesar_registro.php" method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="rol" value="medico">

            <div class="form-floating">
              <input type="text" class="form-control" id="nombreMedico" name="nombre" placeholder="Nombre Completo" required pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]{2,100}">
              <label for="nombreMedico">Nombre Completo</label>
              <div class="invalid-feedback">Por favor ingrese un nombre válido</div>
            </div>

            <div class="form-floating">
              <input type="email" class="form-control" id="emailMedico" name="email" placeholder="nombre@ejemplo.com" required>
              <label for="emailMedico">Correo Electrónico</label>
              <div class="invalid-feedback">Por favor ingrese un correo válido</div>
            </div>

            <div class="form-floating">
              <input type="password" class="form-control" id="passwordMedico" name="password" placeholder="Contraseña" required pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$">
              <label for="passwordMedico">Contraseña</label>
              <div class="password-requirements">La contraseña debe tener al menos 8 caracteres, incluyendo letras y números</div>
            </div>

            <div class="form-floating">
              <input type="tel" class="form-control" id="telefonoMedico" name="telefono" placeholder="Teléfono" required pattern="[0-9]{10}">
              <label for="telefonoMedico">Teléfono</label>
              <div class="invalid-feedback">Ingrese un número de teléfono válido (10 dígitos)</div>
            </div>

            <div class="form-floating">
              <input type="text" class="form-control" id="especialidadMedico" name="especialidad" placeholder="Especialidad" required>
              <label for="especialidadMedico">Especialidad</label>
              <div class="invalid-feedback">Por favor ingrese una especialidad</div>
            </div>

            <button type="submit" class="btn btn-success w-100">Registrarme como Médico</button>
          </form>
        </div>

      </div>
    </div>
  </div>

  <script>
    function toggleForm(tipo) {
      if (tipo === 'paciente') {
        document.getElementById('form-paciente').style.display = 'block';
        document.getElementById('form-medico').style.display = 'none';
      } else {
        document.getElementById('form-paciente').style.display = 'none';
        document.getElementById('form-medico').style.display = 'block';
      }
    }

    // Validación de formularios Bootstrap
    (() => {
      'use strict'
      const forms = document.querySelectorAll('.needs-validation')
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }
          form.classList.add('was-validated')
        }, false)
      })
    })()
  </script>

</body>
</html>

<form action="agendar_cita.php" method="POST">
    <input type="hidden" name="id_medico" value="<?php echo $medico['id']; ?>">
    <div>
        <label for="fecha">Fecha:</label>
        <input type="date" id="fecha" name="fecha" required>
    </div>
    <div>
        <label for="hora">Hora:</label>
        <input type="time" id="hora" name="hora" required>
    </div>
    <div>
        <label for="paciente_nombre">Nombre del Paciente:</label>
        <input type="text" id="paciente_nombre" name="paciente_nombre" required>
    </div>
    <div>
        <label for="paciente_email">Email del Paciente:</label>
        <input type="email" id="paciente_email" name="paciente_email">
    </div>
    <div>
        <label for="descripcion">Descripción o Motivo:</label>
        <textarea id="descripcion" name="descripcion"></textarea>
    </div>
    <button type="submit">Agendar Cita</button>
</form>

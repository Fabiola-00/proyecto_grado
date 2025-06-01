const express = require('express');
const Database = require('better-sqlite3');
const cors = require('cors');

const app = express();
const db = new Database('data_vol.db');

app.use(cors());
app.use(express.json());
app.use(express.static('voluntarios'));


// Crear tabla si no existe
db.exec(`
    CREATE TABLE IF NOT EXISTS voluntarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        anio TEXT,
        nombres TEXT,
        apellidoPaterno TEXT,
        apellidoMaterno TEXT
    )
`);

// Obtener todos los voluntarios
app.get('/api/voluntarios', (req, res) => {
    const voluntarios = db.prepare('SELECT * FROM voluntarios').all();
    res.json(voluntarios);
});

// Crear nuevo voluntarios
app.post('/api/voluntarios', (req, res) => {
    const { anio, nombres, apellidoPaterno, apellidoMaterno } = req.body;
    const stmt = db.prepare('INSERT INTO voluntarios (anio, nombres, apellidoPaterno, apellidoMaterno) VALUES (?, ?, ?, ?)');
    const result = stmt.run(anio, nombres, apellidoPaterno, apellidoMaterno);
    res.json({ id: result.lastInsertRowid });
});

// Actualizar voluntarios
app.put('/api/voluntarios/:id', (req, res) => {
    const { anio, nombres, apellidoPaterno, apellidoMaterno } = req.body;
    const stmt = db.prepare('UPDATE voluntarios SET anio = ?, nombres = ?, apellidoPaterno = ?, apellidoMaterno = ? WHERE id = ?');
    stmt.run(anio, nombres, apellidoPaterno, apellidoMaterno, req.params.id);
    res.json({ success: true });
});

// Eliminar voluntarios
app.delete('/api/voluntarios/:id', (req, res) => {
    const stmt = db.prepare('DELETE FROM voluntarios WHERE id = ?');
    stmt.run(req.params.id);
    res.json({ success: true });
});


// Habilitamos el servidor para escuchar en el puerto 3000
const PORT = 3000;
app.listen(PORT, () => {
    console.log(`Servidor corriendo en http://localhost:${PORT}`);
});
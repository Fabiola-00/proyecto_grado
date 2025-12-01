<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SBRAB - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../icons/escudo.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/style_login.css">
    <link rel="stylesheet" href="../css/admin_styles.css">
</head>

<body>
    <header class="header">
        <div class="header-container">
            <a href="../index.html"><img src="../img/sbrab_escudo.png" alt="Escudo SBRAB" class="logo"></a>
        </div>
    </header>

    <main class="login-main">
        <div class="login-container">
            <div class="login-card">
                <div class="login-icon">
                    <img src="../icons/user_icon.png" alt="rescue" class="res">
                </div>
                <h2>SICOSE</h2>
                <form id="loginForm">
                    <div class="input-group">
                        <label for="username">Usuario</label>
                        <input type="text" id="username" name="username" placeholder="Ingrese su usuario" autocomplete="off" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" autocomplete="off" required>
                    </div>
                    <button type="submit" class="login-btn">Ingresar</button>
                    <div class="login-footer">
                        <p>Acceso para el: <a href="#" id="adminLink">Administrador</a></p>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-column">
                <h3>Contacto</h3>
                <div class="contact-info">
                    <div class="contact-item"><i class="fas fa-map-marker-alt"></i><span>Av. Montes, La Paz</span></div>
                    <div class="contact-item"><i class="fas fa-phone"></i><span>(591) 99999999</span></div>
                    <div class="contact-item"><i class="fas fa-envelope"></i><span>info@sbrab.bo</span></div>
                </div>
            </div>
            <div class="footer-column">
                <h3>Enlaces rápidos</h3>
                <ul>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Enlace 1</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Enlace 2</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Enlace 3</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Calendario</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Redes Sociales</h3>
                <p>Síguenos en nuestras redes:</p>
                <a href="https://www.facebook.com/profile.php?id=100064365202308" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
            </div>
            <div class="footer-column">
                <h3>Horarios de Instrucción</h3>
                <ul>
                    <li><strong>Sábados:</strong> 7:00 - 19:00</li>
                </ul>
            </div>
        </div>
        <div class="copyright">&copy; 2025 Servicio de Búsqueda y Rescate de la Armada Boliviana - Todos los derechos reservados</div>
    </footer>

    <!-- Modales -->
    <div id="adminPinModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Acceso de Administrador</h2>
                <button class="modal-close" onclick="closeAdminPinModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="adminPinForm" class="modal-form">
                    <div class="input-group">
                        <label for="adminPin">Ingrese PIN de 4 dígitos:</label>
                        <input type="password" id="adminPin" maxlength="4" class="pin-input" required autocomplete="off">
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn-modal btn-secondary" onclick="closeAdminPinModal()">Cancelar</button>
                        <button type="submit" class="btn-modal btn-primary">Verificar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="userManagementModal" class="modal-overlay">
        <div class="modal modal-large">
            <div class="modal-header">
                <h2>Gestión de Usuarios</h2>
                <button class="modal-close" onclick="closeUserManagementModal()">&times;</button>
            </div>
            <div class="modal-body">
                <button class="btn-add-user" onclick="showUserForm()">+ Agregar Usuario</button>
                <div id="userFormContainer" style="display: none; margin-bottom: 1.5rem;">
                    <form id="userForm" class="modal-form">
                        <input type="hidden" id="userId">
                        <div class="input-group"><label for="userUsername">Usuario:</label><input type="text" id="userUsername" required autocomplete="off"></div>
                        <div class="input-group"><label for="userPassword">Contraseña:</label><input type="password" id="userPassword" autocomplete="off"><small style="color: #666;">Dejar en blanco para mantener la contraseña actual (solo al editar)</small></div>
                        <div class="input-group"><label for="userNombre">Nombre:</label><input type="text" id="userNombre" required autocomplete="off"></div>
                        <div class="input-group"><label for="userApePat">Apellido Paterno:</label><input type="text" id="userApePat" required autocomplete="off"></div>
                        <div class="input-group"><label for="userApeMat">Apellido Materno:</label><input type="text" id="userApeMat" required autocomplete="off"></div>
                        <div class="input-group"><label for="userRol">Rol:</label><select id="userRol" required>
                                <option value="usuario">Usuario</option>
                                <option value="administrador">Administrador</option>
                            </select></div>
                        <div class="modal-actions">
                            <button type="button" class="btn-modal btn-secondary" onclick="cancelUserForm()">Cancelar</button>
                            <button type="submit" class="btn-modal btn-success">Guardar</button>
                        </div>
                    </form>
                </div>
                <table class="usuarios-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Nombre Completo</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usuariosTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const adminLink = document.getElementById('adminLink');

            loginForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const username = document.getElementById('username').value;
                const password = document.getElementById('password').value;

                try {
                    const response = await fetch('authenticate.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            username,
                            password
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        window.location.href = 'dashboard/inicio.php';
                    } else {
                        alert(data.message || 'Usuario o contraseña incorrectos');
                    }
                } catch (error) {
                    alert('Error al conectar con el servidor');
                }
            });

            adminLink.addEventListener('click', function(e) {
                e.preventDefault();
                openAdminPinModal();
            });
        });

        function openAdminPinModal() {
            document.getElementById('adminPinModal').classList.add('active');
            document.getElementById('adminPin').value = '';
            document.getElementById('adminPin').focus();
        }

        function closeAdminPinModal() {
            document.getElementById('adminPinModal').classList.remove('active');
        }

        document.getElementById('adminPinForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const pin = document.getElementById('adminPin').value;

            try {
                const response = await fetch('dashboard/pages/verify_admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        pin
                    })
                });

                const data = await response.json();

                if (data.success) {
                    closeAdminPinModal();
                    openUserManagementModal();
                } else {
                    alert(data.message || 'PIN incorrecto');
                    document.getElementById('adminPin').value = '';
                    document.getElementById('adminPin').focus();
                }
            } catch (error) {
                alert('Error al verificar PIN');
            }
        });

        async function openUserManagementModal() {
            document.getElementById('userManagementModal').classList.add('active');
            await loadUsuarios();
        }

        function closeUserManagementModal() {
            document.getElementById('userManagementModal').classList.remove('active');
            cancelUserForm();
        }

        async function loadUsuarios() {
            try {
                const response = await fetch('dashboard/pages/get_usuarios.php');
                const data = await response.json();

                if (data.success) {
                    const tbody = document.getElementById('usuariosTableBody');
                    tbody.innerHTML = '';

                    data.usuarios.forEach(user => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.user}</td>
                    <td>${user.Nombre} ${user.Ape_Pat} ${user.Ape_Mat}</td>
                    <td>${user.Rol}</td>
                    <td class="actions">
                        <button class="btn-icon btn-edit" onclick="editUser(${user.id})">Editar</button>
                        <button class="btn-icon btn-delete" onclick="deleteUser(${user.id})">Eliminar</button>
                    </td>
                `;
                        tbody.appendChild(tr);
                    });
                } else {
                    alert(data.message || 'Error al cargar usuarios');
                }
            } catch (error) {
                alert('Error al cargar usuarios');
            }
        }

        function showUserForm() {
            document.getElementById('userFormContainer').style.display = 'block';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('userPassword').required = true;
        }

        function cancelUserForm() {
            document.getElementById('userFormContainer').style.display = 'none';
            document.getElementById('userForm').reset();
        }

        async function editUser(id) {
            try {
                const response = await fetch('dashboard/pages/get_usuarios.php');
                const data = await response.json();

                if (data.success) {
                    const user = data.usuarios.find(u => u.id === id);
                    if (user) {
                        document.getElementById('userId').value = user.id;
                        document.getElementById('userUsername').value = user.user;
                        document.getElementById('userPassword').value = '';
                        document.getElementById('userPassword').required = false;
                        document.getElementById('userNombre').value = user.Nombre;
                        document.getElementById('userApePat').value = user.Ape_Pat;
                        document.getElementById('userApeMat').value = user.Ape_Mat;
                        document.getElementById('userRol').value = user.Rol;
                        document.getElementById('userFormContainer').style.display = 'block';
                    }
                }
            } catch (error) {
                alert('Error al cargar datos del usuario');
            }
        }

        async function deleteUser(id) {
            if (!confirm('¿Está seguro de eliminar este usuario?')) return;

            try {
                const response = await fetch('dashboard/pages/manage_usuario.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        id
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    await loadUsuarios();
                } else {
                    alert(data.message || 'Error al eliminar usuario');
                }
            } catch (error) {
                alert('Error al eliminar usuario');
            }
        }

        document.getElementById('userForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const userId = document.getElementById('userId').value;
            const action = userId ? 'update' : 'create';

            const userData = {
                action,
                user: document.getElementById('userUsername').value,
                pass: document.getElementById('userPassword').value,
                nombre: document.getElementById('userNombre').value,
                ape_pat: document.getElementById('userApePat').value,
                ape_mat: document.getElementById('userApeMat').value,
                rol: document.getElementById('userRol').value
            };

            if (userId) userData.id = userId;

            try {
                const response = await fetch('dashboard/pages/manage_usuario.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(userData)
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    cancelUserForm();
                    await loadUsuarios();
                } else {
                    alert(data.message || 'Error al guardar usuario');
                }
            } catch (error) {
                alert('Error al guardar usuario');
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAdminPinModal();
                closeUserManagementModal();
            }
        });
    </script>
</body>

</html>
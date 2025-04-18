// Загрузка списка пользователей при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
});

// Функция загрузки списка пользователей
function loadUsers() {
    fetch('actions/get_users.php')
        .then(response => response.json())
        .then(response => {
            const usersList = document.getElementById('usersList');
            
            if (!response.success) {
                usersList.innerHTML = `<div class="error-message">${response.message}</div>`;
                return;
            }

            const users = response.users;
            
            if (!users || users.length === 0) {
                usersList.innerHTML = '<p>Нет добавленных пользователей</p>';
                return;
            }

            let html = `
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя пользователя</th>
                            <th>Email</th>
                            <th>Роль</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            users.forEach(user => {
                html += `
                    <tr>
                        <td>${user.id}</td>
                        <td>${user.username}</td>
                        <td>${user.email}</td>
                        <td>${user.role === 'admin' ? 'Администратор' : 'Пользователь'}</td>
                        <td class="action-buttons">
                            <button class="edit-btn" onclick="editUser(${user.id})">Редактировать</button>
                            <button class="delete-btn" onclick="deleteUser(${user.id})">Удалить</button>
                        </td>
                    </tr>
                `;
            });

            html += `
                    </tbody>
                </table>
            `;

            usersList.innerHTML = html;
        })
        .catch(error => {
            console.error('Ошибка:', error);
            document.getElementById('usersList').innerHTML = 'Произошла ошибка при загрузке пользователей';
        });
}

// Показать форму добавления пользователя
function showAddUserForm() {
    document.getElementById('formTitle').textContent = 'Добавить пользователя';
    document.getElementById('userId').value = '';
    document.getElementById('addEditUserForm').reset();
    document.getElementById('userForm').style.display = 'block';
}

// Закрыть форму
function closeUserForm() {
    document.getElementById('userForm').style.display = 'none';
}

// Редактировать пользователя
function editUser(userId) {
    fetch(`actions/get_user.php?id=${userId}`)
        .then(response => response.json())
        .then(response => {
            if (!response.success) {
                throw new Error(response.message || 'Ошибка загрузки данных пользователя');
            }
            
            const user = response.user;
            document.getElementById('formTitle').textContent = 'Редактировать пользователя';
            document.getElementById('userId').value = user.id;
            document.getElementById('username').value = user.username;
            document.getElementById('email').value = user.email;
            document.getElementById('role').value = user.role;
            document.getElementById('userForm').style.display = 'block';
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert(error.message || 'Произошла ошибка при загрузке данных пользователя');
        });
}

// Сохранить пользователя
function saveUser(event) {
    event.preventDefault();
    
    const formData = new FormData(document.getElementById('addEditUserForm'));
    const userId = formData.get('userId');
    const url = userId ? 'actions/update_user.php' : 'actions/add_user.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(response => {
        if (!response.success) {
            throw new Error(response.message || 'Ошибка сохранения пользователя');
        }
        closeUserForm();
        loadUsers();
        alert(response.message || 'Пользователь успешно сохранен');
    })
    .catch(error => {
        console.error('Ошибка:', error);
        alert(error.message || 'Произошла ошибка при сохранении');
    });
}

// Удалить пользователя
function deleteUser(userId) {
    if (confirm('Вы уверены, что хотите удалить этого пользователя?')) {
        fetch('actions/delete_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${userId}`
        })
        .then(response => response.json())
        .then(response => {
            if (!response.success) {
                throw new Error(response.message || 'Ошибка удаления пользователя');
            }
            loadUsers();
            alert(response.message || 'Пользователь успешно удален');
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert(error.message || 'Произошла ошибка при удалении пользователя');
        });
    }
} 
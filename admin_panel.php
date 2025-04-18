<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        
        .admin-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        header {
            background-color: #2c3e50;
            padding: 15px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-radius: 8px;
        }
        
        header h1 {
            margin: 0;
            font-size: 1.8rem;
        }
        
        header nav {
            display: flex;
            gap: 15px;
        }
        
        header a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 20px;
            background-color: #3498db;
            transition: all 0.3s ease;
        }
        
        header a:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .nav-tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            gap: 5px;
        }
        
        .nav-tab {
            padding: 12px 24px;
            color: #333;
            text-decoration: none;
            border-radius: 8px 8px 0 0;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }
        
        .nav-tab:hover {
            background-color: #e9ecef;
        }
        
        .nav-tab.active {
            background-color: #3498db;
            color: white;
        }
        
        .controls {
            margin-bottom: 20px;
        }
        
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #3498db;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .edit-btn {
            background-color: #2ecc71;
        }
        
        .edit-btn:hover {
            background-color: #27ae60;
        }
        
        .delete-btn {
            background-color: #e74c3c;
        }
        
        .delete-btn:hover {
            background-color: #c0392b;
        }
        
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            position: relative;
        }
        
        .close {
            position: absolute;
            right: 15px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .users-table th,
        .users-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .users-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .users-table tr:hover {
            background-color: #f5f7fa;
        }
        
        .back-to-site {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #34495e;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.3s ease;
        }
        
        .back-to-site:hover {
            background-color: #2c3e50;
            transform: translateY(-2px);
        }
        
        .back-to-site i {
            font-size: 14px;
        }
        
        .form-hint {
            color: #666;
            font-size: 0.8em;
            margin-top: 4px;
            display: block;
        }

        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
        }

        .password-strength-meter {
            height: 4px;
            background-color: #ddd;
            margin-top: 8px;
            border-radius: 2px;
        }

        .strength-bar {
            height: 100%;
            width: 0;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .strength-weak { background-color: #ff4444; width: 33%; }
        .strength-medium { background-color: #ffbb33; width: 66%; }
        .strength-strong { background-color: #00C851; width: 100%; }

        .form-group input:invalid {
            border-color: #ff4444;
        }

        .form-group input:valid {
            border-color: #00C851;
        }

        .filter-controls {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .filter-controls input {
            min-width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .filter-controls button {
            margin-left: 5px;
        }

        .active-filter {
            background-color: #e3f2fd;
            padding: 5px 10px;
            border-radius: 4px;
            margin-top: 10px;
            display: none;
        }

        .active-filter span {
            font-weight: bold;
        }

        .videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px 0;
        }

        .video-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .video-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .video-card-header {
            padding: 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
        }

        .video-card-title {
            font-size: 1.1em;
            font-weight: 600;
            color: #2c3e50;
        }

        .video-card-body {
            padding: 15px;
        }

        .video-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.9em;
            color: #666;
        }

        .video-description {
            margin-bottom: 15px;
            font-size: 0.9em;
            color: #666;
        }

        .video-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }

        .video-tag {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .video-tag:hover {
            background: #bbdefb;
        }

        .video-actions {
            display: flex;
            gap: 10px;
        }

        .view-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .active-filter {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #e3f2fd;
            padding: 8px 12px;
            border-radius: 4px;
        }

        .clear-filter {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 0 4px;
        }

        .clear-filter:hover {
            color: #dc3545;
        }

        .no-videos {
            text-align: center;
            grid-column: 1/-1;
            padding: 40px;
            color: #666;
            background: #f8f9fa;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <header>
            <h1>Панель администратора</h1>
            <nav>
                <a href="index.php" class="back-to-site">
                    <i class="fas fa-arrow-left"></i>
                    Вернуться на сайт
                </a>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Выход
                </a>
            </nav>
        </header>
        
        <div class="nav-tabs">
            <a href="?admin=1&tab=colleges" class="nav-tab <?php echo $active_tab === 'colleges' ? 'active' : ''; ?>">
                <i class="fas fa-university"></i>
                Управление колледжами
            </a>
            <a href="?admin=1&tab=videos" class="nav-tab <?php echo $active_tab === 'videos' ? 'active' : ''; ?>">
                <i class="fas fa-video"></i>
                Управление видео
            </a>
            <a href="?admin=1&tab=users" class="nav-tab <?php echo $active_tab === 'users' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                Управление пользователями
            </a>
        </div>
        
        <main>
            <!-- Секция управления колледжами -->
            <div id="colleges-section" class="tab-content <?php echo $active_tab === 'colleges' ? 'active' : ''; ?>">
                <div class="controls">
                    <button onclick="showAddCollegeForm()">Добавить колледж</button>
                </div>
                
                <div id="collegesList">
                    <!-- Список колледжей будет загружен через AJAX -->
                </div>
                
                <!-- Форма добавления/редактирования колледжа -->
                <div id="collegeForm" class="modal" style="display: none;">
                    <div class="modal-content">
                        <span class="close" onclick="closeCollegeForm()">&times;</span>
                        <h2 id="collegeFormTitle">Добавить колледж</h2>
                        <form id="addEditCollegeForm" onsubmit="saveCollege(event)">
                            <input type="hidden" id="collegeId" name="collegeId">
                            <div class="form-group">
                                <label for="collegeName">Название колледжа:</label>
                                <input type="text" id="collegeName" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="collegeAddress">Адрес:</label>
                                <input type="text" id="collegeAddress" name="address">
                            </div>
                            <div class="form-group">
                                <label for="collegePhone">Телефон:</label>
                                <input type="tel" id="collegePhone" name="phone">
                            </div>
                            <div class="form-group">
                                <label for="collegeEmail">Email:</label>
                                <input type="email" id="collegeEmail" name="email">
                            </div>
                            <div class="form-group">
                                <label for="collegeDescription">Описание:</label>
                                <textarea id="collegeDescription" name="description" rows="4"></textarea>
                            </div>
                            <button type="submit">Сохранить</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Секция управления видео -->
            <div id="videos-section" class="tab-content <?php echo $active_tab === 'videos' ? 'active' : ''; ?>">
                <div class="controls">
                    <button onclick="showAddVideoForm()">Добавить видео</button>
                    <div class="filter-controls">
                        <input type="text" id="tagFilter" placeholder="Фильтр по тегам (через запятую)" style="padding: 8px; margin-right: 10px;">
                        <button onclick="filterVideos()">Применить фильтр</button>
                        <button onclick="clearFilter()">Сбросить</button>
                    </div>
                </div>
                
                <div id="videosList">
                    <!-- Список видео будет загружен через AJAX -->
                </div>
                
                <!-- Форма добавления/редактирования видео -->
                <div id="videoForm" class="modal" style="display: none;">
                    <div class="modal-content">
                        <span class="close" onclick="closeVideoForm()">&times;</span>
                        <h2 id="videoFormTitle">Добавить видео</h2>
                        <form id="addEditVideoForm" onsubmit="saveVideo(event)" enctype="multipart/form-data">
                            <input type="hidden" id="videoId" name="videoId">
                            <div class="form-group">
                                <label for="videoTitle">Заголовок видео:</label>
                                <input type="text" id="videoTitle" name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="videoDescription">Описание:</label>
                                <textarea id="videoDescription" name="description" rows="4"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="videoCollege">Учебное заведение:</label>
                                <select id="videoCollege" name="college_id" required>
                                    <option value="">-- Выберите колледж --</option>
                                    <!-- Список колледжей будет загружен через JavaScript -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="videoTags">Теги (через запятую):</label>
                                <input type="text" id="videoTags" name="tags" placeholder="Программирование, Дизайн, Инженерия">
                            </div>
                            <div class="form-group">
                                <label for="videoFile">Файл видео:</label>
                                <input type="file" id="videoFile" name="video_file" accept="video/*">
                                <small id="videoFileHint">(Оставьте пустым, если не хотите менять видео)</small>
                            </div>
                            <div class="form-group">
                                <label for="videoThumbnail">Миниатюра:</label>
                                <input type="file" id="videoThumbnail" name="thumbnail" accept="image/*">
                                <small>(Оставьте пустым, если не хотите менять миниатюру)</small>
                            </div>
                            <button type="submit">Сохранить</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Секция управления пользователями -->
            <div id="users-section" class="tab-content <?php echo $active_tab === 'users' ? 'active' : ''; ?>">
                <div class="controls">
                    <button onclick="showAddUserForm()">Добавить пользователя</button>
                </div>
                
                <div id="usersList">
                    <!-- Список пользователей будет загружен через AJAX -->
                </div>
                
                <!-- Форма добавления/редактирования пользователя -->
                <div id="userForm" class="modal" style="display: none;">
                    <div class="modal-content">
                        <span class="close" onclick="closeUserForm()">&times;</span>
                        <h2 id="userFormTitle">Добавить пользователя</h2>
                        <form id="addEditUserForm" onsubmit="saveUser(event)">
                            <input type="hidden" id="userId" name="userId">
                            <div class="form-group">
                                <label for="username">Имя пользователя:</label>
                                <input type="text" id="username" name="username" required 
                                    pattern="[A-Za-zА-Яа-я0-9_]{3,20}"
                                    title="От 3 до 20 символов, только буквы, цифры и подчеркивания">
                                <small class="form-hint">От 3 до 20 символов, только буквы, цифры и подчеркивания</small>
                            </div>
                            <div class="form-group">
                                <label for="userEmail">Email:</label>
                                <input type="email" id="userEmail" name="email" required
                                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                    title="Введите корректный email адрес">
                                <small class="form-hint">Например: user@example.com</small>
                            </div>
                            <div class="form-group">
                                <label for="userPassword">Пароль:</label>
                                <div class="password-container">
                                    <input type="password" id="userPassword" name="password" 
                                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                        title="Минимум 8 символов, включая цифры, строчные и заглавные буквы">
                                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength-meter">
                                    <div class="strength-bar"></div>
                                </div>
                                <small class="form-hint password-hint">Минимум 8 символов, включая цифры, строчные и заглавные буквы</small>
                                <small>(оставьте пустым, если не хотите менять)</small>
                            </div>
                            <div class="form-group">
                                <label for="userRole">Роль:</label>
                                <select id="userRole" name="role" required>
                                    <option value="user">Пользователь</option>
                                    <option value="admin">Администратор</option>
                                </select>
                            </div>
                            <button type="submit">Сохранить</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Загрузка данных при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            // Определяем активную вкладку
            const activeTab = '<?php echo $active_tab; ?>';
            
            if (activeTab === 'colleges') {
                loadColleges();
            } else if (activeTab === 'users') {
                loadUsers();
            } else if (activeTab === 'videos') {
                loadVideos();
            }
        });

        // УПРАВЛЕНИЕ КОЛЛЕДЖАМИ
        
        // Загрузка списка колледжей
        function loadColleges() {
            fetch('actions/get_colleges.php')
                .then(response => response.json())
                .then(data => {
                    const collegesList = document.getElementById('collegesList');
                    let html = '<div class="colleges-grid">';
                    
                    if (data.length > 0) {
                        data.forEach(college => {
                            // Временные данные для демонстрации (в будущем можно получать их с сервера)
                            const videoCount = Math.floor(Math.random() * 20) + 1; // Случайное число от 1 до 20
                            const tags = ['Программирование', 'Дизайн', 'Инженерия'].slice(0, Math.floor(Math.random() * 3) + 1);
                            
                            html += `
                                <div class="college-card" onclick="viewCollegeProfile(${college.id})">
                                    <div class="college-card-header">
                                        <div class="college-card-title">${college.name}</div>
                                    </div>
                                    <div class="college-card-body">
                                        <div class="college-stats">
                                            <div class="college-stats-item">
                                                <div class="college-stats-value">${videoCount}</div>
                                                <div class="college-stats-label">Видео</div>
                                            </div>
                                        </div>
                                        <div class="college-tags">
                                            ${tags.map(tag => `<span class="college-tag">${tag}</span>`).join('')}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        html += '<p style="grid-column: 1/-1; text-align: center;">Нет добавленных колледжей</p>';
                    }
                    
                    html += '</div>';
                    
                    // Добавляем кнопку переключения вида (сетка/таблица)
                    html = `
                        <div class="view-toggle">
                            <button onclick="toggleCollegesView('grid')" class="active">Карточки</button>
                            <button onclick="toggleCollegesView('table')">Таблица</button>
                        </div>
                        ${html}
                        <div id="collegesTable" style="display: none;">
                            <table class="users-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Название колледжа</th>
                                        <th>Email</th>
                                        <th>Телефон</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.length > 0 ? data.map(college => `
                                        <tr>
                                            <td>${college.id}</td>
                                            <td>${college.name}</td>
                                            <td>${college.email || '-'}</td>
                                            <td>${college.phone || '-'}</td>
                                            <td class="action-buttons">
                                                <button class="edit-btn" onclick="editCollege(${college.id})">Редактировать</button>
                                                <button class="delete-btn" onclick="deleteCollege(${college.id})">Удалить</button>
                                            </td>
                                        </tr>
                                    `).join('') : `
                                        <tr>
                                            <td colspan="5" style="text-align: center;">Нет добавленных колледжей</td>
                                        </tr>
                                    `}
                                </tbody>
                            </table>
                        </div>
                    `;
                    
                    collegesList.innerHTML = html;
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    document.getElementById('collegesList').innerHTML = 'Произошла ошибка при загрузке колледжей';
                });
        }
        
        // Переключение между видами отображения колледжей
        function toggleCollegesView(view) {
            const grid = document.querySelector('.colleges-grid');
            const table = document.getElementById('collegesTable');
            const buttons = document.querySelectorAll('.view-toggle button');
            
            if (view === 'grid') {
                grid.style.display = 'grid';
                table.style.display = 'none';
                buttons[0].classList.add('active');
                buttons[1].classList.remove('active');
            } else {
                grid.style.display = 'none';
                table.style.display = 'block';
                buttons[0].classList.remove('active');
                buttons[1].classList.add('active');
            }
        }
        
        // Просмотр профиля колледжа
        function viewCollegeProfile(collegeId) {
            // Здесь можно реализовать переход на страницу профиля колледжа
            // или показать модальное окно с детальной информацией
            fetch(`actions/get_college.php?id=${collegeId}`)
                .then(response => response.json())
                .then(college => {
                    // Временные данные для демонстрации
                    const videoCount = Math.floor(Math.random() * 20) + 1;
                    const tags = ['Программирование', 'Дизайн', 'Инженерия'].slice(0, Math.floor(Math.random() * 3) + 1);
                    
                    let html = `
                        <div class="modal-content college-profile">
                            <span class="close" onclick="closeCollegeProfile()">&times;</span>
                            <h2>${college.name}</h2>
                            <div class="college-profile-info">
                                <p><strong>Адрес:</strong> ${college.address || 'Не указан'}</p>
                                <p><strong>Телефон:</strong> ${college.phone || 'Не указан'}</p>
                                <p><strong>Email:</strong> ${college.email || 'Не указан'}</p>
                                <p><strong>Количество видео:</strong> ${videoCount}</p>
                                <div class="college-tags">
                                    ${tags.map(tag => `<span class="college-tag">${tag}</span>`).join('')}
                                </div>
                            </div>
                            <div class="college-profile-description">
                                <h3>Описание</h3>
                                <p>${college.description || 'Описание отсутствует'}</p>
                            </div>
                            <div class="college-profile-actions">
                                <button class="edit-btn" onclick="editCollege(${college.id}); closeCollegeProfile();">Редактировать</button>
                                <button class="delete-btn" onclick="if(confirm('Вы уверены, что хотите удалить этот колледж?')) { deleteCollege(${college.id}); closeCollegeProfile(); }">Удалить</button>
                            </div>
                        </div>
                    `;
                    
                    // Создаем и показываем модальное окно
                    const modal = document.createElement('div');
                    modal.id = 'collegeProfileModal';
                    modal.className = 'modal';
                    modal.style.display = 'block';
                    modal.innerHTML = html;
                    
                    document.body.appendChild(modal);
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при загрузке данных колледжа');
                });
        }
        
        // Закрыть профиль колледжа
        function closeCollegeProfile() {
            const modal = document.getElementById('collegeProfileModal');
            if (modal) {
                document.body.removeChild(modal);
            }
        }

        // Показать форму добавления колледжа
        function showAddCollegeForm() {
            document.getElementById('collegeFormTitle').textContent = 'Добавить колледж';
            document.getElementById('collegeId').value = '';
            document.getElementById('addEditCollegeForm').reset();
            document.getElementById('collegeForm').style.display = 'block';
        }

        // Закрыть форму колледжа
        function closeCollegeForm() {
            document.getElementById('collegeForm').style.display = 'none';
        }

        // Редактировать колледж
        function editCollege(collegeId) {
            fetch(`actions/get_college.php?id=${collegeId}`)
                .then(response => response.json())
                .then(college => {
                    document.getElementById('collegeFormTitle').textContent = 'Редактировать колледж';
                    document.getElementById('collegeId').value = college.id;
                    document.getElementById('collegeName').value = college.name;
                    document.getElementById('collegeAddress').value = college.address || '';
                    document.getElementById('collegePhone').value = college.phone || '';
                    document.getElementById('collegeEmail').value = college.email || '';
                    document.getElementById('collegeDescription').value = college.description || '';
                    document.getElementById('collegeForm').style.display = 'block';
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при загрузке данных колледжа');
                });
        }

        // Сохранить колледж
        function saveCollege(event) {
            event.preventDefault();
            
            const formData = new FormData(document.getElementById('addEditCollegeForm'));
            const collegeId = formData.get('collegeId');
            const url = collegeId ? 'actions/update_college.php' : 'actions/add_college.php';
            
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeCollegeForm();
                    loadColleges();
                    alert(data.message);
                } else {
                    alert(data.message || 'Произошла ошибка');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при сохранении');
            });
        }

        // Удалить колледж
        function deleteCollege(collegeId) {
            if (confirm('Вы уверены, что хотите удалить этот колледж?')) {
                fetch('actions/delete_college.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${collegeId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadColleges();
                        alert(data.message);
                    } else {
                        alert(data.message || 'Произошла ошибка при удалении');
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при удалении колледжа');
                });
            }
        }

        // УПРАВЛЕНИЕ ПОЛЬЗОВАТЕЛЯМИ
        
        // Загрузка списка пользователей
        function loadUsers() {
            fetch('actions/get_users.php')
                .then(response => response.json())
                .then(response => {
                    const usersList = document.getElementById('usersList');
                    
                    if (!response.success) {
                        usersList.innerHTML = `<div class="error-message">${response.message || 'Произошла ошибка при загрузке пользователей'}</div>`;
                        return;
                    }

                    const users = response.users;
                    
                    if (!users || users.length === 0) {
                        usersList.innerHTML = '<tr><td colspan="5" class="text-center">Нет добавленных пользователей</td></tr>';
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
                    document.getElementById('usersList').innerHTML = '<div class="error-message">Произошла ошибка при загрузке пользователей</div>';
                });
        }

        // Показать форму добавления пользователя
        function showAddUserForm() {
            document.getElementById('userFormTitle').textContent = 'Добавить пользователя';
            document.getElementById('userId').value = '';
            document.getElementById('addEditUserForm').reset();
            document.getElementById('userForm').style.display = 'block';
        }

        // Закрыть форму пользователя
        function closeUserForm() {
            document.getElementById('userForm').style.display = 'none';
        }

        // Редактировать пользователя
        function editUser(userId) {
            fetch(`actions/get_user.php?id=${userId}`)
                .then(response => response.json())
                .then(user => {
                    document.getElementById('userFormTitle').textContent = 'Редактировать пользователя';
                    document.getElementById('userId').value = user.id;
                    document.getElementById('username').value = user.username;
                    document.getElementById('userEmail').value = user.email;
                    document.getElementById('userRole').value = user.role;
                    document.getElementById('userForm').style.display = 'block';
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при загрузке данных пользователя');
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
            .then(data => {
                if (data.success) {
                    closeUserForm();
                    loadUsers();
                    alert(data.message);
                } else {
                    alert(data.message || 'Произошла ошибка');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при сохранении');
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
                .then(data => {
                    if (data.success) {
                        loadUsers();
                        alert(data.message);
                    } else {
                        alert(data.message || 'Произошла ошибка при удалении');
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при удалении пользователя');
                });
            }
        }

        // УПРАВЛЕНИЕ ВИДЕО
        
        // Глобальная переменная для хранения всех видео
        let allVideos = [];

        // Загрузка списка видео с учетом фильтрации
        function loadVideos(filterTags = []) {
            fetch('actions/get_videos.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Loaded videos:', data); // Отладочный вывод
                    if (data.success && Array.isArray(data.videos)) {
                        allVideos = data.videos;
                        filterVideos();
                    } else {
                        throw new Error(data.message || 'Ошибка загрузки видео');
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    const videosList = document.getElementById('videosList');
                    if (videosList) {
                        videosList.innerHTML = 'Произошла ошибка при загрузке видео: ' + error.message;
                    }
                });
        }

        // Отображение видео
        function displayVideos(videos) {
            const videosList = document.getElementById('videosList');
            let html = '<div class="videos-grid">';
            
            if (videos.length > 0) {
                videos.forEach(video => {
                    html += `
                        <div class="video-card">
                            <div class="video-card-header">
                                <div class="video-card-title">${video.title}</div>
                            </div>
                            <div class="video-card-body">
                                <div class="video-info">
                                    <div class="college-name">
                                        <i class="fas fa-university"></i> ${video.college_name || 'Колледж не указан'}
                                    </div>
                                    <div class="video-date">
                                        <i class="far fa-calendar-alt"></i> ${formatDate(video.created_at)}
                                    </div>
                                </div>
                                <div class="video-description">${video.description || ''}</div>
                                <div class="video-tags">
                                    ${video.tags ? video.tags.split(',').map(tag => {
                                        tag = tag.trim();
                                        return tag ? `
                                            <span class="video-tag" onclick="filterByTag('${tag}', event)">
                                                ${tag}
                                            </span>
                                        ` : '';
                                    }).join('') : ''}
                                </div>
                                <div class="video-actions">
                                    <button class="edit-btn" onclick="editVideo(${video.id})">
                                        <i class="fas fa-edit"></i> Редактировать
                                    </button>
                                    <button class="delete-btn" onclick="deleteVideo(${video.id})">
                                        <i class="fas fa-trash"></i> Удалить
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                html += '<p class="no-videos">Нет видео, соответствующих фильтру</p>';
            }
            
            html += '</div>';
            
            // Добавляем кнопку переключения вида и активный фильтр
            html = `
                <div class="view-controls">
                    <div class="view-toggle">
                        <button onclick="toggleVideosView('grid')" class="active">
                            <i class="fas fa-th"></i> Карточки
                        </button>
                        <button onclick="toggleVideosView('table')">
                            <i class="fas fa-list"></i> Таблица
                        </button>
                    </div>
                    <div id="activeFilter" class="active-filter" style="display: none;">
                        <span>Активный фильтр: <strong id="currentFilter"></strong></span>
                        <button onclick="clearFilter()" class="clear-filter">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                ${html}
                <div id="videosTable" style="display: none;">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название видео</th>
                                <th>Колледж</th>
                                <th>Теги</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${videos.length > 0 ? videos.map(video => `
                                <tr>
                                    <td>${video.id}</td>
                                    <td>${video.title}</td>
                                    <td>${video.college_name || '-'}</td>
                                    <td>
                                        ${video.tags ? video.tags.split(',').map(tag => {
                                            tag = tag.trim();
                                            return tag ? `
                                                <span class="video-tag" onclick="filterByTag('${tag}', event)">
                                                    ${tag}
                                                </span>
                                            ` : '';
                                        }).join('') : '-'}
                                    </td>
                                    <td class="action-buttons">
                                        <button class="edit-btn" onclick="editVideo(${video.id})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="delete-btn" onclick="deleteVideo(${video.id})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `).join('') : `
                                <tr>
                                    <td colspan="5" class="no-videos">Нет видео, соответствующих фильтру</td>
                                </tr>
                            `}
                        </tbody>
                    </table>
                </div>
            `;
            
            videosList.innerHTML = html;
        }

        // Фильтрация по клику на тег
        function filterByTag(tag, event) {
            event.preventDefault();
            const tagInput = document.getElementById('tagFilter');
            tagInput.value = tag;
            filterVideos();
        }

        // Фильтрация видео по тегам
        function filterVideos() {
            const tagInput = document.getElementById('tagFilter');
            if (!tagInput) return; // Проверяем существование элемента
            
            const tags = tagInput.value.split(',').map(tag => tag.trim()).filter(tag => tag.length > 0);
            const activeFilter = document.getElementById('activeFilter');
            
            if (activeFilter) { // Проверяем существование элемента
                if (tags.length > 0) {
                    activeFilter.style.display = 'flex';
                    const currentFilter = document.getElementById('currentFilter');
                    if (currentFilter) {
                        currentFilter.textContent = tags.join(', ');
                    }
                } else {
                    activeFilter.style.display = 'none';
                }
            }
            
            let filteredVideos = allVideos || [];
            if (tags.length > 0) {
                filteredVideos = allVideos.filter(video => {
                    if (!video.tags) return false;
                    const videoTags = video.tags.toLowerCase().split(',').map(tag => tag.trim());
                    return tags.some(filterTag => 
                        videoTags.includes(filterTag.toLowerCase())
                    );
                });
            }
            
            displayVideos(filteredVideos);
        }

        // Очистка фильтра
        function clearFilter() {
            const tagInput = document.getElementById('tagFilter');
            tagInput.value = '';
            filterVideos();
        }

        // Форматирование даты
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('ru-RU', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        // Переключение между видами отображения видео
        function toggleVideosView(view) {
            const grid = document.querySelector('.videos-grid');
            const table = document.getElementById('videosTable');
            const buttons = document.querySelectorAll('.view-toggle button');
            
            if (view === 'grid') {
                grid.style.display = 'grid';
                table.style.display = 'none';
                buttons[0].classList.add('active');
                buttons[1].classList.remove('active');
            } else {
                grid.style.display = 'none';
                table.style.display = 'block';
                buttons[0].classList.remove('active');
                buttons[1].classList.add('active');
            }
        }
        
        // Просмотр профиля видео
        function viewVideoProfile(videoId) {
            // Здесь можно реализовать переход на страницу профиля видео
            // или показать модальное окно с детальной информацией
            fetch(`actions/get_video.php?id=${videoId}`)
                .then(response => response.json())
                .then(video => {
                    // Временные данные для демонстрации
                    const videoCount = Math.floor(Math.random() * 20) + 1;
                    const tags = ['Программирование', 'Дизайн', 'Инженерия'].slice(0, Math.floor(Math.random() * 3) + 1);
                    
                    let html = `
                        <div class="modal-content video-profile">
                            <span class="close" onclick="closeVideoProfile()">&times;</span>
                            <h2>${video.title}</h2>
                            <div class="video-profile-info">
                                <p><strong>Колледж:</strong> ${video.college_name || 'Не указан'}</p>
                                <p><strong>Теги:</strong> ${video.tags || '-'}</p>
                                <p><strong>Просмотров:</strong> ${videoCount}</p>
                            </div>
                            <div class="video-profile-description">
                                <h3>Описание</h3>
                                <p>${video.description || 'Описание отсутствует'}</p>
                            </div>
                            <div class="video-profile-actions">
                                <button class="edit-btn" onclick="editVideo(${video.id}); closeVideoProfile();">Редактировать</button>
                                <button class="delete-btn" onclick="if(confirm('Вы уверены, что хотите удалить это видео?')) { deleteVideo(${video.id}); closeVideoProfile(); }">Удалить</button>
                            </div>
                        </div>
                    `;
                    
                    // Создаем и показываем модальное окно
                    const modal = document.createElement('div');
                    modal.id = 'videoProfileModal';
                    modal.className = 'modal';
                    modal.style.display = 'block';
                    modal.innerHTML = html;
                    
                    document.body.appendChild(modal);
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при загрузке данных видео');
                });
        }
        
        // Закрыть профиль видео
        function closeVideoProfile() {
            const modal = document.getElementById('videoProfileModal');
            if (modal) {
                document.body.removeChild(modal);
            }
        }

        // Показать форму добавления видео
        function showAddVideoForm() {
            document.getElementById('videoFormTitle').textContent = 'Добавить видео';
            document.getElementById('videoId').value = '';
            document.getElementById('addEditVideoForm').reset();
            document.getElementById('videoFileHint').textContent = '(Выберите файл видео)';
            document.getElementById('videoFile').required = true;
            loadCollegesForSelect();
            document.getElementById('videoForm').style.display = 'block';
        }

        // Закрыть форму видео
        function closeVideoForm() {
            document.getElementById('videoForm').style.display = 'none';
        }

        // Редактировать видео
        function editVideo(videoId) {
            fetch(`actions/get_video.php?id=${videoId}`)
                .then(response => response.json())
                .then(video => {
                    document.getElementById('videoFormTitle').textContent = 'Редактировать видео';
                    document.getElementById('videoId').value = video.id;
                    document.getElementById('videoTitle').value = video.title;
                    document.getElementById('videoDescription').value = video.description || '';
                    loadCollegesForSelect(video.college_id);
                    document.getElementById('videoTags').value = video.tags || '';
                    document.getElementById('videoFileHint').textContent = '(Оставьте пустым, если не хотите менять видео)';
                    document.getElementById('videoFile').required = false;
                    document.getElementById('videoForm').style.display = 'block';
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при загрузке данных видео');
                });
        }

        // Сохранить видео
        function saveVideo(event) {
            event.preventDefault();
            
            const formData = new FormData(document.getElementById('addEditVideoForm'));
            const videoId = formData.get('videoId');
            const url = videoId ? 'actions/update_video.php' : 'actions/add_video.php';
            
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeVideoForm();
                    loadVideos();
                    alert(data.message);
                } else {
                    alert(data.message || 'Произошла ошибка');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при сохранении');
            });
        }

        // Удалить видео
        function deleteVideo(videoId) {
            if (confirm('Вы уверены, что хотите удалить это видео?')) {
                fetch('actions/delete_video.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${videoId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadVideos();
                        alert(data.message);
                    } else {
                        alert(data.message || 'Произошла ошибка при удалении');
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при удалении видео');
                });
            }
        }

        // Загрузка списка колледжей для select
        function loadCollegesForSelect(selectedCollegeId = null) {
            fetch('actions/get_colleges.php')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('videoCollege');
                    select.innerHTML = '<option value="">-- Выберите колледж --</option>';
                    
                    if (data.length > 0) {
                        data.forEach(college => {
                            const option = document.createElement('option');
                            option.value = college.id;
                            option.textContent = college.name;
                            
                            if (selectedCollegeId && college.id == selectedCollegeId) {
                                option.selected = true;
                            }
                            
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при загрузке списка колледжей');
                });
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('userPassword');
            const toggleButton = document.querySelector('.toggle-password i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.classList.remove('fa-eye');
                toggleButton.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleButton.classList.remove('fa-eye-slash');
                toggleButton.classList.add('fa-eye');
            }
        }

        function checkPasswordStrength(password) {
            const strengthBar = document.querySelector('.strength-bar');
            
            if (!password) {
                strengthBar.className = 'strength-bar';
                return;
            }

            const hasLower = /[a-z]/.test(password);
            const hasUpper = /[A-Z]/.test(password);
            const hasNumber = /\d/.test(password);
            const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
            const length = password.length;

            let strength = 0;
            strength += hasLower ? 1 : 0;
            strength += hasUpper ? 1 : 0;
            strength += hasNumber ? 1 : 0;
            strength += hasSpecial ? 1 : 0;
            strength += length >= 8 ? 1 : 0;

            strengthBar.className = 'strength-bar';
            if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
            } else if (strength <= 3) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        }

        document.getElementById('userPassword').addEventListener('input', function(e) {
            checkPasswordStrength(e.target.value);
        });
    </script>
</body>
</html> 
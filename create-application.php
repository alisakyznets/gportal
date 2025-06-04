<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php', 'Необходимо авторизоваться');
}

// Получение всех категорий
$categories = $pdo->query("SELECT * FROM category")->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category_id = (int)$_POST['category_id'];
    
    // Валидация данных
    if (empty($title)) {
        $errors['title'] = "Введите название заявки";
    } elseif (strlen($title) > 50) {
        $errors['title'] = "Название не должно превышать 50 символов";
    }
    
    if (empty($description)) {
        $errors['description'] = "Введите описание проблемы";
    }
    
    // Обработка загрузки изображения
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $allowed_types = ['image/jpeg', 'image/png'];
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($file_info, $_FILES['image']['tmp_name']);
        
        if (!in_array($mime_type, $allowed_types)) {
            $errors['image'] = "Допустимы только изображения JPG и PNG";
        } else {
            $extension = $mime_type === 'image/jpeg' ? '.jpg' : '.png';
            $filename = uniqid('img_') . $extension;
            $destination = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image_path = 'uploads/' . $filename;
            } else {
                $errors['image'] = "Ошибка при загрузке файла";
            }
        }
    } else {
        $errors['image'] = "Загрузите фотографию проблемы";
    }
    
    // Если ошибок нет - сохраняем заявку
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO applications 
            (user_id, title, description, path_to_image_before, category_id, status_id) 
            VALUES (?, ?, ?, ?, ?, 3)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $title,
            $description,
            $image_path,
            $category_id
        ]);
        
        redirect('profile.php', 'Заявка успешно создана!');
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создать заявку</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    
    <div class="container">
        <h1>Создать новую заявку</h1>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Название проблемы:</label>
                <input type="text" name="title" value="<?= isset($title) ? escape($title) : '' ?>" required>
                <?= isset($errors['title']) ? '<span class="error">' . escape($errors['title']) . '</span>' : '' ?>
            </div>
            
            <div class="form-group">
                <label>Описание:</label>
                <textarea name="description" rows="5" required><?= isset($description) ? escape($description) : '' ?></textarea>
                <?= isset($errors['description']) ? '<span class="error">' . escape($errors['description']) . '</span>' : '' ?>
            </div>
            
            <div class="form-group">
                <label>Категория:</label>
                <select name="category_id" required>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['category_id'] ?>"><?= escape($category['category']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Фотография проблемы:</label>
                <input type="file" name="image" accept="image/jpeg,image/png" required>
                <?= isset($errors['image']) ? '<span class="error">' . escape($errors['image']) . '</span>' : '' ?>
            </div>
            
            <button type="submit" class="btn">Отправить заявку</button>
        </form>
    </div>
</body>
</html>
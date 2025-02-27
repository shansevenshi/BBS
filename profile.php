<?php  
session_start();  
include 'configs/config.php'; // 包含数据库连接文件  

// 检查用户是否已登录  
if (!isset($_SESSION['user_id'])) {  
    header("Location: index.php"); // 如果未登录，则重定向到主页  
    exit();  
}  

$user_id = $_SESSION['user_id'];  
$message = "";  

// 获取用户信息  
$stmt = $conn->prepare("SELECT username, name, password, img FROM users WHERE id = ?");  
$stmt->bind_param("i", $user_id);  
$stmt->execute();  
$result = $stmt->get_result();  
$user = $result->fetch_assoc();  
$stmt->close();  

// 处理上传头像  
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['avatar'])) {  
    $avatar = $_FILES['avatar'];  
    
    // 确保文件上传没有错误  
    if ($avatar['error'] === 0) {  
        $target_dir = "uploads/"; // 确保这个目录存在并可写  
        $target_file = $target_dir . basename($avatar["name"]);  
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));  

        // 检查文件类型  
        if (in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {  
            if (move_uploaded_file($avatar["tmp_name"], $target_file)) {  
                // 更新数据库以保存头像路径  
                $stmt = $conn->prepare("UPDATE users SET img = ? WHERE id = ?");  
                $stmt->bind_param("si", $target_file, $user_id);  
                if ($stmt->execute()) {  
                    $message = "头像更新成功!";  
                } else {  
                    $message = "数据库更新错误: " . $stmt->error;  
                }  
                $stmt->close();  
            } else {  
                $message = "上传文件时出错.";  
            }  
        } else {  
            $message = "只允许上传 JPG, JPEG, PNG 和 GIF 文件.";  
        }  
    } else {  
        $message = "文件上传出错.";  
    }  
}  

// 处理用户信息更新  
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_info'])) {  
    $username = $_POST['username'];  
    $name = $_POST['name'];  
    $current_password = $_POST['current_password'];  
    $new_password = $_POST['new_password'];  
    $confirm_password = $_POST['confirm_password'];  

    // 验证输入  
    if (!empty($username) && !empty($name)) {  
        // 检查当前密码是否正确  
        if (password_verify($current_password, $user['password'])) {  
            // 检查新密码和确认密码是否匹配  
            if ($new_password === $confirm_password) {  
                // 更新用户信息  
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // 哈希处理新密码  
                $stmt = $conn->prepare("UPDATE users SET username = ?, name = ?, password = ? WHERE id = ?");  
                $stmt->bind_param("sssi", $username, $name, $hashed_password, $user_id);  

                if ($stmt->execute()) {  
                    $message = "用户信息更新成功!";  
                    $_SESSION['username'] = $username; // 更新会话中的用户名  
                } else {  
                    $message = "更新用户信息时出错: " . $stmt->error;  
                }  
                $stmt->close();  
            } else {  
                $message = "新密码和确认密码不匹配!";  
            }  
        } else {  
            $message = "当前密码不正确!";  
        }  
    } else {  
        $message = "请填写所有字段!";  
    }  
}  
?>  

<!DOCTYPE html>  
<html lang="zh">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>个人资料</title>  
    <link rel="stylesheet" href="static/profile.css"> <!-- 引入样式表 -->  
</head>  
<body>  
    <div class="container">  
        <h1>个人信息</h1>  
        <div class="message"><?php echo $message; ?></div>  

        <h2>用户名: <?php echo htmlspecialchars($user['username']); ?></h2>  
        <h3>姓名: <?php echo htmlspecialchars($user['name']); ?></h3>  
        <img src="<?php echo htmlspecialchars($user['img'] ?: 'default-avatar.png'); ?>" alt="头像" width="100">  

        <h3>更换头像</h3>  
        <form method="post" enctype="multipart/form-data">  
            <input type="file" name="avatar" accept="image/*" required>  
            <input type="submit" value="上传头像">  
        </form>  

        <h3>更改用户信息</h3>  
        <form method="post">  
            <label for="username">用户名:</label>  
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>  
            
            <label for="name">姓名:</label>  
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>  
            
            <label for="current_password">当前密码:</label>  
            <input type="password" name="current_password" id="current_password" required>  
            
            <label for="new_password">新密码:</label>  
            <input type="password" name="new_password" id="new_password" required>  
            
            <label for="confirm_password">确认密码:</label>  
            <input type="password" name="confirm_password" id="confirm_password" required>  
            
            <input type="submit" name="update_info" value="更新信息">  
        </form>  

        <a href="index.php">返回主页</a>  
    </div>  
</body>  
</html>
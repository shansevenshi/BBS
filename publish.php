<?php  
session_start();
include './logincheck.php';
nologin();
include 'configs/config.php';  
$message = "";  
if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    if (isset($_SESSION['user_id'])) {  
        $title = $_POST['title'];  
        $body = $_POST['body'];  
        $user_id = $_SESSION['user_id'];  
        $level = $_POST['level'];  

        $time = date('Y-m-d H:i:s');  // 确保时间格式正确  
        // 使用占位符  
        $level=0;
        $sql = "INSERT INTO article (user_id, title, body, level, time) VALUES (?, ?, ?, ?, ?)";  
        $stmt = $conn->prepare($sql);  
        
        // 确保绑定参数的数量和类型与 SQL 语句匹配  
        // user_id (INT), title (STRING), body (STRING), level (INT), time (STRING)  
        $time = date('Y-m-d H:i:s');  

        $level = 0;
        $sql = "INSERT INTO article (user_id, title, body, level, time) VALUES (?, ?, ?, ?, ?)";  
        $stmt = $conn->prepare($sql);  
        $stmt->bind_param("issis", $user_id, $title, $body, $level, $time);  

        if ($stmt->execute()) {  
            $message = "文章发布成功！";  
        } else {  
            $message = "发布失败: " . $stmt->error;  
        }  
        $stmt->close();  
    } else {  
        $message = "请先登录！";  
    }  
}
?>  

<!DOCTYPE html>  
<html lang="zh">  
<head>  
    <meta charset="UTF-8">  
    <title>发表文章</title>  
    <link rel="stylesheet" href="static/style.css">  

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #4A90E2;
            margin-bottom: 20px;
            text-align: center;
            font-size: 2.5em;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px; /* 增加最大宽度 */
            display: flex;
            flex-direction: column;
            align-items: center; /* 居中对齐 */
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus, textarea:focus {
            border-color: #4A90E2;
            outline: none;
        }

        textarea {
            height: 200px; /* 调整高度以容纳更多文字 */
            resize: none; /* 禁止改变大小 */
        }

        input[type="submit"] {
            background-color: #4A90E2;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: #E94E77;
        }

        .message {
            margin-top: 20px;
            font-size: 1.2em;
            color: #333;
            text-align: center;
        }

        a {
            margin-top: 20px;
            text-decoration: none;
            color: #4A90E2;
            font-size: 1em;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>  
<body>  
    <h1>发表文章</h1>  
    <form method="post">  
        <input type="text" name="title" placeholder="标题" required>  
        <textarea name="body" placeholder="内容" required></textarea>  
        <input type="submit" value="发表">  
    </form>  
    <div class="message"><?php echo $message; ?></div>  
    <a href="index.php">返回主页</a>  
</body>  
</html>
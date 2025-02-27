<?php  
session_start();  
include 'configs/config.php'; // 包含数据库连接文件  

// 检查用户是否已登录  
if (!isset($_SESSION['user_id'])) {  
    header("Location: index.php"); // 如果未登录，则重定向到主页  
    exit();  
}  

$user_id = $_SESSION['user_id'];  
$articles = [];  

// 获取用户的文章  
$stmt = $conn->prepare("SELECT title, body, time FROM article WHERE user_id = ? ORDER BY time DESC");  
$stmt->bind_param("i", $user_id);  
$stmt->execute();  
$result = $stmt->get_result();  

while ($row = $result->fetch_assoc()) {  
    $articles[] = $row; // 收集所有文章  
}  

$stmt->close();  
?>  

<!DOCTYPE html>  
<html lang="zh">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>我的文章</title>  
    <link rel="stylesheet" href="static/articles.css"> <!-- 引入样式表 -->  
</head>  
<body>  
    <header>  
        <h1>我的文章</h1>  
    </header>  

    <main>  
        <?php if (count($articles) > 0): ?>  
            <?php foreach ($articles as $article): ?>  
                <div class="article">  
                    <h2><?php echo htmlspecialchars($article['title']); ?></h2>  
                    <time><?php echo date("Y-m-d H:i:s", strtotime($article['time'])); ?></time>  
                    <p><?php echo nl2br(htmlspecialchars($article['body'])); ?></p> <!-- 显示文章内容 -->  
                </div>  
            <?php endforeach; ?>  
        <?php else: ?>  
            <p>没有找到文章。</p>  
        <?php endif; ?>  
    </main>  

    <footer>  
        <p>© 2025 BBS 论坛</p>  
        <a href="index.php">返回主页</a>  
    </footer>  
</body>  
</html>
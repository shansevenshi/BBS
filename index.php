<?php  
session_start();  
include 'configs/config.php'; // 包含数据库连接文件  

// 检查用户是否已登录  
if (isset($_SESSION['user_id'])) {  
    $user_id = $_SESSION['user_id'];  

    // 获取用户信息  
    $stmt = $conn->prepare("SELECT username, img FROM users WHERE id = ?");  
    $stmt->bind_param("i", $user_id);  
    $stmt->execute();  
    $result = $stmt->get_result();  
    $user = $result->fetch_assoc();  
    $stmt->close();  
}  
?>  

<!DOCTYPE html>  
<html lang="zh">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>BBS 论坛</title>  
    <link rel="stylesheet" href="static/style.css">  
    <style>  
        /* 添加烟花背景 */
        body {
            position: relative;
            overflow: hidden; 
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }

        #fireworks {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none; 
        }

        .user-center {  
            position: absolute;  
            top: 20px;           
            right: 20px;         
            display: flex;        
            flex-direction: column; /* 垂直排列 */
            align-items: center;  
        }  
        .user-center img {  
            border-radius: 50%;  
            width: 40px;         
            height: 40px;        
            vertical-align: middle;  
            margin-bottom: 5px;   /* 头像与用户名间距 */  
        }  
        nav ul {  
            list-style: none;  
            padding: 0;  
            display: flex;  
            justify-content: center; /* 居中 */
            margin: 0;  
            flex-grow: 1; /* 允许导航栏伸展以填充空间 */
        }  
        nav ul li {  
            margin: 0 15px;  
        }  
        nav ul li:last-child {
            margin-left: auto; /* 将最后一个项目推到右边 */
        }
    </style>  
</head>  
<body>  
    <canvas id="fireworks"></canvas> <!-- 烟花画布 -->
    <div class="container">
        <h1>BBS 论坛</h1>  
        <nav>  
            <ul>  
                <?php if (!isset($_SESSION['user_id'])): ?>  
                    <li><a href="login.php">登录</a></li>  
                    <li><a href="register.php">注册</a></li>  
                <?php else: ?>  
                    <li><a href="publish.php">发表文章</a></li>  
                    <li><a href="view_articles.php">查看文章</a></li>  
                    <li><a href="logout.php">退出登录</a></li>  
                <?php endif; ?>  
            </ul>  
        </nav>  

        <!-- 用户中心链接 -->  
        <div class="user-center">  
            <?php if (isset($_SESSION['user_id'])): ?>  
                <a href="my_articles.php">  
                    <img src="<?php echo htmlspecialchars($user['img'] ?: 'images/default-avatar.png'); ?>" alt="头像">  
                    <span><?php echo htmlspecialchars($user['username']); ?></span> <!-- 显示用户名 -->
                </a>  
                <a href="profile.php">用户中心</a>  
            <?php endif; ?>  
        </div>  

        <h2>欢迎来到论坛!</h2>
        <p class="message">Join Us</p>
        <button id="celebrate">点一点</button>

        <!-- 弹出词语区域 -->
        <div class="popup" id="popup">
            <div class="words">
                <div class="word">别焦虑</div>
                <div class="word">多思考</div>
                <div class="word">多分享</div>
                <div class="word">保持年轻</div>
                <div class="word">坚持梦想</div>
                <div class="word">虾米生活</div>
                <div class="word">多摸鱼</div>
                <div class="word">八卦一下</div>
                <div class="word">帅的人已睡着</div>
                <div class="word">低俗但好笑</div>
                <div class="word">无语住了</div>
                <div class="word">效率</div>
                <div class="word">友好</div>
                <div class="word">专注</div>
                <div class="word">乐观</div>
                <div class="word">自信</div>
                <div class="word">仙品</div>
                <div class="word">又进步了</div>
                <div class="word">我没惹你</div>
                <div class="word">我是您的奴隶</div>
                <div class="word">拿图扣1</div>
            </div>
        </div>

        <p id="dayMessage" class="day-message"></p>

        <!-- 新增内容 -->
        <div class="additional-info">
            <h3>关于我们</h3>
            <p>这是一个社区论坛，您可以在这里分享您的想法和经验，与其他用户互动。</p>
            <h3>社交媒体</h3>
            <ul class="social-media">
                <li><a href="https://www.douyin.com" target="_blank">抖音</a></li>
                <li><a href="https://www.bilibili.com" target="_blank">哔哩哔哩</a></li>
                <li><a href="https://www.xiaohongshu.com" target="_blank">小红书</a></li>
            </ul>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const message = document.querySelector('.message');
        setTimeout(() => {
            message.style.opacity = 1; // 设置为不透明
        }, 500); // 500毫秒后显示

        // 烟花效果
        const canvas = document.getElementById('fireworks');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        function randomColor() {
            return `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 1)`;
        }

        function Firework(x, y) {
            this.x = x;
            this.y = y;
            this.radius = Math.random() * 4 + 1;
            this.color = randomColor();
            this.speed = Math.random() * 5 + 2;
            this.angle = Math.random() * Math.PI * 2;
            this.dx = Math.cos(this.angle) * this.speed;
            this.dy = Math.sin(this.angle) * this.speed;
            this.alpha = 1;
        }

        const fireworks = [];

        function explode() {
            const numParticles = Math.floor(Math.random() * 100 + 50);
            for (let i = 0; i < numParticles; i++) {
                fireworks.push(new Firework(Math.random() * canvas.width, Math.random() * canvas.height));
            }
        }

        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            for (let i = 0; i < fireworks.length; i++) {
                const f = fireworks[i];
                ctx.beginPath();
                ctx.arc(f.x, f.y, f.radius, 0, Math.PI * 2, false);
                ctx.fillStyle = f.color;
                ctx.fill();
                f.x += f.dx;
                f.y += f.dy;
                f.alpha -= 0.01;
                if (f.alpha <= 0) {
                    fireworks.splice(i, 1);
                    i--;
                }
            }
            requestAnimationFrame(draw);
        }

        document.getElementById('celebrate').addEventListener('click', () => {
            explode();
            displayDayMessage(); // 点击按钮时显示周几的消息
            showWords(); // 显示弹出词语
        });

        // 显示当前星期几的消息
        function displayDayMessage() {
            const daysOfWeek = ["日", "一", "二", "三", "四", "五", "六"];
            const currentDay = new Date().getDay();
            const dayMessage = document.getElementById('dayMessage');
            dayMessage.textContent = `今天周${daysOfWeek[currentDay]}，适合发文章！`;
        }

        // 显示弹出词语
        function showWords() {
            const popup = document.getElementById('popup');
            popup.style.display = 'block';
            setTimeout(() => {
                popup.style.display = 'none'; // 3秒后隐藏
            }, 3000);
        }

        draw();
    });
    </script>
</body>  
</html>
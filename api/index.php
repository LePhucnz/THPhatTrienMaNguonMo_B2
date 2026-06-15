<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Manager - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            width: 100%;
            max-width: 420px;
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: #667eea;
            font-size: 32px;
            margin-bottom: 8px;
        }
        
        .logo p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }
        
        .message.error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ef5350;
            display: block;
        }
        
        .message.success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #66bb6a;
            display: block;
        }
        
        .message.info {
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid #42a5f5;
            display: block;
        }
        
        .quick-login {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .quick-login h3 {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .quick-buttons {
            display: flex;
            gap: 10px;
        }
        
        .quick-btn {
            flex: 1;
            padding: 10px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s;
        }
        
        .quick-btn:hover {
            border-color: #667eea;
            background: #f5f7ff;
        }
        
        .quick-btn strong {
            display: block;
            color: #667eea;
            margin-bottom: 3px;
        }
        
        .quick-btn span {
            color: #999;
            font-size: 11px;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #667eea;
        }
        
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div id="loadingScreen" class="login-container" style="display: none;">
        <div class="loading">
            <div class="spinner"></div>
            <h2>Đang kiểm tra đăng nhập...</h2>
        </div>
    </div>
    
    <!-- Login Form -->
    <div id="loginForm" class="login-container" style="display: none;">
        <div class="logo">
            <h1>🚀 API Manager</h1>
            <p>Đăng nhập để quản lý API</p>
        </div>
        
        <div id="message" class="message"></div>
        
        <form id="loginFormElement">
            <div class="form-group">
                <label for="username">👤 Tên đăng nhập</label>
                <input type="text" id="username" name="username" placeholder="Nhập username" required autocomplete="username">
            </div>
            
            <div class="form-group">
                <label for="password">🔒 Mật khẩu</label>
                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required autocomplete="current-password">
            </div>
            
            <button type="submit" class="btn-login" id="btnLogin">
                🔐 Đăng nhập
            </button>
        </form>
        
        <div class="quick-login">
            <h3>Đăng nhập nhanh (test)</h3>
            <div class="quick-buttons">
                <button class="quick-btn" onclick="quickLogin('admin', 'password')">
                    <strong>👑 Admin</strong>
                    <span>admin / password</span>
                </button>
                <button class="quick-btn" onclick="quickLogin('lhphuc', 'password')">
                    <strong>👤 User</strong>
                    <span>lhphuc / password</span>
                </button>
            </div>
        </div>
    </div>
    
    <script>
        const API_BASE = 'http://localhost:8080/api';
        
        // Khi trang load
        window.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('api_token');
            
            if (token) {
                // Có token → kiểm tra xem còn hợp lệ không
                showLoading();
                verifyToken(token);
            } else {
                // Chưa có token → hiện form login
                showLoginForm();
            }
        });
        
        function showLoading() {
            document.getElementById('loadingScreen').style.display = 'block';
            document.getElementById('loginForm').style.display = 'none';
        }
        
        function showLoginForm() {
            document.getElementById('loadingScreen').style.display = 'none';
            document.getElementById('loginForm').style.display = 'block';
        }
        
        function showMessage(text, type) {
            const msg = document.getElementById('message');
            msg.textContent = text;
            msg.className = 'message ' + type;
        }
        
        // Kiểm tra token có hợp lệ không
        async function verifyToken(token) {
            try {
                const response = await fetch(API_BASE + '/account/me', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const user = await response.json();
                    // Token hợp lệ → chuyển sang API Manager
                    localStorage.setItem('api_user', JSON.stringify(user));
                    window.location.href = 'manager.php';
                } else {
                    // Token hết hạn hoặc không hợp lệ
                    localStorage.removeItem('api_token');
                    localStorage.removeItem('api_user');
                    showMessage('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.', 'error');
                    showLoginForm();
                }
            } catch (error) {
                console.error('Error verifying token:', error);
                showMessage('Lỗi kết nối server. Vui lòng thử lại.', 'error');
                showLoginForm();
            }
        }
        
        // Xử lý form login
        document.getElementById('loginFormElement').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            const btnLogin = document.getElementById('btnLogin');
            
            btnLogin.disabled = true;
            btnLogin.textContent = '⏳ Đang đăng nhập...';
            
            try {
                const response = await fetch(API_BASE + '/account/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username, password })
                });
                
                const data = await response.json();
                
                if (response.ok && data.token) {
                    // Đăng nhập thành công
                    localStorage.setItem('api_token', data.token);
                    localStorage.setItem('api_user', JSON.stringify(data.user));
                    
                    showMessage('✅ Đăng nhập thành công! Đang chuyển hướng...', 'success');
                    
                    setTimeout(() => {
                        window.location.href = 'manager.php';
                    }, 1000);
                } else {
                    showMessage(data.message || 'Đăng nhập thất bại', 'error');
                    btnLogin.disabled = false;
                    btnLogin.textContent = '🔐 Đăng nhập';
                }
            } catch (error) {
                console.error('Login error:', error);
                showMessage('Lỗi kết nối server: ' + error.message, 'error');
                btnLogin.disabled = false;
                btnLogin.textContent = '🔐 Đăng nhập';
            }
        });
        
        // Đăng nhập nhanh
        function quickLogin(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
            document.getElementById('loginFormElement').dispatchEvent(new Event('submit'));
        }
    </script>
</body>
</html>
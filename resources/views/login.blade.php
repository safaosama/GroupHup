<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
:root {
    --bg-main: #0a0f1b;
    --bg-dark: #1a1f35;
    --card: #1e2749;
    --primary: #3b82f6;
    --secondary: #8b5cf6;
    --gradient: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    --text: #ffffff;
    --text-muted: #8b91a8;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', system-ui, sans-serif;
}

body {
    background: var(--bg-main);
    color: var(--text);
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

/* Background image with blur */
body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('{{ asset("images/logo2.png") }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    filter: blur(12px) brightness(0.25) saturate(0.8);
    z-index: 1;
}

.login-box {
    width: 380px;
    background: rgba(30, 39, 73, 0.92);
    padding: 45px;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(15px);
    border: 1px solid rgba(59, 130, 246, 0.15);
    position: relative;
    z-index: 2;
}

h1 {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 6px;
    color: var(--text);
    background: var(--gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

p {
    color: var(--text-muted);
    margin-bottom: 35px;
    font-size: 13px;
    letter-spacing: 0.5px;
}

input {
    width: 100%;
    padding: 12px 15px;
    margin-bottom: 12px;
    border-radius: 10px;
    border: 1px solid rgba(59, 130, 246, 0.25);
    background: rgba(26, 31, 53, 0.8);
    color: var(--text);
    font-size: 13px;
    transition: 0.3s;
}

input::placeholder {
    color: rgba(255, 255, 255, 0.45);
}

input:focus {
    outline: none;
    border-color: rgba(59, 130, 246, 0.6);
    background: rgba(26, 31, 53, 1);
    box-shadow: 0 0 10px rgba(59, 130, 246, 0.1);
}

button {
    width: 100%;
    padding: 12px;
    margin-top: 8px;
    border: none;
    border-radius: 10px;
    background: var(--gradient);
    color: white;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(59, 130, 246, 0.4);
}

.error-msg {
    background: rgba(239, 68, 68, 0.15);
    border: 1px solid rgba(239, 68, 68, 0.4);
    color: #f87171;
    padding: 12px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: 13px;
}

.register-link {
    margin-top: 20px;
    font-size: 12px;
    color: var(--text-muted);
}

.register-link a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
}

.register-link a:hover {
    text-decoration: underline;
}
.logo-header{
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-bottom: 10px;
}

.logo-header img{
    width: 80px;
    height: 80px;
    object-fit: contain;
     filter: drop-shadow(0 0 10px rgba(142, 95, 251, 0.474));
}
</style>
</head>
<body>


<div class="container">

    <!-- Right Side -->
    <div class="login-box">

        <div class="logo-header">

            <h1>GroupHub</h1>
            <img src="{{ asset('images/logo.png') }}" alt="logo">
        </div>

        <p><em>Team Formation System</em></p>

        @if(session('error'))
            <div class="error-msg">{{ session('error') }}</div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <input type="text" name="student_id" placeholder="Student ID" required>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit">
                Login
            </button>
        </form>

        <div class="register-link">
            Don't have an account?
            <a href="/register">Register here</a>
        </div>

    </div>

</div>

</body>
</html>

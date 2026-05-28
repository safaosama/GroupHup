<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
:root {
    --bg-main: #0d121e;
    --bg-dark: #020617;
    --card: #131c49;
    --primary: #38bdf8;
    --gradient: linear-gradient(135deg, #7c3aed, #38bdf8);
    --text: #ffffff;
    --text-muted: #0f7e8b;
}

* { margin:0; padding:0; box-sizing:border-box; font-family:Segoe UI; }

body {
    background: var(--bg-main);
    color: var(--text);
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.register-box {
    width: 380px;
    background: var(--card);
    padding: 30px;
    border-radius: 20px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.4);
}

.logo {
    width: 250px;
    height: auto;
    display: block;
    margin: 0 auto 10px;
}

p { color: var(--text-muted); margin-bottom: 20px; }

input, select {
    width: 100%;
    padding: 14px;
    margin-bottom: 15px;
    border-radius: 12px;
    border: none;
    background: var(--bg-dark);
    color: #a09e9e;
}

select option { background: var(--bg-dark); }

button {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 12px;
    background: var(--gradient);
    color: black;
    font-weight: bold;
    cursor: pointer;
    transition: .3s;
}

button:hover { transform: scale(1.05); }

.error-msg {
    background: rgba(239, 68, 68, 0.15);
    border: 1px solid rgba(239, 68, 68, 0.4);
    color: #f87171;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 15px;
    font-size: 14px;
    text-align: left;
}

.login-link {
    margin-top: 18px;
    font-size: 13px;
    color: var(--text-muted);
}

.login-link a {
    color: var(--primary);
    text-decoration: none;
}

.login-link a:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="register-box">

    <img src="{{ asset('images/logo.png') }}" class="logo">
    <p><em>Create your account</em></p>

    @if($errors->any())
        <div class="error-msg">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="/register">
        @csrf

        <input name="name"
               placeholder="Full Name"
               value="{{ old('name') }}">

        {{-- student_id مش email --}}
        <input name="student_id"
               placeholder="Student ID"
               value="{{ old('student_id') }}">

        <input name="password"
               type="password"
               placeholder="Password">

        <select name="role">
            <option value="student">Student</option>
            <option value="instructor">Instructor</option>
        </select>

        <button type="submit">Register</button>
    </form>

    <div class="login-link">
        Already have an account? <a href="/login">Login here</a>
    </div>

</div>

</body>
</html>

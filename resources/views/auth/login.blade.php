<!DOCTYPE html>
<html>
<head>
    <title>Login - CloudeConta</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #f5f5f5; }
        .login-box { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { width: 100%; padding: 12px; background: #007cba; color: white; border: none; border-radius: 5px; }
        .error { color: red; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>CloudeConta - Login</h2>
        
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" required value="{{ old('email') }}" autocomplete="email">
            <input type="password" name="password" placeholder="Senha" required autocomplete="current-password">
            <label><input type="checkbox" name="remember"> Lembrar-me</label>
            <button type="submit">Entrar</button>
        </form>
        
        <p style="margin-top: 20px; text-align: center; color: #666;">
            Sistema CloudeConta Laravel - v2.0
        </p>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Registro - CloudConta</title>
</head>
<body>
    <h1>Registro</h1>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <input type="text" name="nome" placeholder="Nome" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Senha" required>
        <button type="submit">Registrar</button>
    </form>
</body>
</html>

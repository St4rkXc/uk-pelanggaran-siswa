<?php session_start(); ?>
<!DOCTYPE html>
<html>

<body>
    <h3>Login</h3>
    <form method="POST" action="login_process.php">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</body>

</html>


















<!DOCTYPE html>
<html>
<head>
  <title>Register & Login</title>
  <style>
    #loginForm { display: none; }
    .success { color: green; font-weight: bold; }
  </style>
</head>
<body>

  <div id="registerForm">
    <h2>Register</h2>
    <form action="register.php" method="POST">
      <input type="text" name="name" placeholder="Your Name" required><br><br>
      <input type="email" name="email" placeholder="Your Email" required><br><br>
      <input type="password" name="password" placeholder="Password" required><br><br>
      <button type="submit">Register</button>
    </form>
  </div>

  <div id="loginForm">
    <h2>Login</h2>
    <form action="login.php" method="POST">
      <input type="email" name="email" placeholder="Your Email" required><br><br>
      <input type="password" name="password" placeholder="Password" required><br><br>
      <button type="submit">Login</button>
    </form>
  </div>

  <script>
    // Check if success message exists in URL
    const params = new URLSearchParams(window.location.search);
    if (params.has('registered') && params.get('registered') === 'true') {
      alert("Successfully Registered!");
      document.getElementById('registerForm').style.display = 'none';
      document.getElementById('loginForm').style.display = 'block';
    }
  </script>

</body>
</html>

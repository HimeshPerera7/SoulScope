<?php
  require_once "header.php";
?>

<main class="registration-page">
  <div class="registration-container">
    <div class="registration-header">
      <h1>Welcome Back!</h1>
      <p>
        <?php 
            if (isset($_SESSION["login_error"])) {
                echo '<span class="error-text">' . htmlspecialchars($_SESSION["login_error"]) . '</span>';
                unset($_SESSION["login_error"]); 
            } else {
                echo 'Log In to Your Account';
            }
        ?>
      </p>
    </div>

    <form class="registration-form" action="includes/login_process.php" method="POST">
      <div class="role-switch">
          <input type="radio" id="user-role" name="role" value="user" checked>
          <label for="user-role">User</label>

          <input type="radio" id="admin-role" name="role" value="admin">
          <label for="admin-role">Admin</label>
      </div>
      <input type="email" id="email" name="email" placeholder="Email" required>
      <input type="password" id="password" name="password" placeholder="Password" required>
      <button type="submit" class="registration-submit-btn">Login</button>
    </form>

    <div class="login-link">
      <p>Don't have an account? <a href="register.php">Register</a></p>
    </div>
  </div>
</main>

<?php
  require_once "footer.php";
?>

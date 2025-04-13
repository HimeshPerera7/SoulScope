<?php
  require_once "header.php";
  
  /* Loading birth districts */
$sql = "SELECT * FROM districts;";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$districts = $stmt->fetchAll();
?>

  <main class="registration-page">
    <div class="registration-container">
      <div class="registration-header">
        <h1>Welcome!</h1>
        <p>
          <?php 
              if (isset($_SESSION["errors"]) && !empty($_SESSION["errors"])) {
                  echo '<span class="error-text">' . htmlspecialchars($_SESSION["errors"][0]) . '</span>';
              } else {
                  echo 'Create Your Account';
              }
          ?>
        </p>
      </div>
      
      <form class="registration-form" action="includes/register_process.php" method="POST">
          <div class="name-inputs">
              <input type="text" id="firstName" name="first_name" placeholder="First Name" required>
              <input type="text" id="lastName" name="last_name" placeholder="Last Name" required>
              <input type="email" id="email" name="email" placeholder="Email" required>
              <input type="password" id="password" name="password" placeholder="Password" required>
              <input type="password" id="c_password" name="c_password" placeholder="Confirm Password" required>
          </div>
          <div class="gender-selection">
              <div class="gender-inner">
                  <label for="male">Male</label>
                  <input type="radio" name="gender" id="male" value="Male" required>
              </div>
              <div class="gender-inner">
                  <label for="female">Female</label>
                  <input type="radio" name="gender" id="female" value="Female" required>
              </div>
          </div>
          <div class="date-inputs">
              <div>
                  <label for="birth_date">Date of Birth</label>
                  <input type="date" id="birth_date" name="birth_date" style="margin-top:5px" required>
              </div>
              <div>
                  <label for="birth_time">Time of Birth (24H Format)</label>
                  <input type="time" id="birth_time" name="birth_time" style="margin-top:5px" required>
              </div>
          </div>
          <select id="district" name="district" required onchange="loadCities(this.value)">
              <option value="">Select Birth District</option>
              <?php
              if (count($districts) > 0) {
                  foreach ($districts as $district) {
                      echo '<option value="' . (int)$district['id'] . '">' . htmlspecialchars($district['name_en']) . '</option>';
                  }
              }
              ?>
          </select>
          <select id="city" name="city" required>
              <option value="">Select Birth Town/City</option>
          </select>
          <p class="agreement">By selecting Register, you agree to our <a href="terms.php">Terms and Conditions</a></p>
          <button type="submit" class="registration-submit-btn">Register</button>
          
          <?php if (isset($_SESSION["errors"]) && count($_SESSION["errors"]) > 1): ?>
            <div class="error-messages">
                <?php for ($i = 1; $i < count($_SESSION["errors"]); $i++): ?>
                    <p class="error-text"><?php echo htmlspecialchars($_SESSION["errors"][$i]); ?></p>
                <?php endfor; ?>
            </div>
          <?php endif; ?>
          <?php unset($_SESSION["errors"]); ?> <!-- Clear errors after displaying -->
      
      </form>
      <div class="login-link">
            <p>Already have an account? <a href="login.php">Login</a></p>
      </div>
    </div>
  </main>

<?php
  require_once "footer.php";
?>


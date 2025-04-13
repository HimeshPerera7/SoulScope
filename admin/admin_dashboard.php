<?php
session_start();
require_once __DIR__ . "/../includes/config.php";

// Check if admin is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard - SoulScope</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link rel="stylesheet" href="css/dashboard.css"/>
  <script src="../js/jquery-3.7.1.min.js"></script>
</head>
<body>
<header>
    <div class="header-container">
        <img src="../assets/images/logo/logo_name.png" alt="Logo" class="logo">
      <nav>
                  <ul class="nav-links">
                      <li><a href="admin_view_profile.php">Profile</a></li>
                      <li><a href="../includes/logout.php">Logout</a></li>
                  </ul>
      </nav>
    </div>
  </header>

  <div class="dashboard-container">
    <aside class="sidebar">
      <h2 class="logo">Admin Dashboard</h2>
      <ul class="menu">
        <li class="tab-link active" data-tab="overview">📊 Overview</li>
        <li class="tab-link" data-tab="view_users">👤 View Users</li>
        <li class="tab-link" data-tab="user_reports">✏️ User Reports</li>
        <li class="tab-link" data-tab="performance">📈 Platform Performance</li>
      </ul>
    </aside>

    <main class="main">
      <header class="top-bar">
        <h3>Welcome, Admin</h3>
      </header>

      <section id="tab-content" class="tab-content active">
        <p>Loading...</p>
      </section>
    </main>
  </div>

  <script>
    const tabLinks = document.querySelectorAll('.tab-link');
    const contentSection = document.getElementById('tab-content');

    tabLinks.forEach(link => {
      link.addEventListener('click', () => {
        // Handle active class
        tabLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');

        // Fetch and load the respective tab content
        const tabFile = link.getAttribute('data-tab');
        fetch(`partials/${tabFile}.php`)
          .then(response => response.text())
          .then(data => {
            contentSection.innerHTML = data;
          })
          .catch(error => {
            contentSection.innerHTML = "<p>Error loading content.</p>";
            console.error(error);
          });
      });
    });

    // Optional: Load 'overview.php' by default on page load
    window.addEventListener('DOMContentLoaded', () => {
      fetch('partials/overview.php')
        .then(res => res.text())
        .then(data => {
          contentSection.innerHTML = data;
        });
    });

    $(document).ready(function() {
        $('#deleteLink').click(function(e) {
            e.preventDefault(); // Prevent link behavior
        });
    });
    
    function deleteProfile(userId) {
        // console.log(userId);
        if (confirm("Are you sure you want to delete this profile?")) {
            var profileId = parseInt(userId);
            var reportUrl = "api/delete_profile.php?id=" + profileId;

            $.getJSON(reportUrl, function(response) {
                if (response.success) {
                    // location.reload();
                    fetch('partials/view_users.php')
                        .then(res => res.text())
                        .then(data => {
                            contentSection.innerHTML = data;
                        });
                } else {
                    alert("Something went wrong. Try again.");
                }
            }).fail(function() {
                alert("Error connecting to server.");
            });
        }
        
    }
    
  </script>

</body>
</html>

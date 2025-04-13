<?php require_once "header.php"; ?>

<!-- Overlay with loading spinner -->
<div id="overlay">
   <div class="spinner"></div>
</div>

    <main class="dashboard-user">
        
        <!-- Top Bar -->
<!--        <div class="dashboard-header">-->
<!--            <h1>--><?php //echo htmlspecialchars($userFirstName); ?><!--'s Soul</h1>-->
<!--            <ul class="dash-links">-->
<!--                <li><a href="user_view_profile.php">Profile</a></li>-->
<!--                <li><a href="message.php">Message</a></li>-->
<!--            </ul>-->
<!--        </div>-->
        
        <div class="dashboard-container">
            
            <!-- Daily Insights -->
            <div class="insights-section">
                <div class="insights-heading">
                    <div class="zodiac-image">
                        <img src="<?=$zodiacPic?>" alt="zodiac image">
                    </div>
                    <div class="zodiac-text">
                        <h2>Hi</h2>
                        <h1><?=ucfirst($userFirstName)?></h1>
                        <h3>This is your destiny today</h3>
                    </div>
                </div>
                <div class="insights-content">
                    <div class="insights-inner">
                        <div>
                            <h3>Today</h3>
                            <h2 id="currentDate"></h2>
                        </div>
                        <div style="display: flex; justify-content: center; align-items: center;">
                            <img src="assets/images/icons/today.png" alt="zodiac image">
                        </div>
                    </div>
                    <div class="insights-inner">
                        <div>
                            <h3>Sunrise</h3>
                            <h2 id="sunRise"></h2>
                        </div>
                        <div style="display: flex; justify-content: center; align-items: center;">
                            <img src="assets/images/icons/sunrise.png" alt="zodiac image">
                        </div>
                    </div>
                    <div class="insights-inner">
                        <div>
                            <h3>Sunset</h3>
                            <h2 id="sunSet"></h2>
                        </div>
                        <div style="display: flex; justify-content: center; align-items: center;">
                            <img src="assets/images/icons/sunset.png" alt="zodiac image">
                        </div>
                    </div>
                    <div class="insights-inner">
                        <div>
                            <h3>Rahu Kalaya</h3>
                            <h2 id="rahuKalayaFrom"></h2>
                            <h2 id="rahuKalayaUntil"></h2>
                        </div>
                        <div style="display: flex; justify-content: center; align-items: center;">
                            <img src="assets/images/icons/bad.png" alt="zodiac image">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Horoscope Insights -->
            <div class="horoscope-section">
                <div class="tabs">
                    <div class="tab-button active" data-tab="tab1">Today</div>
                    <div class="tab-button" data-tab="tab2">This Week</div>
                </div>
                <div class="tab-content active" id="tab1">
                    <div class="tab-section-wrapper">
                        <div>
                            <h4>Life</h4>
                            <p id="todayLife"></p>
                        </div>
                        <img src="assets/images/icons/life.png" alt="zodiac image">
                    </div>
                    <div class="tab-section-wrapper">
                        <div>
                            <h4>Love</h4>
                            <p id="todayLove"></p>
                        </div>
                        <img src="assets/images/icons/today.png" alt="zodiac image">
                    </div>
                    <div class="tab-section-wrapper">
                        <div>
                            <h4>Career</h4>
                            <p id="todayCareer"></p>
                        </div>
                        <img src="assets/images/icons/career.png" alt="zodiac image">
                    </div>
                    <div class="tab-section-wrapper">
                        <div>
                            <h4>Health</h4>
                            <p id="todayHealth"></p>
                        </div>
                        <img src="assets/images/icons/health.png" alt="zodiac image">
                    </div>
                    <div class="tab-section-wrapper">
                        <div>
                            <p id="todayLucky"></p>
                        </div>
                    </div>
                </div>
                <div class="tab-content" id="tab2">
                    <div class="tab-section-wrapper">
                        <div>
                            <h4>Life</h4>
                            <p id="weekLife"></p>
                        </div>
                        <img src="assets/images/icons/life.png" alt="zodiac image">
                    </div>
                    <div class="tab-section-wrapper">
                        <div>
                            <h4>Love</h4>
                            <p id="weekLove"></p>
                        </div>
                        <img src="assets/images/icons/today.png" alt="zodiac image">
                    </div>
                    <div class="tab-section-wrapper">
                        <div>
                            <h4>Career</h4>
                            <p id="weekCareer"></p>
                        </div>
                        <img src="assets/images/icons/career.png" alt="zodiac image">
                    </div>
                    <div class="tab-section-wrapper">
                        <div>
                            <h4>Health</h4>
                            <p id="weekHealth"></p>
                        </div>
                        <img src="assets/images/icons/health.png" alt="zodiac image">
                    </div>
                    <div class="tab-section-wrapper">
                        <div>
                            <p id="weekLucky"></p>
                        </div>
                    </div>
                </div>
                <div class="horoscope-image">
                    <div id="horoscopeImage"></div>
                    <div id="horoscopeDownload">
                        <a href="#" id="horoscopeDownloadLink">Download</a>
                    </div>
                </div>
            </div>

            <!-- Suggested Partners -->
            <div class="partners-section" id="partners-list">
                <div class="partners-heading">
                    <a href="#" id="reloadMatching">Reload</a>
                </div>
                <div id="matching-container"></div>
            </div>
        </div>
        
        <style>
            
            .horoscope-image {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            
            #horoscopeDownloadLink {
                text-decoration: none;
                color: #ffffff;
                font-weight: 600;
                padding: 10px 20px;
                border-radius: 50px;
                background-color: #fd2a75;
                transition: all 0.3s;
            }

            #horoscopeDownloadLink:hover {
                background-color: #85002c;
            }
            
            #horoscopeDownload {
                margin-top: 20px;
            }
            
            /* Fullscreen overlay */
            #overlay {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                background: rgba(255, 255, 255, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
            }

            /* Loading spinner */
            .spinner {
                border: 4px solid rgba(0, 0, 0, 0.1);
                border-top: 4px solid #fd2a75;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>

        <script>
            
            $(document).ready(function() {
                
                function formatTime(dateTime) {
                    let timePart = dateTime.split(" ")[1].split(".")[0]; // Extract "21:45:06"
                    let [hour, minute, second] = timePart.split(":").map(Number);
                    let period = hour >= 12 ? "PM" : "AM";
                    hour = hour % 12 || 12; // Convert 24-hour to 12-hour format
                    return `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}:${second.toString().padStart(2, '0')} ${period}`;
                }
                
                let today = new Date();
                let year = today.getFullYear();
                let month = String(today.getMonth() + 1).padStart(2, '0');
                let day = String(today.getDate()).padStart(2, '0');
                let weekday = today.toLocaleString('en-us', { weekday: 'long' });
                let formattedDate = `${year}-${month}-${day} ${weekday}`;
                $('#currentDate').text(formattedDate);
                
                let userId = <?=(int)$userId?>
                // Get current date in YYYY-MM-DD format
                let currentDate = new Date().toISOString().split('T')[0];

                /* Load Matching users when page opens*/
                fetchMatchingUsers();

                $("#reloadMatching").on("click", function(event) {
                    event.preventDefault(); // Prevent default link action
                    fetchMatchingUsers();
                });

                function fetchMatchingUsers() {
                    // Get Matching Users
                    $.ajax({
                        url: "api/fetch_partners.php?id=" + userId,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            $("#matching-container").empty(); // Clear previous content

                            $.each(data.data, function (key, value) {
                                $("#matching-container").append(
                                    `<div class="partner-card" style="display: none;">
                                        <div>
                                            <img src="assets/images/avatar/${value.profilePic}" alt="${value.name}">
                                        </div>
                                        <div>
                                            <p><strong>Name:</strong> ${value.name}</p>
                                            <p><strong>Age:</strong> ${value.age}</p>
                                            <p><strong>Sign:</strong> ${value.zodiacSign}</p>
                                            <p><strong>Compatibility Score:</strong> ${value.compatibility}%</p>
                                        </div>
                                        <div>
                                            <a href="partner_view_profile.php?id=${value.id}"><button>View Profile</button></a>
                                        </div>
                                    </div>`
                                );
                                $(".partner-card:last").fadeIn(500);
                            });
                        },
                        error: function () {
                            console.error("Error fetching data");
                            $("#overlay").fadeOut();
                        }
                    });
                }
                
                // Set user's horoscope image
                $.ajax({
                    url: "api/fetch_horoscope.php?id="+ userId,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        /* Updating Data */
                        $('#horoscopeImage').html(data.data); // Set the horoscope image
                    },
                    error: function () {
                        console.error("Error fetching horoscope image");
                    }
                });
                
                // Set daily predictions
                $.ajax({
                    url: "api/fetch_predictions.php?id="+ userId,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        /* Updating Data */
                        $('#todayLife').text(data.data.daily.general);
                        $('#todayLove').text(data.data.daily.love);
                        $('#todayCareer').text(data.data.daily.career);
                        $('#todayHealth').text(data.data.daily.health);
                        $('#todayLucky').text("Your lucky number is " + data.data.daily.lucky_number + " and lucky color is " + data.data.daily.lucky_color);
                        $('#weekLife').text(data.data.weekly.general);
                        $('#weekLove').text(data.data.weekly.love);
                        $('#weekCareer').text(data.data.weekly.career);
                        $('#weekHealth').text(data.data.weekly.health);
                        $('#weekLucky').text("Your lucky numbers are " + data.data.weekly.lucky_numbers + " and lucky colors are " + data.data.weekly.lucky_colors);
                    },
                    error: function () {
                        console.error("Error fetching data");
                    }
                });
                
                // Get user's location
               if (navigator.geolocation) {
                   navigator.geolocation.getCurrentPosition(function(position) {
                       let latitude = position.coords.latitude;
                       let longitude = position.coords.longitude;
           
                       // Send data via GET request to API
                       $.ajax({
                           url: "api/soul_info.php?id="+ userId + "&lat=" + latitude + "&long=" + longitude + "&date=" + currentDate,
                           type: "GET",
                           dataType: "json",
                           success: function (data) {
                               /* Updating Data */
                               $('#sunRise').text(data.data.sun.sunrise + ' AM');
                               $('#sunSet').text(data.data.sun.sunset + ' PM');
                               $('#rahuKalayaFrom').text('From ' + formatTime(data.data.rahu_kalaya.starts_at));
                               $('#rahuKalayaUntil').text('Until ' + formatTime(data.data.rahu_kalaya.ends_at));
                               $("#overlay").fadeOut();
                           },
                           error: function () {
                               console.error("Error fetching data");
                               $("#overlay").fadeOut();
                           }
                       });
           
                   }, function(error) {
                       console.error("Error getting location:", error);
                       $("#overlay").fadeOut();
                   });
               } else {
                   console.error("Geolocation is not supported by this browser.");
                   $("#overlay").fadeOut();
               }

                $('.tab-button').click(function() {
                    var tabId = $(this).data('tab');
                    $('.tab-button').removeClass('active');
                    $(this).addClass('active');
                    $('.tab-content').removeClass('active');
                    $('#' + tabId).addClass('active');
                })
                
                $('#horoscopeDownloadLink').on('click', function (e) {
                    e.preventDefault();

                    var svgElement = $('#horoscopeImage svg')[0];

                    if (!svgElement) {
                        alert('SVG not found!');
                        return;
                    }

                    // Serialize SVG
                    var svgData = new XMLSerializer().serializeToString(svgElement);
                    var svgBlob = new Blob([svgData], { type: "image/svg+xml;charset=utf-8" });
                    var url = URL.createObjectURL(svgBlob);

                    var img = new Image();
                    img.onload = function () {
                        // Get original dimensions
                        var originalWidth = svgElement.viewBox?.baseVal?.width ||
                            svgElement.getAttribute('width') ||
                            svgElement.getBoundingClientRect().width;

                        var originalHeight = svgElement.viewBox?.baseVal?.height ||
                            svgElement.getAttribute('height') ||
                            svgElement.getBoundingClientRect().height;

                        originalWidth = parseFloat(originalWidth);
                        originalHeight = parseFloat(originalHeight);

                        // Target output width
                        var targetWidth = 800;
                        var scale = targetWidth / originalWidth;
                        var targetHeight = originalHeight * scale;

                        // Create canvas
                        var canvas = document.createElement('canvas');
                        canvas.width = targetWidth;
                        canvas.height = targetHeight;

                        var ctx = canvas.getContext('2d');
                        ctx.fillStyle = "#ffffff"; // white background
                        ctx.fillRect(0, 0, targetWidth, targetHeight);
                        ctx.drawImage(img, 0, 0, targetWidth, targetHeight);

                        // Export to JPEG
                        var jpegUrl = canvas.toDataURL("image/jpeg");

                        // Trigger download
                        var link = document.createElement('a');
                        link.href = jpegUrl;
                        link.download = "horoscope.jpg";
                        link.click();

                        URL.revokeObjectURL(url);
                    };

                    img.onerror = function () {
                        alert("Error loading the SVG image.");
                    };

                    img.src = url;
                });







            });
           
        </script>
    </main>


<?php require_once "footer.php"; ?>

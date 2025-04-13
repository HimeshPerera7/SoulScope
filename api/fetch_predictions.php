<?php
session_start();
require_once "../includes/config.php";
$errors = array();
$data = array();

/* Get data from URL */
$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch user details if logged in
if (isset($_SESSION["user_id"])) {
    $userId = $_SESSION["user_id"];
    
    // Use PDO to fetch user details
    $query = "SELECT u.*, c.name_en, c.latitude, c.longitude FROM `users` AS u LEFT JOIN cities AS c ON u.city=c.id WHERE userId = :userId";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $zodiacSign    = htmlspecialchars($user['zodiacSign']);
    }
    
    /* Get daily predictions depending on user's zodiac sign - (Not 100% accurate. This is a general predictions) */
    $horoscopes = [
        "aries" => [
            "general" => "Today is a great day to focus on personal growth and career advancements.",
            "love" => "Your relationships may need extra attention today. Open communication is key.",
            "career" => "Opportunities for leadership may arise, be confident in your decisions.",
            "health" => "Maintain a balanced diet and stay hydrated for better energy levels.",
            "lucky_number" => 9,
            "lucky_color" => "Red"
        ],
        "taurus" => [
            "general" => "Avoid conflicts today, and focus on your financial planning.",
            "love" => "A romantic surprise might be in store for you, embrace the moment.",
            "career" => "Hard work will be rewarded soon, stay consistent.",
            "health" => "Stress levels may rise, practice meditation or breathing exercises.",
            "lucky_number" => 6,
            "lucky_color" => "Green"
        ],
        "gemini" => [
            "general" => "New opportunities will come your way, be open to change.",
            "love" => "Flirting and new connections may bring excitement.",
            "career" => "Networking will open new doors, don’t hesitate to reach out.",
            "health" => "Get enough rest to avoid mental fatigue.",
            "lucky_number" => 5,
            "lucky_color" => "Yellow"
        ],
        "cancer" => [
            "general" => "Take care of your health, and spend time with family.",
            "love" => "Emotional connections will deepen today.",
            "career" => "Avoid office gossip and stay focused on your tasks.",
            "health" => "Minor digestive issues may arise, eat light meals.",
            "lucky_number" => 2,
            "lucky_color" => "White"
        ],
        "leo" => [
            "general" => "A day filled with energy and positivity, make the most of it.",
            "love" => "A bold move in love could bring exciting results.",
            "career" => "Recognition for your hard work is coming soon.",
            "health" => "Engage in physical activity to boost your energy.",
            "lucky_number" => 1,
            "lucky_color" => "Gold"
        ],
        "virgo" => [
            "general" => "Pay attention to details at work, and avoid unnecessary stress.",
            "love" => "Patience is needed in relationships, avoid overanalyzing.",
            "career" => "Your problem-solving skills will shine today.",
            "health" => "Stay hydrated and avoid excessive caffeine.",
            "lucky_number" => 7,
            "lucky_color" => "Blue"
        ],
        "libra" => [
            "general" => "Your social life will flourish today, reconnect with old friends.",
            "love" => "Romance is in the air, make time for your partner.",
            "career" => "Teamwork will lead to success in a big project.",
            "health" => "Take a break from screen time to relax your eyes.",
            "lucky_number" => 3,
            "lucky_color" => "Pink"
        ],
        "scorpio" => [
            "general" => "A powerful day for transformation and making bold decisions.",
            "love" => "Passionate moments await, express your true feelings.",
            "career" => "A challenging situation will bring out your best abilities.",
            "health" => "Avoid overexertion, get enough sleep.",
            "lucky_number" => 8,
            "lucky_color" => "Black"
        ],
        "sagittarius" => [
            "general" => "Travel and adventure might be on your mind today.",
            "love" => "A spontaneous plan with your partner will bring joy.",
            "career" => "Your creative ideas will be appreciated at work.",
            "health" => "Outdoor activities will refresh your mind.",
            "lucky_number" => 4,
            "lucky_color" => "Purple"
        ],
        "capricorn" => [
            "general" => "Stay disciplined, and you will see long-term success.",
            "love" => "A mature conversation will strengthen your relationship.",
            "career" => "Financial growth is on the horizon, make wise investments.",
            "health" => "Focus on posture and avoid back pain.",
            "lucky_number" => 10,
            "lucky_color" => "Brown"
        ],
        "aquarius" => [
            "general" => "Think outside the box, creativity will lead to success.",
            "love" => "You may meet someone special unexpectedly.",
            "career" => "Technology and innovation will play a key role in your success.",
            "health" => "Hydrate well and eat fresh foods for better vitality.",
            "lucky_number" => 11,
            "lucky_color" => "Turquoise"
        ],
        "pisces" => [
            "general" => "Trust your intuition, and go with the flow today.",
            "love" => "A heart-to-heart talk will resolve past misunderstandings.",
            "career" => "Your imagination will help you in problem-solving today.",
            "health" => "Engage in relaxing activities like yoga or music therapy.",
            "lucky_number" => 12,
            "lucky_color" => "Sea Green"
        ]
    ];
    
    $data['daily'] = $horoscopes[strtolower($zodiacSign)];
    
    /* Get weekly predictions depending on user's zodiac sign - (Not 100% accurate. This is a general predictions) */
    $horoscopes = [
        "aries" => [
            "general" => "This week brings opportunities for personal growth. Focus on setting long-term goals and improving work-life balance.",
            "love" => "Romantic energy is high. If single, you may meet someone special. If in a relationship, plan quality time together.",
            "career" => "A leadership opportunity may arise. Stay confident and showcase your skills.",
            "health" => "Manage stress with meditation or exercise. Take care of your mental well-being.",
            "lucky_numbers" => [3, 9, 17],
            "lucky_colors" => ["Red", "Orange"]
        ],
        "taurus" => [
            "general" => "Financial stability is in focus. It's a good week to plan investments and savings.",
            "love" => "Communication is key in relationships. Be honest about your feelings.",
            "career" => "A promotion or recognition for hard work is possible.",
            "health" => "Take care of your digestive system. Avoid junk food.",
            "lucky_numbers" => [6, 14, 22],
            "lucky_colors" => ["Green", "Brown"]
        ],
        "gemini" => [
            "general" => "A week of change and adaptability. Stay open to new opportunities.",
            "love" => "Flirtation and fun await! A spontaneous romantic gesture will bring excitement.",
            "career" => "Your creativity will help you excel at work. Express new ideas.",
            "health" => "Take breaks from screens to avoid eye strain.",
            "lucky_numbers" => [5, 11, 23],
            "lucky_colors" => ["Yellow", "Blue"]
        ],
        "cancer" => [
            "general" => "Family matters take priority. Spend quality time with loved ones.",
            "love" => "An emotional week for relationships. Patience will strengthen bonds.",
            "career" => "Collaboration at work will lead to success. Be a team player.",
            "health" => "Maintain hydration and a balanced diet.",
            "lucky_numbers" => [2, 10, 20],
            "lucky_colors" => ["White", "Silver"]
        ],
        "leo" => [
            "general" => "A strong week for personal achievements. Set ambitious goals and take action.",
            "love" => "Your charm is magnetic. Take the lead in romance and express your desires.",
            "career" => "Recognition for your efforts is near. Stay consistent.",
            "health" => "A great week for fitness. Consider starting a new workout routine.",
            "lucky_numbers" => [1, 8, 19],
            "lucky_colors" => ["Gold", "Orange"]
        ],
        "virgo" => [
            "general" => "Organization and planning will help you achieve stability.",
            "love" => "A meaningful conversation can bring clarity in relationships.",
            "career" => "Your problem-solving skills will shine this week.",
            "health" => "Watch out for anxiety. Practice mindfulness.",
            "lucky_numbers" => [4, 16, 24],
            "lucky_colors" => ["Blue", "Brown"]
        ],
        "libra" => [
            "general" => "A socially active week. Networking will bring unexpected benefits.",
            "love" => "Love is in the air. An exciting romantic surprise awaits.",
            "career" => "A teamwork-oriented project will be successful.",
            "health" => "Focus on relaxation and self-care.",
            "lucky_numbers" => [3, 7, 21],
            "lucky_colors" => ["Pink", "Lavender"]
        ],
        "scorpio" => [
            "general" => "A transformational week. A major life decision may be on the horizon.",
            "love" => "Passion runs high. Deep emotional connections will grow.",
            "career" => "Stay focused and avoid workplace conflicts.",
            "health" => "Good week for detox and cleansing.",
            "lucky_numbers" => [8, 15, 27],
            "lucky_colors" => ["Black", "Maroon"]
        ],
        "sagittarius" => [
            "general" => "Adventure and learning will define your week. Be open to new experiences.",
            "love" => "A travel plan with a partner will bring excitement.",
            "career" => "You may be inspired to start a new project or business.",
            "health" => "Outdoor activities will refresh your mind.",
            "lucky_numbers" => [4, 9, 18],
            "lucky_colors" => ["Purple", "Turquoise"]
        ],
        "capricorn" => [
            "general" => "Patience and hard work will pay off this week.",
            "love" => "Emotional stability in relationships. A great week for commitment.",
            "career" => "Financial growth is on the horizon. Plan wisely.",
            "health" => "Take care of your bones and joints.",
            "lucky_numbers" => [5, 12, 26],
            "lucky_colors" => ["Brown", "Grey"]
        ],
        "aquarius" => [
            "general" => "An innovative week ahead. Your ideas will be well received.",
            "love" => "Unexpected romantic encounters will surprise you.",
            "career" => "Use technology to improve efficiency at work.",
            "health" => "Hydration and diet control are essential.",
            "lucky_numbers" => [7, 14, 25],
            "lucky_colors" => ["Blue", "Turquoise"]
        ],
        "pisces" => [
            "general" => "Trust your instincts. Intuition will guide you to success.",
            "love" => "A heart-to-heart talk will strengthen bonds.",
            "career" => "A creative breakthrough is coming.",
            "health" => "Focus on mental wellness. Try meditation.",
            "lucky_numbers" => [2, 6, 15],
            "lucky_colors" => ["Sea Green", "Lilac"]
        ]
    ];
    
    $data['weekly'] = $horoscopes[strtolower($zodiacSign)];
    
} else {
    $errors[] = "User not found";
}

// Set HTTP headers
header("Content-Type: application/json");
//header("Access-Control-Allow-Origin: same-origin");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Return JSON response
if (count($errors) > 0) {
    $response = array(
        "result" => "fail",
        "data" => $errors,
    );
} else {
    $response = array(
        "result" => "success",
        "data" => $data
    );
}

echo json_encode($response);


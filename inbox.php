<?php
require_once "header.php";

// Get current logged-in user ID from session
$currentUserId = $_SESSION['user_id'];

// Get the receiver user ID from URL parameter
$receiverId = isset($_GET['receiver']) ? $_GET['receiver'] : null;

// Fetch chat list (always needed for sidebar)
$chatQuery = "
    SELECT
        CASE
            WHEN sender_id = :currentUser THEN receiver_id
            ELSE sender_id
        END AS chat_partner,
        MAX(created_at) as last_message_time,
        (
            SELECT message
            FROM messages m2
            WHERE
                (m2.sender_id = :currentUser AND m2.receiver_id = chat_partner) OR
                (m2.sender_id = chat_partner AND m2.receiver_id = :currentUser)
            ORDER BY m2.created_at DESC
            LIMIT 1
        ) AS last_message
    FROM messages
    WHERE sender_id = :currentUser OR receiver_id = :currentUser
    GROUP BY chat_partner
    ORDER BY last_message_time DESC";
$stmt = $pdo->prepare($chatQuery);
$stmt->execute(['currentUser' => $currentUserId]);
$chatList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If receiver is selected, fetch that conversation
$messages = [];
if ($receiverId) {
    $messageQuery = "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at";
    $stmt = $pdo->prepare($messageQuery);
    $stmt->execute([$currentUserId, $receiverId, $receiverId, $currentUserId]);
    $messages = $stmt->fetchAll();
}

?>

    <div class="inbox_body">

        <!-- Sidebar -->
        <div class="inbox_sidebar">
            
            <div class="inbox_search-bar">
                <input type="text" placeholder="Search...">
            </div>

            <div class="chat-list">
                <?php foreach ($chatList as $chat): ?>
                    <?php
                    $otherUserId = $chat['chat_partner'];
                    $stmt = $pdo->prepare("SELECT firstName, lastName, profilePic FROM users WHERE userId = ?");
                    $stmt->execute([$otherUserId]);
                    $user = $stmt->fetch();
                    $isSelected = ($receiverId == $otherUserId) ? 'chatSelected' : '';
                    ?>
                    <a class="sideBarLink" href="inbox.php?receiver=<?= $otherUserId ?>">
                        <div class="chat-item <?= $isSelected ?>">
                            <img src="assets/images/avatar/<?= htmlspecialchars($user['profilePic']) ?>" alt="User">
                            <div>
                                <div class="name"><?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?></div>
                                <div class="preview"><?= htmlspecialchars(mb_strimwidth($chat['last_message'], 0, 30, '...')) ?></div>
                                <div class="time"><?= date("h:i A", strtotime($chat['last_message_time'])) ?></div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Chat Window -->
        <div class="chat-window">
            
            <?php if ($receiverId): ?>
            
                <!-- Chat Header -->
                <div class="chat-header">
                    <?php
                    $stmt = $pdo->prepare("SELECT firstName, lastName, profilePic FROM users WHERE userId = ?");
                    $stmt->execute([$receiverId]);
                    $receiver = $stmt->fetch();
                    ?>
                    <img src="assets/images/avatar/<?= htmlspecialchars($receiver['profilePic']) ?>" alt="<?=$receiver['firstName']?>" class="headerAvatar">
                    <div><?= htmlspecialchars($receiver['firstName'] . ' ' . $receiver['lastName']) ?></div>
                </div>

                <!-- Chat Messages -->
                <div class="chat-messages" id="chat-messages"></div>
            
                <div class="chat-input">
                    <form id="message-form">
                        <input type="hidden" name="receiver_id" value="<?= htmlspecialchars($receiverId) ?>">
                        <input type="text" name="message" placeholder="Type message here..." autocomplete="off" required>
                        <button type="submit">Send</button>
                    </form>
                </div>
            
            <?php else: ?>
            
                <!-- Placeholder -->
                <div class="chat-header">
                    <p>Select a chat from the left panel to start messaging.</p>
                </div>
            
                <!-- Chat Messages -->
                <div class="chat-messages" id="chat-messages"></div>

                <!-- Chat Input -->
                <div class="chat-input">
                    <form method="post" id="chatForm" action="">
                        <input type="hidden" name="receiver_id" value="">
                        <input type="text" name="message" id="message" placeholder="Type message here..." required>
                        <button type="submit">Send</button>
                    </form>
                </div>
            
            <?php endif; ?>
            
        </div>
    </div>

    <script>
        
        $(document).ready(function() {
            const receiverId = <?= isset($receiverId) ? (int)$receiverId : 'null' ?>;

            function scrollToBottom() {
                const chatMessages = document.getElementById('chat-messages');
                if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            function fetchMessages() {
                if (receiverId !== null) {
                    $.post('api/fetch_messages.php', { receiver_id: receiverId }, function(response) {
                        $('#chat-messages').html(response);
                        scrollToBottom();
                    });
                }
            }

            // Load + refresh
            fetchMessages();
            setInterval(fetchMessages, 5000);

            // Send message
            $('.chat-input form').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const message = form.find('input[name="message"]').val();
                if (!message.trim()) return;

                $.post('api/send_message.php', form.serialize(), function() {
                    form.find('input[name="message"]').val('');
                    fetchMessages();
                });
            });

            // Delete message (uses event delegation)
            $('#chat-messages').on('click', '.delete-msg-btn', function() {
                const messageId = $(this).data('id');
                if (confirm('Delete this message?')) {
                    $.post('api/delete_message.php', { message_id: messageId }, function(res) {
                        if (res === 'success') fetchMessages();
                    });
                }
            });

            // Search chat list
            $('.inbox_search-bar input').on('keyup', function() {
                const keyword = $(this).val().toLowerCase();
                $('.chat-list .chat-item').each(function() {
                    const name = $(this).find('.name').text().toLowerCase();
                    $(this).toggle(name.includes(keyword));
                });
            });
            
        });
    </script>

<?php require_once "footer.php"; ?>
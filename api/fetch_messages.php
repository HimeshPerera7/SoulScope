<?php
session_start();
require_once  "../includes/config.php";

$sender_id = $_SESSION['user_id'] ?? 0;
$receiver_id = $_POST['receiver_id'] ?? 0;

if ($sender_id && $receiver_id) {
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE
        ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
        AND deleted = 0
        ORDER BY created_at ASC
    ");
    $stmt->execute([$sender_id, $receiver_id, $receiver_id, $sender_id]);
    $messages = $stmt->fetchAll();
    
    foreach ($messages as $msg):
        $isSender = $msg['sender_id'] == $sender_id;
        ?>
        <div class="message-container <?= $isSender ? 'sentme' : 'receivedme' ?>">
            <div class="message <?= $isSender ? 'sent' : 'received' ?>">
                <div class="message-text">
                    <?= htmlspecialchars($msg['message']) ?>
                </div>
                <div class="message-data">
                    <span class="time"><?= date('h:i A', strtotime($msg['created_at'])) ?></span>
                    <?php if ($isSender): ?>
                        <button class="delete-msg-btn" data-id="<?= $msg['id'] ?>" title="Delete">🗑</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php
    endforeach;
}

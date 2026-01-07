<?php
require_once __DIR__ . '/../frontend/header.php';
?>

<div class="container mx-auto p-4">
    <div class="glass p-8 max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold mb-4"><?php echo translate('chatbot'); ?></h2>
        <div id="chat-container" class="h-96 overflow-y-auto mb-4 p-4 glass">
            <!-- Chat messages will appear here -->
        </div>
        <div class="flex">
            <input type="text" id="chat-input" placeholder="<?php echo translate('type_message'); ?>" class="glass p-2 flex-grow">
            <button onclick="sendMessage()" class="glass p-2 ml-2"><?php echo translate('send'); ?></button>
        </div>
    </div>
</div>

<script>
async function sendMessage() {
    const input = document.getElementById('chat-input');
    const container = document.getElementById('chat-container');
    const message = input.value.trim();
    if (!message) return;

    // Add user message
    container.innerHTML += `<p class="text-right"><strong>You:</strong> ${message}</p>`;
    input.value = '';

    // Get bot response
    const response = await sendChatMessage(message);
    container.innerHTML += `<p><strong>Bot:</strong> ${response}</p>`;
    container.scrollTop = container.scrollHeight;
}
</script>

<?php require_once __DIR__ . '/../frontend/footer.php'; ?>
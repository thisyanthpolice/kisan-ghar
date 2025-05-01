<?php
require_once 'header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}
?>

<div class="container mx-auto p-4">
    <div class="glass p-8 max-w-5xl mx-auto">
        <h2 class="text-2xl font-bold mb-4"><?php echo translate('nutrition_coach'); ?></h2>
        <div id="chat-container" class="h-[500px] overflow-y-auto mb-4 p-4 glass">
            <div class="welcome-message glass p-4 mb-4 rounded-lg">
                <p class="font-semibold">👋 Welcome to your Nutrition Coach!</p>
                <p class="text-sm mt-2">I can help you with:</p>
                <ul class="text-sm mt-2 list-disc list-inside">
                    <li>Personalized diet recommendations</li>
                    <li>Nutritional information about foods</li>
                    <li>Healthy meal planning</li>
                    <li>Diet and exercise routines</li>
                    <li>General health and wellness advice</li>
                </ul>
            </div>
            <div id="messages" class="space-y-4"></div>
        </div>
        <div class="flex gap-2">
            <input type="text" id="user-input" 
                   placeholder="<?php echo translate('ask_nutrition_coach'); ?>" 
                   class="glass p-2 flex-grow rounded-lg">
            <button onclick="sendMessage()" id="send-button"
                    class="glass px-6 py-2 text-blue-600 hover:text-blue-700 transition-colors rounded-lg">
                <?php echo translate('send'); ?>
            </button>
        </div>
        <div id="error-message" class="mt-4 text-red-500 hidden"></div>
    </div>
</div>

<script>
const API_KEY = '';//PLACE YOUR API KEY HERE
const messagesContainer = document.getElementById('messages');
const userInput = document.getElementById('user-input');
const chatContainer = document.getElementById('chat-container');
const sendButton = document.getElementById('send-button');
const errorMessageDiv = document.getElementById('error-message');

// Initialize chat context
let chatContext = `You are a knowledgeable and supportive nutrition coach 🥗. Your primary focus is helping people achieve their health and wellness goals through sustainable nutrition and lifestyle changes.

When discussing  emphasize these key principles:
dont show the * asterisk  when giving answer and dont show the pre txt and post text 

🌱 Sustainable Approach:
- Focus on long-term lifestyle changes over quick fixes
- Promote balanced, nutrient-rich eating patterns
- Encourage mindful eating practices

💪 Healthy Weight Loss Guidelines:
- Recommend a safe rate of weight loss (0.5-1 kg per week)
- Emphasize the importance of balanced nutrition
- Discourage extreme dieting or restrictive eating

🍎 Dietary Recommendations:
- Promote whole, unprocessed foods
- Encourage adequate protein intake
- Suggest appropriate portion sizes
- Include plenty of fruits and vegetables
- Stay hydrated with water

🏃‍♂️ Lifestyle Factors:
- Stress the importance of regular physical activity
- Discuss sleep quality and stress management
- Encourage building healthy habits gradually

⚕️ Safety First:
- Always recommend consulting healthcare providers before starting any diet
- Consider individual health conditions and limitations
- Provide evidence-based nutrition information

Remember to be:
1. Supportive and encouraging
2. Focused on sustainable changes
3. Evidence-based in your recommendations
4. Mindful of individual differences
5. Clear about the importance of balanced nutrition`;

let isProcessing = false;

async function sendMessage() {
    if (isProcessing) return;
    
    const message = userInput.value.trim();
    if (!message) return;

    // Reset error message
    errorMessageDiv.classList.add('hidden');
    errorMessageDiv.textContent = '';

    // Add user message to chat
    appendMessage('user', message);
    userInput.value = '';

    // Prepare loading indicator
    const loadingId = appendLoading();
    isProcessing = true;
    sendButton.disabled = true;

    try {
        const response = await fetch('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' + API_KEY, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                contents: [{
                    parts: [{
                        text: chatContext + "\n\nUser: " + message
                    }]
                }]
            })
        });

        if (!response.ok) {
            throw new Error(`API Error: ${response.status} ${response.statusText}`);
        }

        const data = await response.json();
        
        if (data.candidates && data.candidates[0].content && data.candidates[0].content.parts && data.candidates[0].content.parts[0].text) {
            appendMessage('bot', data.candidates[0].content.parts[0].text);
        } else {
            throw new Error('Unexpected API response format');
        }
    } catch (error) {
        console.error('Error:', error);
        errorMessageDiv.textContent = 'Sorry, there was an error connecting to the nutrition coach. Please try again later.';
        errorMessageDiv.classList.remove('hidden');
        appendMessage('bot', 'I apologize, but I encountered a technical issue. Please try again later.');
    } finally {
        // Remove loading indicator and reset state
        document.getElementById(loadingId)?.remove();
        isProcessing = false;
        sendButton.disabled = false;
    }

    chatContainer.scrollTop = chatContainer.scrollHeight;
}

function appendMessage(sender, message) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `glass p-4 rounded-lg ${sender === 'user' ? 'ml-8' : 'mr-8'}`;
    messageDiv.innerHTML = `
        <div class="flex items-start gap-2">
            <span class="text-xl">${sender === 'user' ? '👤' : '🥗'}</span>
            <div class="flex-grow">
                <p class="font-semibold mb-1">${sender === 'user' ? 'You' : 'Nutrition Coach'}</p>
                <p>${message.replace(/\n/g, '<br>')}</p>
            </div>
        </div>
    `;
    messagesContainer.appendChild(messageDiv);
}

function appendLoading() {
    const id = 'loading-' + Date.now();
    const loadingDiv = document.createElement('div');
    loadingDiv.id = id;
    loadingDiv.className = 'glass p-4 rounded-lg mr-8';
    loadingDiv.innerHTML = `
        <div class="flex items-center gap-2">
            <span class="text-xl">🥗</span>
            <div class="flex-grow">
                <p class="font-semibold">Nutrition Coach</p>
                <p>Thinking...</p>
            </div>
        </div>
    `;
    messagesContainer.appendChild(loadingDiv);
    return id;
}

// Event Listeners
userInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

// Disable button while processing
sendButton.addEventListener('click', () => {
    if (!isProcessing) {
        sendMessage();
    }
});

// Clear error message when user starts typing
userInput.addEventListener('input', () => {
    errorMessageDiv.classList.add('hidden');
    errorMessageDiv.textContent = '';
});
</script>

<?php require_once 'footer.php'; ?>
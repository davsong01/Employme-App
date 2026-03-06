<style>
#close-ai {
    background: transparent;       /* No background to match header */
    border: none;                  /* Remove default button border */
    color: #fff;                   /* White text to match header */
    font-size: 16px;               /* Slightly bigger for visibility */
    font-weight: bold;             /* Make the ✖ stand out */
    cursor: pointer;               /* Show pointer on hover */
    line-height: 1;                /* Fix vertical alignment */
    padding: 0 6px;                /* Small horizontal padding */
    border-radius: 4px;            /* Slight rounding */
    transition: background 0.2s;   /* Smooth hover effect */
}

#close-ai:hover {
background: rgba(255, 255, 255, 0.2);  /* Slight white overlay on hover */
}
#portal-ai-button {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #28a745;
    color: #fff;
    padding: 14px 18px;
    border-radius: 50px;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0,0,0,.2);
    z-index: 9999;
}

#portal-ai-chatbox {
    position: fixed;
    bottom: 80px;
    right: 20px;
    width: 320px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,.3);
    z-index: 9999;
    display: flex;
    flex-direction: column;
}

.hidden {
    display: none;
}

.ai-header {
    background: #28a745;
    color: #fff;
    padding: 10px;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
}

#ai-messages {
    padding: 10px;
    height: 250px;
    overflow-y: auto;
    font-size: 14px;
}

.ai-input {
    display: flex;
    border-top: 1px solid #ddd;
}

.ai-input input {
    flex: 1;
    padding: 10px;
    border: none;
}

.ai-input button {
    background: #28a745;
    color: white;
    border: none;
    padding: 10px 15px;
}
</style>

<div id="portal-ai-button">
    🤖 Ask Portal AI
</div>

<div id="portal-ai-chatbox" class="hidden">
    <div class="ai-header">
        <span>Portal AI Tutor</span>
        <button id="close-ai">✖</button>
    </div>

    <div id="ai-messages"></div>

    <div class="ai-input">
        <input type="text" id="ai-question" placeholder="Ask about this study material..." />
        <button id="send-ai">Send</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const aiButton = document.getElementById('portal-ai-button');
    const chatBox = document.getElementById('portal-ai-chatbox');
    const closeBtn = document.getElementById('close-ai');
    const sendBtn = document.getElementById('send-ai');

    if (aiButton) {
        aiButton.addEventListener('click', function () {
            chatBox.classList.toggle('hidden');
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            chatBox.classList.add('hidden');
        });
    }

    if (sendBtn) {
        sendBtn.addEventListener('click', sendQuestion);
    }

    function sendQuestion() {
        const questionInput = document.getElementById('ai-question');
        const question = questionInput.value.trim();
        if (!question) return;

        const messages = document.getElementById('ai-messages');
        messages.innerHTML += "<div><b>You:</b> " + question + "</div>";

        fetch("/ai/ask", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                question: question,
                program_id: 116,
                use_case: "module1" // hardcoded, no comma after last property
            })
        })
        .then(function(response){ return response.json(); })
        .then(function(data){
            messages.innerHTML += "<div><b>AI:</b> " + data.answer + "</div>";
            questionInput.value = "";
        })
        .catch(function(error){
            console.error(error);
            messages.innerHTML += "<div style='color:red'>AI error occurred</div>";
        });
    }
});
</script>
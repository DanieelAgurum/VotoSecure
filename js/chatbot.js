document.querySelector(".chat-send-btn").addEventListener("click", sendMessage);
const micBtn = document.getElementById("micBtn");

let recognition;
let isListening = false;

if ('webkitSpeechRecognition' in window) {
    recognition = new webkitSpeechRecognition();
    recognition.lang = "es-MX";
    recognition.continuous = false;
    recognition.interimResults = false;

    micBtn.addEventListener("click", () => {
        if (!isListening) {
            recognition.start();
        }
    });

    recognition.onstart = () => {
        isListening = true;
        micBtn.classList.add("recording");

        showListeningIndicator();
    };

    recognition.onend = () => {
        isListening = false;
        micBtn.classList.remove("recording");

        removeListeningIndicator();
    };

    let listeningIndicator = null;

    function showListeningIndicator() {
        listeningIndicator = document.createElement("div");
        listeningIndicator.classList.add("chat-message", "bot", "listening");
        listeningIndicator.innerHTML = "Escuchando...";
        document.querySelector(".chat-body").appendChild(listeningIndicator);
        scrollChat();
    }

    function removeListeningIndicator() {
        if (listeningIndicator) {
            listeningIndicator.remove();
            listeningIndicator = null;
        }
    }

    let transcribingIndicator = null;

    function showTranscribingIndicator() {
        transcribingIndicator = document.createElement("div");
        transcribingIndicator.classList.add("chat-message", "bot", "transcribing");
        transcribingIndicator.innerHTML = "Transcribiendo audio...";
        document.querySelector(".chat-body").appendChild(transcribingIndicator);
        scrollChat();
    }

    function removeTranscribingIndicator() {
        if (transcribingIndicator) {
            transcribingIndicator.remove();
            transcribingIndicator = null;
        }
    }

    recognition.onresult = function (event) {
        removeListeningIndicator();
        showTranscribingIndicator();

        const transcript = event.results[0][0].transcript;

        setTimeout(() => {
            removeTranscribingIndicator();
            document.querySelector(".chat-footer input").value = transcript;
            sendMessage();
        }, 800);
    };

    recognition.onerror = function (event) {
        console.error("Error reconocimiento:", event.error);
        isListening = false;
    };

    function scrollChat() {
        const chatBody = document.querySelector(".chat-body");
        chatBody.scrollTop = chatBody.scrollHeight;
    }


} else {
    console.warn("SpeechRecognition no soportado en este navegador.");
}

let typingIndicator = null;

function showTyping() {
    typingIndicator = document.createElement("div");
    typingIndicator.classList.add("chat-message", "bot", "typing");

    typingIndicator.innerHTML = `
        <div class="message-bubble">
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
        </div>
    `;

    document.querySelector(".chat-body").appendChild(typingIndicator);
    scrollChat();
}

function removeTyping() {
    if (typingIndicator) {
        typingIndicator.remove();
        typingIndicator = null;
    }
}

function sendMessage() {
    const input = document.querySelector(".chat-footer input");
    const message = input.value.trim();
    if (!message) return;

    addMessage(message, "user");
    input.value = "";

    showTyping();

    fetch("/VotoSecure/Controlador/chatbotControl.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "message=" + encodeURIComponent(message)
    })
        .then(res => res.json())
        .then(data => {
            removeTyping();
            addMessage(data.response, "bot");
        })
        .catch(err => {
            removeTyping();
            console.error(err);
        });
}

function addMessage(text, type) {
    const div = document.createElement("div");
    div.classList.add("chat-message", type);
    div.textContent = text;
    document.querySelector(".chat-body").appendChild(div);
    scrollChat();
}
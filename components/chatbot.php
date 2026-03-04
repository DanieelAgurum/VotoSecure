<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/VotoSecure/obtenerLink/obtenerLink.php';
$urlBase = getBaseUrl();
?>
<!-- BOTÓN FLOTANTE CHAT -->
<button id="chatToggle" class="chat-float-btn"
    data-bs-toggle="tooltip" data-bs-placement="top"
    data-bs-custom-class="custom-tooltip"
    data-bs-title="Asistente virtual">
    <i class="bi bi-chat-text-fill"></i>
</button>

<!-- PANEL CHATBOT -->
<div id="chatPanel" class="chat-panel">
    <div class="chat-header">
        <span>Asistente Virtual</span>
        <button id="chatClose" class="chat-close" data-bs-toggle="tooltip"
            data-bs-placement="top" title="Cerrar">&times;</button>
    </div>

    <div class="chat-body">
        <div class="chat-message bot">
            Hola 👋 ¿En qué puedo ayudarte hoy?
        </div>
    </div>

    <div class="chat-footer">
        <input type="text" placeholder="Escribe un mensaje..." />
        <button id="micBtn" class="mic-btn" data-bs-toggle="tooltip"
            data-bs-placement="top" title="Hablar">
            <i class="bi bi-mic-fill"></i>
        </button>
        <button class="chat-send-btn" data-bs-toggle="tooltip"
            data-bs-placement="top" title="Enviar mensaje">
            <i class="bi bi-send-fill"></i>
        </button>
    </div>
</div>

<script src="<?= $urlBase ?>js/chatbot.js"></script>
<script src="<?= $urlBase ?>js/abrirChatbot.js"></script>
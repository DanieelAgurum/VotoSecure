<?php

class ChatService
{
    private $intents;

    public function __construct()
    {
        $this->intents = include __DIR__ . '/config/chatbot.php';
        if (!is_array($this->intents)) {
            die("Error interno del chatbot.");
        }
    }

    public function processMessage($message)
    {
        $message = $this->normalize($message);
        require_once __DIR__ . '/config/openAi.php';
        $openAI = new OpenAIService();
        $intentDetectado = trim(strtolower($openAI->detectIntent($message)));

        foreach ($this->intents as $intentKey => $intentData) {
            if ($intentKey === $intentDetectado) {
                $responses = $intentData['responses'];
                return $responses[array_rand($responses)];
            }
        }
        return "Lo siento, solo puedo ayudarte con información relacionada a la plataforma VotoSecure.";
    }

    private function normalize($text)
    {
        $text = strtolower(trim($text));

        $acentos = [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'Á' => 'a',
            'É' => 'e',
            'Í' => 'i',
            'Ó' => 'o',
            'Ú' => 'u',
            'ñ' => 'n',
            'Ñ' => 'n'
        ];

        return strtr($text, $acentos);
    }
}

<?php

class OpenAIService
{
    private $apiKey = "";

    public function detectIntent($message)
    {
        $data = [
            "model" => "gpt-4o-mini",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "Eres un clasificador de intenciones para la plataforma VotoSecure.
                    Analiza el mensaje del usuario y responde únicamente con UNA de las siguientes palabras exactas:
                    saludo
                    informacion_general
                    elecciones_activas
                    candidatos
                    propuestas
                    proceso_tarjeta
                    como_votar
                    donde_votar
                    resultados
                    seguridad
                    ayuda_faq
                    problemas_tecnicos
                    despedida
                    fuera_de_contexto
                    
                    Reglas:
                    - Devuelve solo una palabra.
                    - No expliques.
                    - No uses mayúsculas.
                    - No agregues puntuación.
                    - Si no está relacionado con VotoSecure, responde: fuera_de_contexto."
                ],
                [
                    "role" => "user",
                    "content" => $message
                ]
            ],
            "temperature" => 0
        ];

        $ch = curl_init("https://api.openai.com/v1/chat/completions");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        return trim($result['choices'][0]['message']['content'] ?? '');
    }
}

<?php

return [

    "saludo" => [
        "description" => "Saludo inicial del usuario",
        "examples" => [
            "hola",
            "buenas",
            "que tal",
            "hey",
            "buen día"
        ],
        "responses" => [
            "Hola 👋 Soy el asistente virtual de VotoSecure, ¿En qué puedo ayudarte?",
            "¡Hola! Estoy aquí para ayudarte con el proceso de votación.",
            "Bienvenido a VotoSecure 🗳️ ¿En qué puedo ayudarte hoy?"
        ],
        "faq" => [
            "question" => "¿Cómo puedo usar el chatbot?",
            "answer" => "Simplemente escribe tu pregunta relacionada con la plataforma y te responderé lo antes posible."
        ]
    ],

    "informacion_general" => [
        "description" => "Información general sobre la plataforma",
        "examples" => [
            "que es votosecure",
            "para que sirve la plataforma",
            "que hace esta pagina",
            "de que trata el sistema"
        ],
        "responses" => [
            "VotoSecure es una plataforma digital diseñada para gestionar procesos electorales de forma segura y transparente.",
            "La plataforma centraliza información de elecciones, candidatos y resultados."
        ],
        "faq" => [
            "question" => "¿Qué es VotoSecure?",
            "answer" => "Es un sistema digital para gestionar elecciones de manera segura, organizada y con verificación biométrica."
        ]
    ],

    "elecciones_activas" => [
        "description" => "Consultar elecciones disponibles",
        "examples" => [
            "elecciones activas",
            "hay votaciones abiertas",
            "que elecciones estan disponibles",
            "ver elecciones"
        ],
        "responses" => [
            "Puedes consultar las elecciones activas en el módulo 'Elecciones' del menú principal."
        ],
        "faq" => [
            "question" => "¿Cómo veo las elecciones disponibles?",
            "answer" => "Ingresa al menú principal y selecciona el módulo 'Elecciones' para consultar las votaciones activas."
        ]
    ],

    "candidatos" => [
        "description" => "Consultar lista de candidatos",
        "examples" => [
            "ver candidatos",
            "lista de candidatos",
            "quienes participan",
            "quien esta en la eleccion"
        ],
        "responses" => [
            "Dentro de cada elección puedes consultar la lista completa de candidatos."
        ],
        "faq" => [
            "question" => "¿Dónde puedo ver los candidatos?",
            "answer" => "Selecciona una elección y entra al apartado 'Candidatos' para ver la información de cada participante."
        ]
    ],

    "propuestas" => [
        "description" => "Consultar propuestas de candidatos",
        "examples" => [
            "propuestas",
            "que propone el candidato",
            "planes de trabajo",
            "ver propuesta"
        ],
        "responses" => [
            "Selecciona un candidato y haz clic en 'Ver Propuesta' para conocer su plan de trabajo."
        ],
        "faq" => [
            "question" => "¿Cómo veo las propuestas?",
            "answer" => "En el perfil del candidato encontrarás su plan de trabajo y propuestas detalladas."
        ]
    ],

    "como_votar" => [
        "description" => "Proceso para emitir voto",
        "examples" => [
            "como votar",
            "proceso de votacion",
            "como emito mi voto",
            "como funciona el voto"
        ],
        "responses" => [
            "El día de la elección debes acudir al centro asignado con tu tarjeta de registro, seleccionar tu candidato y confirmar con tu huella digital."
        ],
        "faq" => [
            "question" => "¿Cuál es el proceso para votar?",
            "answer" => "Presenta tu tarjeta de registro, accede a la casilla asignada, elige tu candidato y valida tu voto con tu huella digital."
        ]
    ],

    "donde_votar" => [
        "description" => "Información sobre centro de votación",
        "examples" => [
            "donde votar",
            "donde emito mi voto",
            "en que seccion voto"
        ],
        "responses" => [
            "Debes acudir al centro de votación asignado según tu sección electoral."
        ],
        "faq" => [
            "question" => "¿Dónde debo votar?",
            "answer" => "En el centro correspondiente a tu sección electoral, indicado en tu tarjeta de registro."
        ]
    ],

    "resultados" => [
        "description" => "Consultar resultados",
        "examples" => [
            "resultados",
            "quien gano",
            "resultados de la eleccion",
            "ver resultados"
        ],
        "responses" => [
            "Los resultados se publican en tiempo real dentro de la plataforma."
        ],
        "faq" => [
            "question" => "¿Cómo consulto los resultados?",
            "answer" => "En el módulo 'Resultados' podrás ver el conteo en tiempo real por sección y candidato."
        ]
    ],

    "seguridad" => [
        "description" => "Seguridad del sistema",
        "examples" => [
            "es seguro votar aqui",
            "mi voto es anonimo",
            "seguridad del sistema",
            "mi informacion esta protegida"
        ],
        "responses" => [
            "VotoSecure protege la identidad del votante mediante autenticación biométrica y validación por tarjeta."
        ],
        "faq" => [
            "question" => "¿Mi voto es seguro y anónimo?",
            "answer" => "Sí, el sistema utiliza autenticación biométrica y mecanismos de seguridad que garantizan confidencialidad e integridad."
        ]
    ],

    "problemas_tecnicos" => [
        "description" => "Errores o fallas técnicas",
        "examples" => [
            "error en la pagina",
            "no carga",
            "tengo un problema",
            "fallo del sistema"
        ],
        "responses" => [
            "Si presentas un inconveniente técnico, informa al personal autorizado o administrador del sistema."
        ]
    ],

    "despedida" => [
        "description" => "Despedida del usuario",
        "examples" => [
            "gracias",
            "adios",
            "hasta luego",
            "nos vemos"
        ],
        "responses" => [
            "¡Con gusto! 😊 Estoy aquí para ayudarte cuando lo necesites.",
            "Gracias por utilizar VotoSecure."
        ]
    ],

    "fuera_de_contexto" => [
        "description" => "Preguntas fuera del sistema",
        "examples" => [],
        "responses" => [
            "Solo puedo ayudarte con información relacionada con la plataforma VotoSecure.",
            "Puedo asistirte con dudas sobre elecciones, candidatos y resultados."
        ],
        "faq" => [
            "question" => "¿Sobre qué temas puede ayudar el chatbot?",
            "answer" => "El asistente solo responde preguntas relacionadas con VotoSecure y el proceso electoral."
        ]
    ]
];

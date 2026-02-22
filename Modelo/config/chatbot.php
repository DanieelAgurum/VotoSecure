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
            "Hola 👋 Soy el asistente virtual de VotoSecure, ¿En que puedo ayudarte?",
            "¡Hola! Estoy aquí para ayudarte con el proceso de votación.",
            "Bienvenido a VotoSecure 🗳️ ¿En qué puedo ayudarte hoy?"
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
            "VotoSecure es una plataforma digital diseñada para gestionar procesos electorales de forma segura, transparente y organizada.",
            "La plataforma centraliza la información relacionada con los procesos electorales, incluyendo candidatos y sus propuestas."
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
            "Puedes consultar las elecciones activas en el módulo 'Elecciones' del menú principal.",
            "Dirígete a la sección 'Elecciones' para ver las votaciones disponibles."
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
            "Dentro de cada elección puedes consultar la lista completa de candidatos.",
            "En el apartado 'Candidatos' podrás ver la información de cada participante."
        ]
    ],

    "propuestas" => [
        "description" => "Consultar propuestas de los candidatos",
        "examples" => [
            "propuestas",
            "que propone el candidato",
            "planes de trabajo",
            "ver propuesta"
        ],
        "responses" => [
            "Selecciona un candidato y haz clic en 'Ver Propuesta' para conocer su plan de trabajo.",
            "Las propuestas están disponibles en el perfil de cada partido o candidato."
        ]
    ],

    "proceso_tarjeta" => [
        "description" => "Proceso para obtener la tarjeta de registro",
        "examples" => [
            "como obtengo mi tarjeta",
            "tramitar tarjeta",
            "donde saco mi tarjeta",
            "como consigo mi tarjeta de registro",
            "proceso para obtener tarjeta"
        ],
        "responses" => [
            "Para obtener tu tarjeta de registro, debes acudir al módulo de registro autorizado, proporcionar tus datos personales y realizar el registro biométrico correspondiente.",
            "La tarjeta de registro se tramita previamente a la jornada electoral. Deberás completar tu registro, validar tu identidad y realizar la captura de tu huella digital."
        ]
    ],

    "como_votar" => [
        "description" => "Explicación del proceso de votación",
        "examples" => [
            "como votar",
            "proceso de votacion",
            "como emito mi voto",
            "como funciona el voto"
        ],
        "responses" => [
            "El día oficial de la elección deberás acudir al centro de votación con tu tarjeta de registro, la cual te dará acceso a la casilla correspondiente. Posteriormente, selecciona al candidato de tu preferencia y confirma tu voto mediante la verificación de tu huella digital.",
            "Para votar, el día de la elección preséntate en el centro de votación con tu tarjeta de registro. Una vez dentro de la casilla, elige al candidato y autentícate con tu huella digital para emitir tu voto."
        ]
    ],

    "donde_votar" => [
        "description" => "Información sobre dónde votar",
        "examples" => [
            "donde votar",
            "donde emito mi voto",
            "en que seccion voto"
        ],
        "responses" => [
            "Debes acudir al centro de votación que te fue asignado según tu sección electoral. Tu tarjeta de registro indicará la sección correspondiente y te permitirá acceder a la casilla.",
            "La votación se realiza de manera presencial en el centro asignado a tu sección. Presenta tu tarjeta de registro para ingresar a la casilla correspondiente."
        ]
    ],

    "resultados" => [
        "description" => "Consultar resultados de elecciones",
        "examples" => [
            "resultados",
            "quien gano",
            "resultados de la eleccion",
            "ver resultados"
        ],
        "responses" => [
            "Los resultados se publican en tiempo real dentro de VotoSecure, mostrando el conteo por sección y por candidato.",
            "Puedes consultar los resultados en la plataforma durante la jornada electoral, donde se actualizan en tiempo real por sección y candidato."
        ]
    ],

    "seguridad" => [
        "description" => "Información sobre la seguridad del sistema",
        "examples" => [
            "es seguro votar aqui",
            "mi voto es anonimo",
            "seguridad del sistema",
            "mi informacion esta protegida"
        ],
        "responses" => [
            "VotoSecure garantiza la confidencialidad del voto mediante autenticación biométrica con huella digital y validación mediante tarjeta de registro.",
            "El sistema protege la identidad del votante y asegura la integridad del proceso electoral mediante mecanismos de verificación y control por sección."
        ]
    ],

    "ayuda_faq" => [
        "description" => "Preguntas frecuentes y ayuda",
        "examples" => [
            "preguntas frecuentes",
            "ayuda",
            "tengo dudas",
            "faq"
        ],
        "responses" => [
            "Puedes consultar la sección 'Ayuda' donde encontrarás preguntas frecuentes.",
            "En el módulo 'Ayuda' están disponibles respuestas a las dudas más comunes."
        ]
    ],

    "problemas_tecnicos" => [
        "description" => "Errores o fallas técnicas",
        "examples" => [
            "error en la pagina",
            "no carga",
            "tengo un problema",
            "fallo del sistema",
            "no puedo votar"
        ],
        "responses" => [
            "Si presentas un inconveniente con la verificación de huella digital o la validación de tu tarjeta de registro, informa al personal autorizado en el centro de votación.",
            "Ante cualquier falla técnica en el sistema o en la visualización de resultados, notifica al administrador o responsable del proceso electoral."
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
            "Gracias por utilizar VotoSecure. Estamos para apoyarte en lo que necesites."
        ]
    ],

    "fuera_de_contexto" => [
        "description" => "Preguntas fuera del sistema",
        "responses" => [
            "Solo puedo ayudarte con información relacionada con la plataforma VotoSecure y el proceso electoral.",
            "Puedo asistirte con dudas sobre elecciones, candidatos, resultados y funcionamiento del sistema VotoSecure."
        ]
    ]

];


# 🥗 FitFoodChallenge

**FitFoodChallenge** es una plataforma web diseñada para motivar a los jóvenes a realizar actividades físicas y adoptar hábitos nutricionales básicos, fomentando el autocuidado y un estilo de vida saludable.

---

## 🚀 Objetivo

El proyecto busca promover el bienestar físico y mental mediante mini retos diarios de movimiento, seguimiento de progreso, registro de hábitos saludables y una interfaz moderna e intuitiva.

---

## 🏗️ Arquitectura del Proyecto (MVC)

El proyecto sigue el patrón **Modelo-Vista-Controlador (MVC)** para mantener una estructura clara y escalable.

```
fitfoodchallenge/
├── app/
│   ├── Config/              # Configuración general y conexión a la base de datos
│   ├── Controllers/         # Controladores principales (lógica de cada módulo)
│   │   ├── HomeController.php
│   │   ├── ActividadesController.php
│   │   ├── HabitosController.php
│   │   ├── ProgresoController.php
│   │   └── CuentaController.php
│   ├── Core/                # Núcleo del framework (Router, Request, Response, etc.)
│   ├── Models/              # Modelos que representan las entidades del sistema
│   ├── Repositories/        # Acceso a datos y consultas SQL específicas
│   ├── Services/            # Lógica de negocio (ej: cálculo de progreso)
│   └── Views/               # Vistas HTML/PHP (interfaz del usuario)
│       ├── layouts/
│       │   └── main.php     # Layout base (header, footer, estilos)
│       └── home/
│           ├── index.php
│           ├── actividades.php
│           ├── habitos.php
│           ├── progreso.php
│           └── miCuenta.php
│
├── config/
│   ├── bootstrap.php        # Autoload, carga de helpers, configuración global
│   ├── helpers.php          # Funciones de ayuda (url(), view(), env(), etc.)
│   └── routes.php           # Definición de rutas y controladores
│
├── database/
│   └── migrations/
│       └── 0001_initial.sql # Estructura inicial de la base de datos
│
├── public/                  # Carpeta pública (servida por el navegador)
│   ├── assets/
│   │   ├── css/
│   │   │   └── main.css     # Estilos principales
│   │   └── js/
│   │       └── main.js      # Scripts del front-end
│   ├── .htaccess            # Reescritura de URLs para rutas limpias
│   └── index.php            # Punto de entrada del sistema
│
├── storage/
│   └── logs/                # Archivos de logs y temporales
│
├── .env.example             # Variables de entorno (base de datos, APP_URL)
├── .gitignore               # Archivos y carpetas que no se suben a Git
└── README.md
```

---

## 🧰 Tecnologías Utilizadas

| Categoría | Tecnología |
|------------|-------------|
| Lenguaje principal | PHP 8+ |
| Servidor local | WAMP / Apache |
| Base de datos | MySQL |
| Frontend | HTML5, CSS3, JavaScript |
| Librerías | Google Fonts (Poppins / Inter) |
| Arquitectura | MVC (Modelo-Vista-Controlador) |
| Control de versiones | Git + GitHub |

---

## ⚙️ Instalación local

### 1️⃣ Clona el repositorio
```bash
git clone https://github.com/tuUsuario/fitfoodchallenge.git
cd fitfoodchallenge
```

### 2️⃣ Configura el entorno
Copia el archivo `.env.example` y renómbralo a `.env`, luego edita tus valores:

```bash
APP_URL=http://localhost/fitfoodchallenge/public
DB_HOST=127.0.0.1
DB_NAME=fitfood_db
DB_USER=root
DB_PASS=
```

### 3️⃣ Importa la base de datos
Ejecuta en **phpMyAdmin** o consola:
```sql
SOURCE database/migrations/0001_initial.sql;
```

### 4️⃣ Inicia WAMP o tu servidor local
Luego visita:

👉 [http://localhost/fitfoodchallenge/public](http://localhost/fitfoodchallenge/public)

---

## 🧩 Funcionalidades (en progreso)

- [x] Estructura MVC funcional con router y vistas dinámicas  
- [x] Navegación limpia con URLs amigables  
- [x] Página principal con diseño moderno  
- [ ] Registro de progreso diario (actividad y agua)  
- [ ] Panel de hábitos saludables  
- [ ] Gestión de cuenta de usuario  
- [ ] Integración con base de datos MySQL  

---

## 👨‍💻 Autor

**Eric Diego Hernández**  
Proyecto académico de autocuidado y bienestar.  
Hecho con ❤️ usando PHP, CSS y buenas prácticas de desarrollo web.

---

## 📜 Licencia

Este proyecto está bajo la licencia **MIT**, por lo que puedes usarlo, modificarlo y distribuirlo libremente dando el crédito correspondiente.


### Instalación con Docker
```bash
docker compose up -d
# App: http://localhost:8080
# phpMyAdmin: http://localhost:8081

# Wikipedia Search Application

Pequeña aplicación web (SPA) que permite buscar términos en la API de Wikipedia, mostrando los resultados en tiempo real y almacenando el historial de búsquedas de forma segura en una base de datos PostgreSQL, asociándolo a la sesión del usuario sin necesidad de autenticación (Login).

## 🚀 Características Técnicas
* **Frontend:** HTML5, CSS3 (Responsivo) y Vanilla JS.
* **Backend:** PHP Nativo .
* **Base de Datos:** PostgreSQL y Docker.
---

## 🛠️ Requisitos Previos
* **Docker** y **Docker Compose**
* **PHP 8.x** (con la extensión `pdo_pgsql` habilitada)

---

## 💻 Instalación y Despliegue

### 1. Levantar la Base de Datos (PostgreSQL)
En la raíz del proyecto, ejecutad el siguiente comando para levantar el contenedor de la base de datos:
```
docker compose up -d

```

*Nota: El contenedor ejecutará automáticamente el script ubicado en `/backend/scripts/create-table.sql` para inicializar la tabla necesaria.*

### 2. Levantar el Servidor Web (PHP)

Para ejecutar la aplicación localmente, inicia el servidor embebido de PHP apuntando a la raíz del proyecto:

```
php -S localhost:8000

```


---

## 📐 Decisiones de Diseño

* **Optimización y Rendimiento (Debounce):** En el frontend he implementado un mecanismo de *Debounce* (200ms) al capturar el evento de entrada del usuario (`input`). Para limitar, las peticiones, de esta forma, no se hace una petición por cada letra, evitando saturar la API pública de Wikipedia con peticiones innecesarias.

* **Seguridad en el Cliente (Mitigación XSS):** Para la renderización de los títulos y los elementos del historial, se manipula el DOM de forma segura utilizando la propiedad `textContent` en lugar de `innerHTML`. De esta manera, se sanitizan los datos recibidos y se inmuniza la aplicación contra vulnerabilidades de Cross-Site Scripting (XSS).

* **Historial sin Autenticación:** He implementado el uso de `session_id()` de PHP para identificar de forma unívoca el navegador del usuario. De este modo, el historial es persistente y privado para cada usuario sin obligarle a registrarse.

* **Separación de Responsabilidades:** El Frontend actúa como una SPA pura comunicándose de forma asíncrona mediante `fetch` con los endpoints de PHP (`save_history.php` y `get_history.php`), manteniendo desacoplada la lógica de persistencia de la interfaz de usuario.
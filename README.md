# EMCaptcha

[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Paquete Laravel para generar **captchas de operaciones aritméticas** (suma, resta, multiplicación) como imágenes PNG. Compatible con **Laravel 10, 11 y 12**.

---

## Características

- ✅ Operaciones aritméticas: suma, resta y multiplicación (solo enteros, sin decimales)
- 🖼️ Generación de imagen PNG con ruido anti-bot (líneas y puntos aleatorios)
- 🎚️ 3 niveles de dificultad configurables: `easy`, `medium`, `hard`
- 🔄 Botón de refresh integrado
- ✔️ Regla de validación lista para usar
- 🧩 Componente Blade: `<x-emcaptcha />`
- ⚙️ Configuración publicable
- 🔍 Auto-discovery de Laravel (sin configuración manual)

---

## Requisitos

- PHP ≥ 8.1
- Laravel 10, 11 o 12
- Extensión PHP `ext-gd`

---

## Instalación

```bash
composer require elysdan/emcaptcha
```

El paquete se registra automáticamente gracias al auto-discovery de Laravel.

### Publicar configuración (opcional)

```bash
php artisan vendor:publish --tag=emcaptcha-config
```

Esto crea `config/emcaptcha.php` donde puedes personalizar:

| Opción | Default | Descripción |
|---|---|---|
| `difficulty` | `'medium'` | `easy` (1-9), `medium` (10-99), `hard` (100-999) |
| `operations` | `['+', '-', '*']` | Operaciones habilitadas |
| `image.width` | `200` | Ancho de imagen (px) |
| `image.height` | `70` | Alto de imagen (px) |
| `image.bg_color` | `'#ffffff'` | Color de fondo |
| `image.text_color` | `'#333333'` | Color del texto |
| `image.noise_lines` | `5` | Líneas de ruido |
| `image.noise_dots` | `50` | Puntos de ruido |
| `session_key` | `'emcaptcha_answer'` | Clave de sesión |
| `expire_minutes` | `5` | Minutos de expiración |

---

## Uso

### 1. En un formulario Blade

```blade
<form method="POST" action="/tu-ruta">
    @csrf

    <!-- Tus otros campos... -->

    <x-emcaptcha />

    @error('captcha')
        <span class="error">{{ $message }}</span>
    @enderror

    <button type="submit">Enviar</button>
</form>
```

El componente renderiza automáticamente:
- La imagen del captcha
- Un botón de refresh 🔄
- Un input numérico para la respuesta

### 2. Validación en el Controller

```php
use Elysdan\EMCaptcha\Rules\ValidCaptcha;

public function store(Request $request)
{
    $request->validate([
        'captcha' => ['required', 'numeric', new ValidCaptcha],
        // ...otros campos
    ]);

    // El captcha es válido, continúa...
}
```

### 3. Uso con Facade (avanzado)

```php
use Elysdan\EMCaptcha\Facades\EMCaptcha;

// Crear un captcha manualmente
$captcha = EMCaptcha::createFull();
// ['expression' => '7 + 3', 'answer' => 10, 'key' => 'emcaptcha_answer']

// Validar respuesta
$isValid = EMCaptcha::check($userInput);

// Refrescar captcha
$newCaptcha = EMCaptcha::refresh();

// Obtener URL de imagen
$url = EMCaptcha::getImageUrl();
```

---

## Rutas registradas

| Método | URI | Nombre | Descripción |
|---|---|---|---|
| GET | `/captcha/emcaptcha` | `emcaptcha.show` | Retorna la imagen PNG |
| GET | `/captcha/emcaptcha/refresh` | `emcaptcha.refresh` | Genera nuevo captcha (JSON) |

---

## Personalización del componente Blade

El componente acepta atributos opcionales:

```blade
<x-emcaptcha name="mi_captcha" placeholder="Escribe el resultado" />
```

---

## Tests

```bash
composer install
./vendor/bin/phpunit
```

---

## Licencia

MIT. Ver [LICENSE](LICENSE).

# 🍳 Recetario Instant Pot — Estado del Proyecto

**Generado:** 2026-07-21
**Repositorio:** `github.com/rikardote/recetario.git`

---

## 📦 Infraestructura

### Docker
| Archivo | Propósito |
|---------|-----------|
| `Dockerfile` | PHP 8.4 Apache + MySQL + Node 22 |
| `docker-compose.yml` | App (puerto 9787) + MySQL 8.0 (puerto 3307) |
| `docker-entrypoint.sh` | Espera MySQL, migra y seedea automáticamente |
| `.dockerignore` | Excluye node_modules, vendor, .env, public/hot |

### Variables de entorno (docker-compose)
```
APP_ENV=production, APP_DEBUG=false, DB_CONNECTION=mysql
DB_HOST=db, DB_DATABASE=recetario, DB_USERNAME=recetario, DB_PASSWORD=recetario_secret
APP_KEY=base64:dV61Qe4WY/Kgg75MTyNxfmjIErPKq/ubhZ6FqgKZNiY=
```
**Nota:** `APP_URL` se determina dinámicamente en `AppServiceProvider` según la petición. Soporta HTTPS detrás de proxy (Cloudflare, NPM).

---

## 🏗️ Arquitectura

### Importador Legacy (`/importar`)
- **Archivo:** `app/Services/RecipeMarkdownParser.php` (~900 líneas)
- **Componente:** `pages::recipe-importer` → `resources/views/pages/recipe-importer.blade.php`
- **Ruta:** `Route::livewire('/importar', 'pages::recipe-importer')`
- **Características:** Flexible, tolerante, basado en heurísticas (~10 violaciones a RMS v2.0)
- **Estado:** ✅ Funcional, en producción, pero se recomienda migrar a v2

### Importador RMS v2.0 (`/importar-v2`)
- **Archivos:** `app/Services/RmsV2/*` (10 clases)
- **Componente:** `pages::recipe-importer-v2` → `resources/views/pages/recipe-importer-v2.blade.php`
- **Ruta:** `Route::livewire('/importar-v2', 'pages::recipe-importer-v2')`
- **Estado:** ✅ Implementado, probado, funcional
- **100% determinista:** Sin heurísticas, sin adivinar, sin `str_contains`

```
app/Services/RmsV2/
├── RmsV2Parser.php              # Orquestador
├── RmsValidationResult.php      # DTO éxito/error
├── YamlParser.php               # YAML frontmatter (16 campos obligatorios)
├── SectionExtractor.php         # Localiza 16 secciones por H1 exacto
├── IngredientsValidator.php     # Formato: "- cantidad unidad nombre"
├── ProcedureValidator.php       # 4 sub-bloques exactos por paso
├── TechnicalSummaryValidator.php # Tabla Markdown | Función | Tiempo |
├── ProblemsValidator.php        # ## Problema → ### Causa + ### Solución
├── SectionValidators.php        # Texto libre, listas, H2 sections
└── RmsV2Importer.php            # Escribe en BD solo si validación ok
```

---

## 🗄️ Base de datos

### Migraciones (25)
Recetas, ingredientes, pasos, variantes, adaptaciones, conceptos, errores, equipo, tags, categorías, favoritos, vistas, funciones Instant Pot, imágenes, videos, tabla pivot `category_recipe`, `recipe_dependencies`, `source_markdown`.

### Seeders
| Archivo | Contenido |
|---------|-----------|
| `ExistingRecipesSeeder.php` | **9 recetas reales** (Choripollo, Cochinita, Pollo BBQ, Bistec Ranchero, Caldo de Res, Picadillo, Frijoles, Caldo de Papas, Tostadas/Sopes) |
| `DatabaseSeeder.php` | Llama a `ExistingRecipesSeeder` + seeders de soporte |

### Modelos
- **Recipe** — fillable incluye `source_markdown`, `categories()` many-to-many con `is_primary`, `dependencies()` auto-referencial
- **Ingredient**, **RecipeIngredient**, **RecipeStep**, **RecipeVariant**, **RecipeAdaptation**, **RecipeConcept**, **RecipeError**, **Equipment**, **Tag**, **Category**, **Concept**, **InstantPotFunction**, **Favorite**, **RecipeView**

---

## 🔧 Funcionalidades implementadas

| Funcionalidad | Estado | Detalle |
|--------------|--------|---------|
| Importar recetas (legacy) | ✅ | Markdown con formato flexible |
| Importar recetas (RMS v2.0) | ✅ | Formato determinista con validación estricta |
| Ver recetas | ✅ | Pestañas: procedimiento, ingredientes, equipo, variantes, adaptaciones, conceptos, errores, resultado, conservación, resumen |
| Ver fuente markdown | ✅ | Botón "Ver fuente" en detalle de receta (solo bajo demanda) |
| Eliminar recetas | ✅ | Modal de confirmación, elimina datos relacionados, redirige al listado |
| Slug único automático | ✅ | Si el slug existe, agrega `-1`, `-2` |
| Búsqueda | ✅ | Componente Livewire |
| Comparar recetas | ✅ | Ruta `/comparar` |
| Lista de compras | ✅ | Por receta y general |
| Planeador semanal | ✅ | Ruta `/planeador` |
| Favoritos | ✅ | Componente Livewire |
| Listado de técnicas | ✅ | Ruta `/tecnicas` |
| Listado de conceptos | ✅ | Ruta `/conceptos` |
| Listado de funciones IP | ✅ | Ruta `/funciones` |
| Listado de equipos | ✅ | Ruta `/accesorios` |
| Detección dinámica de URL | ✅ | Funciona con/sin proxy HTTPS |
| TrustProxies | ✅ | `bootstrap/app.php: trustProxies(at: "*")` |
| PHP 8.4 + MySQL | ✅ | Dockerfile + docker-compose |

---

## 📄 Archivos clave

### Configuración
- `docker-compose.yml` — servicios app + db
- `Dockerfile` — build de la imagen
- `docker-entrypoint.sh` — startup (espera MySQL, migra, seedea)
- `.env` — local (SQLite)
- `.env.example` — documentado con ambas opciones
- `config/livewire.php` — SFC components, namespaces

### Frontend
- `resources/views/layouts/app.blade.php` — layout con navegación
- `resources/views/pages/recipe-detail.blade.php` — detalle de receta (SFC)
- `resources/views/pages/recipe-importer.blade.php` — importador legacy (SFC)
- `resources/views/pages/recipe-importer-v2.blade.php` — importador RMS v2.0 (SFC)
- `resources/views/components/⚡recipe-importer.blade.php` — componente importador legacy
- `resources/views/components/⚡rms-v2-importer.blade.php` — componente importador v2
- `resources/views/components/⚡recipe-detail.blade.php` — componente detalle

### Backend
- `routes/web.php` — todas las rutas Livewire
- `app/Services/RecipeMarkdownParser.php` — parser legacy (~900 líneas)
- `app/Services/RmsV2/*` — nuevo parser v2 (~80 líneas cada clase)
- `app/Providers/AppServiceProvider.php` — URL dinámica
- `bootstrap/app.php` — trustProxies

---

## 📋 Pendientes / Próximos pasos

1. **🔀 Migrar recetas existentes a formato RMS v2.0** — las 9 recetas del seeder usan el formato legacy
2. **🧪 Tests** — no hay tests automatizados para el parser
3. **🔧 Editor de recetas** — actualmente solo importación, no hay edición inline
4. **📸 Imágenes** — el modelo `RecipeImage` existe pero no hay UI para subir imágenes
5. **📊 Analytics** — `RecipeView` existe pero sin UI de estadísticas
6. **🌐 API** — no hay endpoints REST/JSON
7. **🔐 Autenticación** — no hay login/registro (tabla `users` creada por migración pero sin uso)
8. **🐛 Debug** — validar que el importador v2 maneje todos los casos borde (ingredientes opcionales, cantidades fraccionarias, etc.)

---

## 🚀 Deploy en Portainer

1. **Stacks → Add stack**
2. Nombre: `recetario`
3. Git Repository: `https://github.com/rikardote/recetario.git`
4. Branch: `main`
5. Si usas dominio propio, agrega variable de entorno: `APP_URL=https://tudominio.com`
6. **Deploy**

### Para actualizar después de cambios:
1. **Stacks → recetario → Update stack**
2. Marcar **"Re-pull image"** y **"Force update"**
3. **Update**

---

## 🛠️ Comandos útiles

```bash
# Local
docker compose up -d --build    # Construir y levantar
docker compose logs -f app       # Logs de la app
docker compose exec app sh       # Shell en el contenedor

# Base de datos
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force --class=ExistingRecipesSeeder

# Cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear

# Probar parser v2
docker compose exec app php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$parser = new App\Services\RmsV2\RmsV2Parser();
$result = $parser->validate(file_get_contents("test.md"));
var_dump($result->valid ? $result->data : $result->errors);
'
```

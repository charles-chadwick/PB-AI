# EHR Project Rules

## Project Overview

This is an Electronic Health Records (EHR) system built with Laravel 12, Vue 3, and TypeScript using Inertia.js for SPA functionality.

## Tech Stack

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Vue 3 (Composition API with `<script setup>`)
- **Type Safety:** TypeScript (strict mode)
- **Styling:** TailwindCSS 4
- **Build Tool:** Vite 7
- **Database:** SQLite (dev) / MySQL (production)

## Coding Style

### PHP (Laravel)

- Follow PSR-12 coding standard
- Use 4-space indentation
- Use type hints and return types on all methods
- Use PHP 8+ features: named arguments, match expressions, null-safe operator (`?->`)
- Use short closures (`fn() =>`) when appropriate
- Use snake_case for database columns and properties
- Use camelCase for method names
- Use PascalCase for class names and Enums
- Use snake_case for variable names

#### Models

- Extend `App\Models\Base` for all models (provides soft deletes, audit fields)
- Use traits for reusable functionality (`Searchable`, `Sortable`, `LogModelActivity`)
- Define `$fillable` arrays explicitly
- Use Eloquent relationships with proper return types

#### Controllers

- Use RESTful resource controllers with standard CRUD methods
- Inject Form Request classes for validation
- Return Inertia responses for page rendering
- Keep controllers thin, move logic to services when complex

#### Form Requests

- Create dedicated FormRequest classes for validation
- Use `rules()` method for validation rules
- Use `authorize()` for permission checks

### Vue / TypeScript

- Use `<script setup lang="ts">` syntax (Composition API)
- Use 2-space indentation for Vue/TS/JS files
- Define props with TypeScript interfaces using `defineProps<T>()`
- Use `ref()` for primitive reactive values
- Use `reactive()` for object reactive values
- Use `computed()` for derived state
- Prefer template refs over DOM queries

#### Component Structure

```vue
<script setup lang="ts">
// 1. Imports
// 2. Props/Emits definitions
// 3. Composables
// 4. Reactive state
// 5. Computed properties
// 6. Methods
// 7. Lifecycle hooks
</script>

<template>
  <!-- Template content -->
</template>
```

#### Naming Conventions

- PascalCase for component names and files
- camelCase for functions
- SCREAMING_SNAKE_CASE for constants
- kebab-case for custom events
- snake_case for variables, props and attributes

### shadcn-vue Components

- **Always use shadcn-vue components** if available at shadcn-vue.com
- Install components via: `npx shadcn-vue@latest add <component>`
- Components are installed to `resources/js/Components/ui/`
- Import from `@/Components/ui/<component>`
- Do NOT create custom components for functionality shadcn-vue provides

### Ziggy Routes

- **Always use Ziggy's `route()` function** for all Laravel routes in Vue
- Never hardcode URL paths (e.g., `/users`, `/patients/1/edit`)
- Use named routes: `route('users.index')`, `route('users.show', user.id)`
- Route names follow Laravel resource conventions: `.index`, `.create`, `.store`, `.show`, `.edit`, `.update`, `.destroy`

### TailwindCSS

- Use utility classes directly in templates
- Use semantic color variables (primary, secondary, muted, etc.)
- Follow mobile-first responsive design (sm:, md:, lg:)
- Use `clsx` or `tailwind-merge` for conditional classes
- Use `bg-linear-to-*` for gradients (not `bg-gradient-to-*` which is deprecated in Tailwind v4)

## File Organization

```
app/
├── Enums/           # PHP enums
├── Http/
│   ├── Controllers/ # RESTful controllers
│   └── Requests/    # Form request validation
├── Models/          # Eloquent models (extend Base)
├── Services/        # Business logic services
└── Traits/          # Reusable model traits

resources/js/
├── Components/ui/   # shadcn-vue components (installed via CLI)
├── Layouts/         # Page layout components
├── Pages/           # Inertia page components
├── lib/             # Utility functions
└── types/           # TypeScript type definitions
```

## Best Practices

### Security

- Always validate user input via Form Requests
- Use Spatie Permission for authorization
- Never expose sensitive data in Inertia props
- Sanitize user-generated content before display

### Performance

- Use eager loading to prevent N+1 queries
- Paginate large datasets
- Use `only()` on Inertia responses to minimize data transfer
- Lazy load heavy components when possible

### Database

- Use migrations for all schema changes
- Use soft deletes via Base model
- Audit fields (created_by, updated_by) are automatic via Base model
- Use database transactions for multi-step operations

### Testing

- Write Pest feature tests for controllers
- Write Pext tests for services and complex logic
- Use model factories for test data
- Run tests with `composer run test`

## Commands

This project uses **Laravel Sail** for local development. Always prefix artisan commands with `sail`.

- `sail php artisan <command>` - Run any artisan command
- `sail php artisan migrate:fresh --seed` - Reset and seed database
- `sail php artisan tinker` - Interactive REPL
- `npm run dev` - Start Vite dev server
- `npm run build` - Build for production
- `composer run dev` - Start full dev environment
- `composer run test` - Run PHPUnit tests
- `./vendor/bin/pint` - Format PHP code

**Important:** Never use `php artisan` directly. Always use `sail php artisan`.

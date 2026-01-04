# Using DashLite with Laravel + Vite

## ⚠️ Important: DashLite Asset Loading

DashLite's source files in `resources/` contain **build directives** (`@@include`) that are NOT compatible with Vite.

### Solution: Use Pre-compiled DashLite Assets

You have **two options**:

---

## Option 1: Load DashLite from Public Assets (Recommended)

Copy the **compiled** DashLite CSS and JS from your DashLite template to `public/assets/`:

```
public/
└── assets/
    ├── css/
    │   ├── dashlite.css
    │   └── theme.css
    └── js/
        ├── bundle.js
        └── scripts.js
```

Then load them in your Blade layout:

```blade
<!-- resources/views/layout/base.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <!-- DashLite CSS (pre-compiled) -->
    <link rel="stylesheet" href="{{ asset('assets/css/dashlite.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">

    <!-- Your custom CSS (compiled by Vite) -->
    @vite('resources/css/app.css')
    @stack('styles')
</head>
<body>
    @yield('content')

    <!-- DashLite JS (pre-compiled, includes jQuery) -->
    <script src="{{ asset('assets/js/bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>

    <!-- Your custom JS (compiled by Vite) -->
    @vite('resources/js/app.js')
    @stack('scripts')
</body>
</html>
```

---

## Option 2: Use CDN for Bootstrap & jQuery

Load Bootstrap 5 and jQuery from CDN, then only use Vite for your custom files:

```blade
<!DOCTYPE html>
<html>
<head>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Your custom CSS -->
    @vite('resources/css/app.css')
</head>
<body>
    @yield('content')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Your custom JS -->
    @vite('resources/js/app.js')
</body>
</html>
```

---

## Current Vite Setup

Vite is now configured to **only compile your custom files**:

```javascript
// vite.config.js
input: [
    "resources/css/app.css", // Your custom CSS
    "resources/js/app.js", // Your custom JS
];
```

**Page-specific files** still work with `@vite()`:

```blade
@vite('resources/js/pages/dashboard/index.js')
@vite('resources/css/pages/dashboard/dashboard.scss')
```

---

## Quick Start

1. ✅ **Copy** compiled DashLite assets to `public/assets/` (if using Option 1)
2. ✅ **Update** your `base.blade.php` with one of the options above
3. ✅ **Run** `npm run dev`
4. ✅ **Start coding!**

---

## Files You Can Edit

✅ **Can edit & compile with Vite:**

-   `resources/css/app.css` - Your custom global CSS
-   `resources/css/pages/**/*.scss` - Page-specific SCSS
-   `resources/js/app.js` - Your custom global JS
-   `resources/js/pages/**/*.js` - Page-specific JS

❌ **Cannot compile with Vite (use pre-compiled):**

-   `resources/css/dashlite.scss` - Contains build directives
-   `resources/css/theme.scss` - Contains build directives
-   `resources/js/bundle.js` - Contains build directives
-   `resources/js/scripts.js` - Contains build directives

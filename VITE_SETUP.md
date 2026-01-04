# Vite Configuration for DashLite + Laravel

## 📋 Overview

This project uses **DashLite** (Bootstrap 5 template) as the admin dashboard UI framework with Laravel and Vite for asset compilation.

**JavaScript Stack:**

-   ✅ **jQuery** (included in DashLite's `bundle.js`) - For DOM manipulation and `$.ajax`
-   ✅ **Bootstrap 5** (included in DashLite) - UI framework
-   ✅ **DashLite scripts** - Template functionality
-   ✅ **Page-specific JS** - Modular scripts in `resources/js/pages/`

## ⚙️ Configuration Summary

### **What We Removed:**

-   ❌ TailwindCSS (conflicts with Bootstrap 5)
-   ❌ Axios (you use jQuery's `$.ajax`)
-   ❌ bootstrap.js (no longer needed)

### **What We Have:**

-   ✅ DashLite SCSS files (`dashlite.scss`, `theme.scss`)
-   ✅ DashLite JS files (`bundle.js` with jQuery, `scripts.js`)
-   ✅ Custom styles (`app.css` for project-specific CSS)
-   ✅ Global JavaScript (`app.js` with CSRF setup for $.ajax)
-   ✅ Page-specific JavaScript (`resources/js/pages/`)

## 📦 File Structure

```
resources/
├── views/                         ← Your Blade templates
│   ├── dashboard/
│   ├── auth/
│   └── landing/
│
├── css/
│   ├── dashlite.scss              # DashLite main styles (Bootstrap 5)
│   ├── theme.scss                 # DashLite theme customization
│   ├── app.css                    # Your custom CSS (extends DashLite)
│   │
│   └── pages/                     # 🆕 Page-specific SCSS (mirrors views)
│       ├── dashboard/
│       │   └── dashboard.scss
│       ├── auth/
│       │   └── login.scss
│       └── landing/
│           └── landing.scss
│
└── js/
    ├── bundle.js                  # DashLite vendor bundle (includes jQuery)
    ├── scripts.js                 # DashLite main scripts
    ├── app.js                     # Your global JavaScript (CSRF, utilities)
    │
    └── pages/                     # 🆕 Page-specific JS (mirrors views)
        ├── dashboard/
        │   └── index.js
        ├── auth/
        │   └── login.js
        └── landing/
            └── landing.js
```

## 🎯 Entry Points

The Vite config compiles these main entry points:

### **CSS/SCSS:**

1. `resources/css/dashlite.scss` → DashLite styles
2. `resources/css/theme.scss` → DashLite theme
3. `resources/css/app.css` → Your custom styles

### **JavaScript:**

1. `resources/js/bundle.js` → DashLite vendor bundle
2. `resources/js/scripts.js` → DashLite functionality
3. `resources/js/app.js` → Laravel utilities
4. Individual chart/app scripts (loaded separately if needed)

## 🚀 Usage in Blade Templates

### **Base Layout (All Pages)**

```blade
<!-- resources/views/layout/base.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/dashlite.scss', 'resources/css/theme.scss', 'resources/css/app.css'])
    @stack('styles')
</head>
<body>
    @yield('content')

    {{-- Load DashLite core scripts + global app.js --}}
    @vite(['resources/js/bundle.js', 'resources/js/scripts.js', 'resources/js/app.js'])

    {{-- Page-specific scripts --}}
    @stack('scripts')
</body>
</html>
```

### **Page-Specific Scripts (Dashboard Example)**

```blade
<!-- resources/views/dashboard/index.blade.php -->
@extends('layout.base')

@push('styles')
    @vite('resources/css/pages/dashboard/dashboard.scss')
@endpush

@section('content')
    <div class="nk-content">
        <h1>Dashboard</h1>
        <!-- Your dashboard content -->
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/dashboard/index.js')
@endpush
```

### **Multiple Page-Specific Scripts**

```blade
<!-- resources/views/users/index.blade.php -->
@extends('layout.base')

@push('styles')
    {{-- Page-specific CSS if needed --}}
    @vite('resources/css/custom-datatable.css')
@endpush

@section('content')
    <!-- Your users page content -->
@endsection

@push('scripts')
    {{-- Load multiple page-specific scripts --}}
    @vite(['resources/js/pages/users.js', 'resources/js/libs/datatable-btns.js'])
@endpush
```

## 📝 Creating Page-Specific JavaScript

### **1. Create the JS file**

```javascript
// resources/js/pages/products.js
$(document).ready(function () {
    console.log("Products page loaded");

    // Load products via AJAX
    function loadProducts() {
        $.ajax({
            url: "/api/products",
            method: "GET",
            success: function (response) {
                console.log("Products loaded:", response);
                // Update UI with products
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
            },
        });
    }

    // Form submission
    $("#productForm").on("submit", function (e) {
        e.preventDefault();

        $.ajax({
            url: $(this).attr("action"),
            method: "POST",
            data:
                $(this).attr("method") === "POST" ? $(this).serialize() : null, // Only serialize for POST
            success: function (response) {
                alert("Product saved!");
                loadProducts(); // Reload list
            },
            error: function (xhr, status, error) {
                console.error("Error saving product:", error);
            },
        });
    });

    // Initialize
    loadProducts();
});
```

### **2. Load in your Blade template**

```blade
@push('scripts')
    @vite('resources/js/pages/products.js')
@endpush
```

### **3. (Optional) Add to Vite config for pre-compilation**

If you want faster builds, add frequently-used pages to `vite.config.js`:

```javascript
input: [
    // ... existing files
    'resources/js/pages/products.js',
    'resources/js/pages/orders.js',
],
```

## 💡 jQuery & AJAX Examples

### **Global CSRF Setup (Already in app.js)**

```javascript
// This is already configured in app.js
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});
```

### **GET Request**

```javascript
$.ajax({
    url: "/api/data",
    method: "GET",
    success: function (response) {
        console.log(response);
    },
});

// Or shorthand
$.get("/api/data", function (response) {
    console.log(response);
});
```

### **POST Request**

```javascript
$.ajax({
    url: "/api/save",
    method: "POST",
    data: {
        name: "Product Name",
        price: 99.99,
    },
    success: function (response) {
        console.log("Saved:", response);
    },
    error: function (xhr) {
        console.error("Error:", xhr.responseJSON);
    },
});
```

### **Form Serialization**

```javascript
$("#myForm").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
        url: $(this).attr("action"),
        method: $(this).attr("method") || "POST", // Use form method or default to POST
        data: $(this).serialize(), // Converts form to key=value&key2=value2
        success: function (response) {
            alert("Form submitted!");
        },
        error: function (xhr, status, error) {
            console.error("Form submission error:", error);
        },
    });
});
```

## 📁 Organizing Page Scripts (Folder-Based)

The folder structure **mirrors your views folder** for easy navigation:

```
views/                    css/pages/                js/pages/
├── dashboard/            ├── dashboard/            ├── dashboard/
│   └── index.blade.php   │   └── dashboard.scss    │   └── index.js
├── auth/                 ├── auth/                 ├── auth/
│   └── login.blade.php   │   └── login.scss        │   └── login.js
└── landing/              └── landing/              └── landing/
    └── index.blade.php       └── landing.scss          └── landing.js
```

**Load in Blade:**

```blade
@vite('resources/css/pages/dashboard/dashboard.scss')
@vite('resources/js/pages/dashboard/index.js')
```

## 🛠️ Development Commands

```bash
# Install dependencies
npm install

# Start dev server with Hot Module Replacement (HMR)
npm run dev

# Build for production
npm run build
```

## 📝 Adding Custom Styles

Edit `resources/css/app.css`:

```css
/* Use CSS variables for consistency */
:root {
    --app-primary: #6576ff;
    --app-custom: #ff6584;
}

/* Your custom classes */
.my-custom-button {
    background: var(--app-primary);
    padding: 10px 20px;
    border-radius: 4px;
}

/* Override DashLite/Bootstrap if needed */
.btn-primary {
    background-color: var(--app-primary);
}
```

## 🎨 Bootstrap 5 + DashLite

DashLite uses Bootstrap 5, so you have access to:

-   **Grid system**: `.container`, `.row`, `.col-*`
-   **Components**: `.btn`, `.card`, `.modal`, `.navbar`, etc.
-   **Utilities**: `.m-*`, `.p-*`, `.text-*`, `.bg-*`
-   **DashLite custom classes**: `.nk-*`, `.card-inner`, etc.

**Documentation:**

-   Bootstrap 5: https://getbootstrap.com/docs/5.0/
-   DashLite: Check your template documentation

## ⚠️ Important Notes

1. **Don't use TailwindCSS** - It conflicts with Bootstrap 5
2. **SCSS compilation** - Powered by Sass (Dart Sass)
3. **Hot Module Replacement (HMR)** - Changes reflect instantly during `npm run dev`
4. **Production builds** - Assets are hashed and optimized automatically

## 🔧 Vite Config (Simplified)

The Vite configuration is **minimal and clean** - only essential features:

```javascript
// vite.config.js
export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/dashlite.scss",
                "resources/css/theme.scss",
                "resources/css/app.css",
                "resources/js/bundle.js",
                "resources/js/scripts.js",
                "resources/js/app.js",
            ],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                api: "modern-compiler", // Required for SCSS
            },
        },
    },
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"], // Prevents infinite reload
        },
    },
});
```

**What it does:**

-   ✅ Compiles SCSS with modern Dart Sass API
-   ✅ Prevents infinite reload loops from Laravel's compiled Blade views
-   ✅ Laravel Vite plugin handles everything else automatically

**What's NOT included (not needed):**

-   ❌ Path aliases - Use relative imports
-   ❌ Custom build output - Laravel plugin handles this
-   ❌ Server host/port - Defaults work fine (localhost:5173)

**Page-specific files** (like `pages/dashboard/index.js`) compile **on-demand** when you use `@vite()` in Blade templates!

## 📚 Next Steps

1. ✅ **Run `npm install`** to install dependencies (sass, sass-embedded, vite)
2. ✅ **Run `npm run dev`** to start development server
3. ✅ **Update your Blade templates** to use `@vite` directives
4. ✅ **Create page-specific JS** in `resources/js/pages/`
5. ✅ **Add custom styles** in `resources/css/app.css`
6. ✅ **Use jQuery's `$.ajax`** for AJAX calls (CSRF token already configured!)
7. 🎉 **Enjoy coding!**

## 🎯 Quick Reference

**Load Core Assets (Every Page):**

```blade
@vite(['resources/css/dashlite.scss', 'resources/css/theme.scss', 'resources/css/app.css'])
@vite(['resources/js/bundle.js', 'resources/js/scripts.js', 'resources/js/app.js'])
```

**Add Page-Specific Script:**

```blade
@push('scripts')
    @vite('resources/js/pages/yourpage.js')
@endpush
```

**jQuery is ready to use:**

```javascript
$(document).ready(function () {
    // Your code here
    $.ajax({
        /* CSRF already configured! */
    });
});
```

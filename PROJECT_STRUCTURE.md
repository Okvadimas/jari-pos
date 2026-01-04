# 📁 Project Structure: Global vs Page-Specific Files

## Perfect Parallel Structure ✅

Your project now has a **symmetrical structure** matching your **views folder**:

````
resources/
├── views/                         ← Your Blade templates
│   ├── dashboard/
│   │   └── index.blade.php
│   ├── auth/
│   │   ├── login.blade.php
│   │   └── register.blade.php
│   ├── landing/
│   │   └── index.blade.php
│   └── layout/
│       └── base.blade.php
│
├── css/
│   ├── app.css                    🌍 GLOBAL custom styles
│   ├── dashlite.scss              🌍 GLOBAL DashLite styles
│   ├── theme.scss                 🌍 GLOBAL DashLite theme
│   │
│   └── pages/                     📄 PAGE-SPECIFIC SCSS (mirrors views)
│       ├── dashboard/
│       │   └── dashboard.scss
│       ├── auth/
│       │   ├── login.scss
│       │   └── register.scss
│       ├── landing/
│       │   └── landing.scss
│       └── README.md
│
└── js/
    ├── app.js                     🌍 GLOBAL custom JavaScript
    ├── bundle.js                  🌍 GLOBAL DashLite bundle (jQuery)
    ├── scripts.js                 🌍 GLOBAL DashLite scripts
    │
    ## 🎯 Naming Convention & Structure

### **View Folder → CSS/JS Folder (Perfect Mirror)**

| View Path | SCSS Path | JS Path |
|-----------|-----------|---------|
| `views/dashboard/index.blade.php` | `css/pages/dashboard/dashboard.scss` | `js/pages/dashboard/dashboard.js` |
| `views/auth/login.blade.php` | `css/pages/auth/login.scss` | `js/pages/auth/login.js` |
| `views/landing/index.blade.php` | `css/pages/landing/landing.scss` | `js/pages/landing/landing.js` |
| `views/products/list.blade.php` | `css/pages/products/list.scss` | `js/pages/products/list.js` |

**Pattern:** Each view folder gets matching CSS and JS folders!

## How It Works

### 🌍 Global Files (Loaded on ALL pages)

| File | Purpose | When Loaded |
|------|---------|-------------|
| `dashlite.scss` | Bootstrap 5 + DashLite UI framework | Every page |
| `theme.scss` | DashLite color theme | Every page |
| `app.css` | **Your custom global styles** | Every page |
| `bundle.js` | jQuery + vendors | Every page |
| `scripts.js` | DashLite functionality | Every page |
| `app.js` | **Your custom global JS** (CSRF, utilities) | Every page |

### 📄 Page-Specific Files (Loaded ONLY when needed)

| File Pattern | Purpose | When Loaded |
|--------------|---------|-------------|
| `css/pages/dashboard/dashboard.scss` | Dashboard-only styles | Dashboard page only |
| `js/pages/dashboard/dashboard.js` | Dashboard-only scripts | Dashboard page only |
| `css/pages/auth/login.scss` | Login page-only styles | Login page only |
| `js/pages/auth/login.js` | Login page-only scripts | Login page only |    |

---

## 📖 Complete Usage Example

### Base Layout (resources/views/layout/base.blade.php)

```blade
<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- GLOBAL CSS - Loads on every page --}}
    @vite([
        'resources/css/dashlite.scss',
        'resources/css/theme.scss',
        'resources/css/app.css'
    ])

    {{-- PAGE-SPECIFIC SCSS - Added by child views --}}
    @stack('styles')
</head>
<body>
    @yield('content')

    {{-- GLOBAL JS - Loads on every page --}}
    @vite([
        'resources/js/bundle.js',
        'resources/js/scripts.js',
        'resources/js/app.js'
    ])

    {{-- PAGE-SPECIFIC JS - Added by child views --}}
    @stack('scripts')
</body>
</html>
```

### Dashboard Page (resources/views/dashboard/index.blade.php)

```blade
@extends('layout.base')

{{-- Load dashboard-specific SCSS --}}
@push('styles')
    @vite('resources/css/pages/dashboard/dashboard.scss')
@endpush

@section('content')
    <div class="nk-content">
        <h1>Dashboard</h1>

        <div class="dashboard-quick-stats">
            <div class="dashboard-stats-card">
                <span class="dashboard-revenue">Revenue</span>
                <h3>$12,345</h3>
            </div>
            <!-- More stats -->
        </div>
    </div>
@endsection

{{-- Load dashboard-specific JS --}}
@push('scripts')
    @vite('resources/js/pages/dashboard/dashboard.js')
@endpush
```

### Login Page (resources/views/auth/login.blade.php)

```blade
@extends('layout.base')

{{-- Load login-specific SCSS --}}
@push('styles')
    @vite('resources/css/pages/auth/login.scss')
@endpush

@section('content')
    <div class="auth-login-page">
        <div class="login-card">
            <form id="loginForm">
                <!-- Login form fields -->
            </form>
        </div>
    </div>
@endsection

{{-- Load login-specific JS --}}
@push('scripts')
    @vite('resources/js/pages/auth/login.js')
@endpush
```

---

## 🎨 What Goes Where?

### Put in `app.css` (Global CSS):
- ✅ CSS variables for your brand colors
- ✅ Global utility classes used across multiple pages
- ✅ Overrides to DashLite/Bootstrap that apply everywhere
- ✅ Custom font imports
- ✅ Global animations

**Example:**
```css
/* resources/css/app.css */
:root {
    --brand-primary: #6576ff;
    --brand-success: #1ee0ac;
}

.btn-brand {
    background: var(--brand-primary);
    color: white;
}
```

### Put in `css/pages/[module]/[page].scss` (Page-Specific SCSS):
- ✅ Styles ONLY used on that specific page
- ✅ Page-specific layouts
- ✅ Component customizations for that page
- ✅ Page-specific animations
- ✅ Use SCSS features (nesting, variables, mixins)

**Example:**
```scss
/* resources/css/pages/dashboard/dashboard.scss */

// SCSS variables (page-specific)
$card-hover-lift: -4px;
$transition-speed: 0.3s;

.dashboard-stats-card {
    padding: 1.5rem;
    transition: transform $transition-speed;

    // SCSS nesting
    &:hover {
        transform: translateY($card-hover-lift);

        .card-icon {
            color: var(--brand-primary);
        }
    }

    .card-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
}
```

### 🎨 Why SCSS for Page-Specific Files?

**SCSS gives you superpowers:**

1. **Nesting** - Cleaner, more organized code
```scss
.product-card {
    padding: 1rem;

    &:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .product-title {
        font-size: 1.25rem;
    }
}
```

2. **Variables** - Reusable values
```scss
$spacing-unit: 1rem;
$primary-blue: #6576ff;

.my-component {
    padding: $spacing-unit * 2;
    color: $primary-blue;
}
```

3. **Mixins** - Reusable styles
```scss
@mixin card-hover-effect {
    transition: transform 0.2s;

    &:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
}

.feature-card {
    @include card-hover-effect;
}
```

4. **Import DashLite Variables**
```scss
// Import DashLite's variables
@import '../../dashlite_variables';

.custom-button {
    // Use DashLite's predefined colors
    background: $primary;
    border-radius: $border-radius;
}
```

### Put in `app.js` (Global JS):

-   ✅ jQuery CSRF token setup (already done)
-   ✅ Global event listeners
-   ✅ Utilities used across all pages
-   ✅ Global form validation

**Example:**

```javascript
// resources/js/app.js
$(document).ready(function () {
    // Global delete confirmation
    $(".delete-confirm").on("click", function (e) {
        if (!confirm("Delete this item?")) {
            e.preventDefault();
        }
    });
});
```

### Put in `js/pages/[page].js` (Page-Specific):

-   ✅ AJAX calls specific to that page
-   ✅ Page-specific event handlers
-   ✅ Chart initialization for that page
-   ✅ Form submissions unique to that page

**Example:**

```javascript
// resources/js/pages/dashboard.js
$(document).ready(function () {
    // Load dashboard stats
    $.ajax({
        url: "/api/dashboard/stats",
        success: function (data) {
            // Update dashboard UI
        },
    });
});
```

---

## 🚀 Quick Start Checklist

Creating a new page? Follow these steps:

- [ ] **1. Create Blade view:** `resources/views/[module]/[page].blade.php`
- [ ] **2. Create CSS folder:** `mkdir resources/css/pages/[module]`
- [ ] **3. Create JS folder:** `mkdir resources/js/pages/[module]`
- [ ] **4. Create SCSS file:** `resources/css/pages/[module]/[page].scss`
- [ ] **5. Create JS file:** `resources/js/pages/[module]/[page].js`
- [ ] ** 6. Load in Blade:**
  ```blade
  @push('styles')
      @vite('resources/css/pages/[module]/[page].scss')
  @endpush

  @push('scripts')
      @vite('resources/js/pages/[module]/[page].js')
  @endpush
  ```

**Example for products page:**
```bash
# 1. Create folders
mkdir resources/css/pages/products
mkdir resources/js/pages/products

# 2. Create files
# resources/css/pages/products/products.scss
# resources/js/pages/products/products.js
```

---

## 💡 Pro Tips

## 📁 Organizing Page Files

### **By Module (Mirrors Views):**

```
views/                    css/pages/                js/pages/
├── dashboard/            ├── dashboard/            ├── dashboard/
│   └── index.blade.php   │   └── dashboard.scss    │   └── dashboard.js
├── auth/                 ├── auth/                 ├── auth/
│   ├── login.blade.php   │   ├── login.scss        │   ├── login.js
│   └── register....      │   └── register.scss     │   └── register.js
├── products/             ├── products/             ├── products/
│   ├── list.blade.php    │   ├── list.scss         │   ├── list.js
│   └── edit.blade.php    │   └── edit.scss         │   └── edit.js
└── landing/              └── landing/              └── landing/
    └── index.blade.php       └── landing.scss          └── landing.js
```

**Load in Blade:**
```blade
@vite('resources/css/pages/dashboard/dashboard.scss')
@vite('resources/js/pages/dashboard/dashboard.js')
```
1. **Not every page needs page-specific files** - If you only need a few lines of CSS/JS, put theminline in the Blade template

2. **DRY principle** - If you use the same styles/scripts on 3+ pages, move them to `app.css` or `app.js`

3. **Bootstrap classes first** - Use DashLite/Bootstrap classes when possible, only write custom CSS when needed

---

## 🎯 Summary

**Perfect symmetry between CSS and JS:**

| Purpose           | CSS               | JavaScript      |
| ----------------- | ----------------- | --------------- |
| **Global**        | `app.css`         | `app.js`        |
| **Page-specific** | `css/pages/*.css` | `js/pages/*.js` |

Load global files in base layout, load page-specific files with `@push('styles')` and `@push('scripts')`.

**Now your project structure is clean, organized, and scalable!** 🎉
````

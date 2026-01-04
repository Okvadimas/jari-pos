# Page-Specific Files Organization

This folder contains page-specific CSS (SCSS) and JavaScript files organized by feature/module.

## 📁 Folder Structure

The structure **mirrors your views folder** for easy navigation:

```
resources/
├── views/
│   ├── dashboard/
│   │   └── index.blade.php
│   ├── auth/
│   │   ├── login.blade.php
│   │   └── register.blade.php
│   └── landing/
│       └── index.blade.php
│
├── css/pages/                    ← SCSS files (same structure)
│   ├── dashboard/
│   │   └── dashboard.scss
│   ├── auth/
│   │   ├── login.scss
│   │   └── register.scss
│   └── landing/
│       └── landing.scss
│
└── js/pages/                     ← JS files (same structure)
    ├── dashboard/
    │   └── dashboard.js
    ├── auth/
    │   ├── login.js
    │   └── register.js
    └── landing/
        └── landing.js
```

## 🎯 Naming Convention

**View path → CSS/JS path:**

-   `views/dashboard/index.blade.php` → `css/pages/dashboard/dashboard.scss` + `js/pages/dashboard/dashboard.js`
-   `views/auth/login.blade.php` → `css/pages/auth/login.scss` + `js/pages/auth/login.js`
-   `views/products/list.blade.php` → `css/pages/products/list.scss` + `js/pages/products/list.js`

## 📝 Usage in Blade Templates

```blade
<!-- resources/views/dashboard/index.blade.php -->
@extends('layout.base')

@push('styles')
    @vite('resources/css/pages/dashboard/dashboard.scss')
@endpush

@section('content')
    <!-- Your dashboard content -->
@endsection

@push('scripts')
    @vite('resources/js/pages/dashboard/dashboard.js')
@endpush
```

## 🎨 Why SCSS Instead of CSS?

SCSS gives you powerful features:

### 1. **Nesting**

```scss
.dashboard-card {
    padding: 1rem;

    &:hover {
        transform: translateY(-2px);
    }

    .card-title {
        font-weight: 600;
    }
}
```

### 2. **Variables**

```scss
$primary-color: #6576ff;
$card-padding: 1.5rem;

.dashboard-stats {
    color: $primary-color;
    padding: $card-padding;
}
```

### 3. **Mixins**

```scss
@mixin card-hover {
    transition: transform 0.2s;

    &:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
}

.feature-card {
    @include card-hover;
}
```

### 4. **Imports**

```scss
// Import variables from main theme
@import "../../dashlite_variables";

.my-component {
    color: $primary;
}
```

## 🚀 Creating New Page Files

### 1. Create the folder structure

```bash
mkdir resources/css/pages/products
mkdir resources/js/pages/products
```

### 2. Create SCSS file

```scss
// resources/css/pages/products/products.scss
.products-page {
    // Your styles here
}
```

### 3. Create JS file

```javascript
// resources/js/pages/products/products.js
$(document).ready(function () {
    // Your scripts here
});
```

### 4. Load in Blade template

```blade
@push('styles')
    @vite('resources/css/pages/products/products.scss')
@endpush

@push('scripts')
    @vite('resources/js/pages/products/products.js')
@endpush
```

## 💡 Best Practices

1. **Match view structure** - Keep CSS/JS folders identical to views
2. **Use SCSS features** - Leverage nesting, variables, mixins
3. **Keep it modular** - Each page should be self-contained
4. **Avoid global conflicts** - Prefix classes with page name (e.g., `.dashboard-*`)

## 📚 Examples Included

-   ✅ `dashboard/dashboard.scss` + `dashboard/dashboard.js` - Dashboard page
-   ✅ `auth/login.scss` + `auth/login.js` - Login page
-   ✅ `landing/landing.scss` + `landing/landing.js` - Landing page

Use these as templates for your own pages!

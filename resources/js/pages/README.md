# Page-Specific JavaScript Files

This folder contains JavaScript files specific to individual pages or features.

## Usage

### 1. Create a page-specific JS file

Create a new file for your page, e.g., `products.js`:

```javascript
// resources/js/pages/products.js
$(document).ready(function () {
    console.log("Products page loaded");

    // Your page-specific code here
});
```

### 2. Add to Vite config (Optional)

If you want the file to be pre-compiled, add it to `vite.config.js`:

```javascript
input: [
    // ... other files
    'resources/js/pages/products.js',
],
```

### 3. Load in your Blade template

In your Blade template, load the page-specific script:

```blade
@extends('layout.base')

@section('content')
    <!-- Your page content -->
@endsection

@push('scripts')
    @vite('resources/js/pages/products.js')
@endpush
```

## Organization

You can organize files by:

-   **Module**: `users.js`, `products.js`, `orders.js`
-   **Feature**: `checkout.js`, `analytics.js`, `reports.js`
-   **Subfolder**: `admin/dashboard.js`, `shop/cart.js`

## Examples

-   `dashboard.js` - Dashboard-specific functionality
-   `users.js` - User management page
-   `products.js` - Product listing/management
-   `reports.js` - Reports and analytics

## jQuery is Available

jQuery (`$`) is globally available via DashLite's `bundle.js`, so you can use it directly in all page files.

## AJAX Calls

CSRF token is automatically configured in `app.js`, so you can make AJAX calls like:

```javascript
$.ajax({
    url: "/api/endpoint",
    method: "POST",
    data: { key: "value" },
    success: function (response) {
        console.log(response);
    },
});
```

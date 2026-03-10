# Jari POS - Product Specifications

## 1. Overview
**Jari POS** is a comprehensive Point of Sale (POS) and Enterprise Resource Planning (ERP) application tailored for businesses to manage their sales, inventory, and business operations seamlessly. The platform features an intelligent AI Chatbot designed to assist with data retrieval and recommendations.

## 2. Technology Stack
- **Backend Framework**: Laravel 12.0 (PHP 8.2+)
- **Frontend**: Blade Templates, Laravel Vite, AJAX (jQuery / Vanilla JS)
- **Database**: MySQL / MariaDB
- **Data Tables**: Yajra DataTables for server-side processing
- **AI & RAG**: Laravel AI, Pinecone PHP (Vector Database for RAG), Spatie PDF to Text
- **Reporting**: Barryvdh DOMPDF

## 3. Core Modules & Features

### 3.1. Authentication & Authorization
- **User Login & Registration**: Secure access with email verification.
- **Role & Permission Management**: Granular control over menu access (e.g., `menu-access:MJ-01`).
- **Screen Locking**: Ability to lock the session screen temporarily and unlock it for security in a physical store environment.

### 3.2. Dashboard
- High-level overview of sales performance, low-stock alerts, and key metrics.

### 3.3. Point of Sale (POS) Interface
- **Sales Interface**: Fast checkout process tailored for cashiers.
- **Product Lookup**: Search by product names, categories, and top-selling items.
- **Vouchers & Promos**: Apply discounts, manual promos, or membership promos directly to the cart.
- **Tax Calculation**: Automated calculation of PPN (VAT) where applicable.
- **Receipt Printing**: Thermal printer integration for generating sales receipts.
- **Transaction Syncing**: Synchronization for offline-to-online transactions capability (features PWA offline fallback).

### 3.4. Inventory Management
- **Master Data**: Manage Units and Categories.
- **Product Management**: Manage single products and variable products (Product Variants).
- **Stock Opname (Audits)**: 
  - Compare physical stock with system stock.
  - Multi-step approval workflow for stock adjustments (Create $\rightarrow$ Approve/Cancel).

### 3.5. Transactions
- **Sales Transactions**: Historical record of all sales, detailed views, and data tabular summaries.
- **Purchasing**: Manage inbound stock, supplier purchases, and procurement data.

### 3.6. Management & Administration
- **User Management**: Add, edit, and deactivate system users.
- **Company Profile**: Manage business details and store locations.
- **Payment Methods**: Configure payment gateways and manual payment methods (Cash, Transfer, Card, etc.).

### 3.7. Automated Reports & AI Recommendations
- **Stock Recommendations**: Intelligent stock replenishment suggestions based on sales velocity.
- **AI-Powered Insights**: Retrieves automated AI suggestions for inventory purchasing.
- **PDF Exports**: Downloadable reports generated via DOMPDF.

### 3.8. Smart Chatbot (Jari AI)
- **Knowledge Base Integration**: Upload documents (PDFs) that are parsed using `spatie/pdf-to-text`.
- **Vector Search Context**: Embeddings stored in Pinecone provide accurate Retrieval-Augmented Generation (RAG).
- **Contextual Assistance**: Users can ask natural language questions regarding the store's data, SOPs, or system usage.

## 4. User Roles (Typical Setup)
1. **Super Admin**: Full access to all settings, role management, and reporting.
2. **Manager**: Access to inventory approvals, purchasing, and advanced transaction histories.
3. **Cashier**: Access to the POS terminal, shift management, and basic transaction records.

## 5. Non-Functional Requirements
- **Performance**: High-speed POS interface capable of running efficiently with Server-side DataTables handling large product catalogs.
- **Offline Capability**: Features a PWA offline fallback page ensuring basic navigational continuity when connection drops.
- **Security**: Strict rate-limiting on login/password reset routes, robust route middleware protecting management views.

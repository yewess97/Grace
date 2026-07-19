<div align="center">

# <img src="public/storage/images/favicon.webp" width="50" align="center">race
### A Modern, Secure, and Scalable Fashion E-Commerce Platform

<p align="center">

A full-featured online fashion store built with Laravel 9, designed using modern software engineering principles to deliver a secure, scalable, high-performance, and maintainable shopping experience.

Secure • Scalable • Modular • High Performance

</p>

---

![Laravel](https://img.shields.io/badge/Laravel-9.x-red?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2-blue?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-Database-orange?style=for-the-badge&logo=mysql)
![HTML](https://img.shields.io/badge/HTML-5-orange?style=for-the-badge&logo=html5)
![CSS](https://img.shields.io/badge/CSS-3-blue?style=for-the-badge&logo=css&logoColor=2965f1)
![MDB](https://img.shields.io/badge/MDB-4-purple?style=for-the-badge&logo=mdb)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-yellow?style=for-the-badge&logo=javascript)
![JQ](https://img.shields.io/badge/jQuery-3.7-blue?style=for-the-badge&logo=jquery)
![Stripe](https://img.shields.io/badge/Stripe-Payment-blueviolet?style=for-the-badge&logo=stripe)
![License](https://img.shields.io/badge/License-MIT-success?style=for-the-badge)

</div>

---

# Table of Contents

- [Introduction](#introduction)
- [Business Problem](#business-problem)
- [Project Vision](#project-vision)
- [Objectives](#objectives)
- [Key Features](#key-features)
- [Screenshots](#screenshots)
- [Technology Stack](#technology-stack)
- [System Architecture](#system-architecture)
- [Database ERD](#database-erd)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running the Project](#running-the-project)
- [Documentation](#documentation)
- [Security Highlights](#security-highlights)
- [Performance Optimizations](#performance-optimizations)
- [Future Improvements](#future-improvements)
- [Visit the Store](#visit-the-store)

---

# Introduction

Grace is a modern fashion e-commerce platform developed with **Laravel 9**, following enterprise-level software engineering principles and modern architectural practices.

The platform is designed to provide customers with a seamless shopping experience while offering administrators a powerful dashboard for managing products, categories, users, orders, reviews, notifications, and other business operations.

Unlike traditional academic CRUD projects, Grace has been engineered with a strong emphasis on maintainability, scalability, code reusability, performance optimization, and security.

The application combines a clean user experience with a modular backend architecture, allowing new features to be integrated with minimal changes to the existing codebase.

---

# Business Problem

Many online clothing stores focus primarily on displaying products while overlooking essential aspects such as maintainability, performance, and user experience.

Grace addresses these challenges by providing a comprehensive platform that offers:

- A structured product catalog
- Secure online payments
- Flexible order management
- Personalized shopping experience
- Powerful administration tools
- Efficient product discovery
- High-performance browsing
- Secure customer authentication

The platform aims to bridge the gap between elegant user interfaces and robust backend architecture.

---

# Project Vision

The vision behind Grace is to build a complete fashion marketplace that combines modern web technologies with clean software architecture.

Rather than being just another online store, the project demonstrates how enterprise software engineering principles can be applied to Laravel applications to produce a scalable and maintainable system suitable for real-world deployment.

---

# Objectives

Grace has been developed with several primary objectives:

- Deliver a modern online shopping experience.
- Provide an intuitive administrative dashboard.
- Maintain a modular and reusable codebase.
- Support secure online transactions.
- Ensure high application performance.
- Reduce code duplication through reusable components.
- Simplify future maintenance and scalability.
- Demonstrate clean Laravel architecture and best practices.

---

# Key Features

## Customer Features

- User Registration
- Secure Login
- Social Authentication
    - Google
    - Facebook
    - GitHub
- Password Recovery
- User Profile Management
- Address Management
- Wishlist
- Shopping Cart
- Product Search
- Advanced Product Filtering
- Product Reviews
- Product Ratings
- Order Tracking
- Stripe Payment Gateway
- Cash on Delivery
- Notifications
- Responsive User Interface

---

## Product Management

- Product Categories
- Product Collections
- Multiple Product Images
- Multiple Product Sizes
- Product Variants
- Inventory Management
- Soft Delete Support
- Image Optimization
- Product Search
- Dynamic Filtering

---

## Administration Panel

The administration dashboard provides centralized management for the entire platform.

Main modules include:

- Dashboard
- Categories
- Subcategories
- Products
- Customers
- Orders
- Reviews
- User Addresses
- Notifications
- Search
- Filtering

Each module follows a unified CRUD architecture that significantly reduces repetitive code and improves maintainability.

---

# Screenshots

| Home                                    | Products                                    |
|-----------------------------------------|---------------------------------------------|
| ![](public/assets/screenshots/home.png) | ![](public/assets/screenshots/products.png) |

| Product                                            | Cart                                    |
|----------------------------------------------------|-----------------------------------------|
| ![](public/assets/screenshots/product-details.png) | ![](public/assets/screenshots/cart.png) |

| Checkout                                    | Dashboard                                    |
|---------------------------------------------|----------------------------------------------|
| ![](public/assets/screenshots/checkout.png) | ![](public/assets/screenshots/dashboard.png) |

---

# Technology Stack

## Backend

- Laravel 9
- PHP 8.2
- MySQL
- Eloquent ORM

### *Backend Third-Party Services

- Stripe
- Laravel Socialite
- Google OAuth
- Facebook OAuth
- GitHub OAuth

## Frontend

- Blade Templates
- MDBootstrap 4
- JavaScript
- jQuery
- AJAX

### *Frontend Third-Party Services

- Font Awesome
- Themify Icons
- Owl Carousel 2
- Sweet Alert 2
- aksFileUpload
- Filter Multiselect
- libphonenumber
- Tiny MCE

## Development Tools

- Composer
- NPM
- Laravel Mix
- Guzzle HTTP Client

## Performance Tools

- Fast Pagination
- Redis Support
- Laravel Cache
- Optimized Asset Pipeline
---

# System Architecture

Grace follows Laravel's MVC architecture while extending it with additional abstraction layers to improve maintainability, reusability, scalability, and consistency across the entire application.

The application separates responsibilities into well-defined modules, ensuring that each layer focuses on a single responsibility while remaining loosely coupled with the rest of the system.

```mermaid
flowchart TD
    
    %% Nodes
    Browser[Client Browser]
    Request[HTTP Request]
    Route[Route Layer]
    Middleware[Middleware Layer]
    Controller[Controller Layer]
    Validation[Validation Layer]
    Logic[Business Logic Core]
    Models[Eloquent Models]
    DB[Database Layer]
    Views[Blade Views]
    Response[HTTP Response]

    %% Ingress & Routing Pipeline
    Browser -->|User Interaction| Request
    Request -->|URL Pattern Matching| Route
    Route -->|Global & Route Group Filters| Middleware

    %% Application Controller Entry
    Middleware -->|Dispatches Validated Traffic| Controller

    %% Architectural Abstraction Separation
    Controller -->|Delegates to Form Request| Validation
    Validation -->|Sanitized & Typed Array Input| Logic
    Logic -->|Executes Domain Operations| Models

    %% Data Persistence & Response Reconstitution
    Models -->|Performs Parameterized Queries| DB
    DB -->|Returns Result Set / Hydrated Collections| Models
    Models -->|Injects Collections & Structural Context| Views
    Views -->|Compiles Server-Side HTML| Response
    Response -->|Delivers Network Payload| Browser
```

### Architectural Highlights

- Modular MVC Architecture
- Reusable Blade Components
- Standardized Constants Layer
- Helper-Based Business Utilities
- Custom Blade Directives
- Custom Artisan Commands
- Service Provider Extensions
- Modular Route Organization
- Soft Delete Strategy
- Centralized Configuration
- Localization Support
- Reusable CRUD Components

The architecture has been designed to simplify future expansion while minimizing code duplication.

---

# Database ERD

```mermaid
erDiagram

    USERS ||--o{ ADDRESSES : "located in"

    USERS ||--o{ ORDERS : "places"

    USERS ||--o{ REVIEWS : "writes"

    USERS ||--o{ WISHLISTS : "owns"

    USERS ||--o{ CARTS : "owns"

    USERS ||--o{ NOTIFICATIONS : "receives"

    CATEGORIES ||--o{ CATEGORY_SUBCATEGORY : "has"

    SUBCATEGORIES ||--o{ CATEGORY_SUBCATEGORY : "belongs to"

    CATEGORIES ||--o{ CATEGORY_PRODUCT : "has"

    PRODUCTS ||--o{ CATEGORY_PRODUCT : "belongs to"

    SUBCATEGORIES ||--o{ PRODUCT_SUBCATEGORY : "has"

    PRODUCTS ||--o{ PRODUCT_SUBCATEGORY : "belongs to"

    WISHLISTS ||--o{ PRODUCTS : "contains"

    CARTS ||--o{ PRODUCTS : "contains"

    PRODUCTS ||--o{ REVIEWS : "reviewed by"

    PRODUCTS ||--|{ PRODUCT_SIZES : "contains"

    PRODUCTS ||--o{ THUMB_IMAGES : "contains"

    PRODUCTS ||--|{ ORDER_ITEMS : "ordered in"

    ORDERS ||--|{ ORDER_ITEMS : "has"

    ORDERS ||--|{ ADDRESSES : "delivered to"


    ADDRESSES {
        unsignedBigInteger id PK
        mediumText address_1
        mediumText address_2 "NULL"
        string country
        string(50) city
        string(50) state "NULL"
        string(16) phone
        unsignedInteger postal_code
        unsignedBigInteger user_id FK
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at "NULL"
    }

    CARTS {
        unsignedBigInteger id PK
        unsignedBigInteger user_id FK
        unsignedBigInteger product_id FK
        unsignedTinyInteger product_size "DEFAULT (3)"
        unsignedInteger product_quantity "DEFAULT (1)"
        timestamp created_at
        timestamp updated_at
    }

    CATEGORIES {
        unsignedBigInteger id PK
        string name "INDEX"
        string slug UK
        string main_image
        string banner_image
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at "NULL"
    }

    CATEGORY_PRODUCT {
        unsignedBigInteger category_id FK
        unsignedBigInteger product_id FK
        timestamp created_at
        timestamp updated_at
    }

    CATEGORY_SUBCATEGORY {
        unsignedBigInteger category_id FK
        unsignedBigInteger subcategory_id FK
        timestamp created_at
        timestamp updated_at
    }

    NOTIFICATIONS {
        uuid id PK
        string type
        string notifiable_type "INDEX"
        uuid notifiable_id "INDEX"
        text data
        timestamp read_at "NULL"
        timestamp created_at
        timestamp updated_at
    }

    ORDERS {
        unsignedBigInteger id PK
        string tracking_num UK "INDEX"
        unsignedInteger num_items
        unsignedDouble total_cost
        unsignedTinyInteger status "DEFAULT (1)"
        unsignedTinyInteger payment_method
        string payment_id "NULL"
        unsignedBigInteger user_id FK "NULL"
        unsignedBigInteger address_id FK "NULL"
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at "NULL"
    }

    ORDER_ITEMS {
        unsignedBigInteger id PK
        unsignedBigInteger product_id
        string(300) product_name
        string product_main_image
        unsignedTinyInteger product_size "DEFAULT (3)"
        unsignedInteger product_quantity "DEFAULT (1)"
        unsignedDouble product_total_price
        unsignedBigInteger order_id FK
        timestamp created_at
        timestamp updated_at
    }

    PRODUCTS {
        unsignedBigInteger id PK
        string(300) name "INDEX"
        string slug UK
        text short_description
        longText long_description
        string main_image
        unsignedFloat(6) old_price "NULL"
        unsignedFloat(6) new_price
        unsignedInteger quantity "DEFAULT (1)"
        boolean status "DEFAULT (1)"
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at "NULL"
    }

    PRODUCT_SIZES {
        unsignedBigInteger id PK
        unsignedTinyInteger size "DEFAULT (3)"
        unsignedBigInteger product_id FK
        timestamp created_at
        timestamp updated_at
    }

    PRODUCT_SUBCATEGORY {
        unsignedBigInteger subcategory_id FK
        unsignedBigInteger product_id FK
        timestamp created_at
        timestamp updated_at
    }

    REVIEWS {
        unsignedBigInteger id PK
        unsignedTinyInteger rating "DEFAULT (3)"
        string(70) title
        text body_text
        unsignedBigInteger product_id FK
        unsignedBigInteger user_id FK
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at "NULL"
    }

    SUBCATEGORIES {
        unsignedBigInteger id PK
        string name "INDEX"
        string slug UK
        string main_image
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at "NULL"
    }

    THUMB_IMAGES {
        unsignedBigInteger id PK
        string thumb_image
        unsignedBigInteger product_id FK
        timestamp created_at
        timestamp updated_at
    }

    USERS {
        unsignedBigInteger id PK
        string(50) first_name "INDEX"
        string(50) last_name "INDEX"
        string email UK "INDEX"
        timestamp email_verified_at
        string password
        boolean role "DEFAULT (0)"
        string google_id "NULL"
        string facebook_id "NULL"
        string github_id "NULL"
        timestamp last_seen "NULL"
        string(100) remember_token "NULL"
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at "NULL"
    }

    WISHLISTS {
        unsignedBigInteger id PK
        unsignedBigInteger user_id FK
        unsignedBigInteger product_id FK
        timestamp created_at
        timestamp updated_at
    }
```
---

# Project Structure

The project is organized into clearly separated modules, allowing developers to quickly understand and navigate the codebase.

```text
📂 app/
│
├── 📂 Console/
│   └── 📂 Commands/
├── 📂 Contracts/
├── 📂 Exceptions/
├── 📂 Helpers/
├── 📂 Http/
│   ├── 📂 Controllers/
│   ├── 📂 Middleware/
│   └── 📂 Requests/
├── 📂 Mail/
├── 📂 Models/
├── 📂 Notifications/
├── 📂 Providers/
├── 📂 Services/
└── 📂 Traits/

📂 bootstrap/

📂 config/

📂 database/
└── 📂 migrations/

📂 lang/

📂 public/
│
├── 📂 assets/
├── 📂 css/
├── 📂 icons/
├── 📂 js/
├── 📂 sounds/
└── 📂 storage/

📂 resources/
│
├── 📂 css/
├── 📂 js/
└── 📂 views/

📂 routes/
│
├── 📂 admin/
├── 📂 auth/
└── 📂 guest/

📂 storage/

📂 tests/
```

Each directory has a dedicated responsibility, promoting a clean separation of concerns and simplifying long-term maintenance.

---

# Installation

## Clone the Repository

```bash
git clone https://github.com/your-username/grace.git
```

---

## Navigate into the Project

```bash
cd grace
```

---

## Install PHP Dependencies

```bash
composer install
```

---

## Install JavaScript Dependencies

```bash
npm install
```

---

## Copy Environment File

```bash
cp .env.example .env
```

---

## Generate Application Key

```bash
php artisan key:generate
```

---

## Configure Database

Update your `.env` file.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=grace
DB_USERNAME=root
DB_PASSWORD=
```

---

## Run Migrations

```bash
php artisan migrate
```

---

## Seed the Database (Optional)

```bash
php artisan db:seed
```

---

## Build Frontend Assets

Development

```bash
npm run dev
```

Production

```bash
npm run production
```

---

## Start the Development Server

```bash
php artisan serve
```

---

# Configuration

Grace supports environment-based configuration for all major services.

Examples include:

- Database
- Mail
- Cache
- Session
- Filesystem
- Queue
- Stripe Payment Gateway
- Social Login Providers
- Redis
- Logging

No sensitive credentials are stored directly inside the source code.

---

# Running the Project

After completing the installation steps, the application will be available at:

```
http://127.0.0.1:8000
```

From there, users can:

- Browse products
- Create accounts
- Place orders
- Manage wishlists
- Write reviews
- Complete payments
- Track orders

Administrators can access the dashboard to manage every aspect of the platform.

---

# Documentation

The project documentation is divided into specialized documents for easier navigation.

```text
docs/

01-project-overview.md

02-system-architecture.md

03-features.md

04-folder-structure.md

05-database-design.md

06-security.md

07-performance.md

08-installation.md

09-deployment.md

10-development-methodology.md

11-project-workflow.md

12-routing-and-application-flow.md

13-future-enhancements.md
```

Each document focuses on one aspect of the project, making the documentation easier to maintain and extend.

---

# Security Highlights

Security has been considered throughout the development lifecycle.

Implemented security measures include:

- Authentication
- Authorization
- Route Protection
- Middleware-Based Access Control
- CSRF Protection
- SQL Injection Protection
- XSS Protection
- Request Validation
- Password Hashing
- Secure Sessions
- Remember Me Authentication
- Social Login Authentication
- Secure Stripe Integration
- Environment-Based Secrets Management
- Input Sanitization
- Secure File Upload Handling
- Soft Delete Strategy
- Error Handling

These mechanisms work together to provide a secure shopping experience for both customers and administrators.

---

# Performance Optimizations

Grace incorporates multiple optimization strategies to improve responsiveness and scalability.

### Backend

- Optimized Eloquent Queries
- Fast Pagination
- Redis Support
- Application Caching
- Route Caching
- Configuration Caching
- Optimized Composer Autoload

### Frontend

- AJAX-Based Interactions
- Asset Minification
- Optimized Images
- Lazy Loading Strategies
- Responsive Design

### Architecture

- Reusable Components
- Modular Organization
- Helper Functions
- Reduced Code Duplication
- Centralized Configuration

---

# Future Improvements

Potential future enhancements include:

- Multi-language Support
- Product Recommendation Engine
- AI-Powered Search
- Real-Time Notifications
- Multi-Vendor Marketplace
- Coupon & Discount Engine
- Loyalty Points System
- REST API
- Mobile Application
- Docker Support
- CI/CD Pipeline
- Automated Testing Suite

---

# Visit the Store


---

# License

This project is licensed under the MIT License.

See the LICENSE file for more information.

---

# Author

**Yousif Ayman Ahmed Mohamed**

Full Stack Web Developer & Teaching Assistant at the Faculty of Computers & Information Technology (EELU)

---

# Acknowledgments

Special thanks to the Laravel community and all open-source contributors whose tools, libraries, and best practices helped shape this project.

---

<div align="center">

Grace

Built with Laravel ❤️ © 2026

⭐ If you found this project useful, consider giving it a star on GitHub!

</div>

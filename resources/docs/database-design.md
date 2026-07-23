# Database Design

---

# Table of Contents

- [Overview](#overview)
- [Database Philosophy](#database-philosophy)
- [Entity Relationship Overview](#entity-relationship-overview)
- [Core Entities](#core-entities)
- [Product Domain](#product-domain)
- [Customer Domain](#customer-domain)
- [Shopping Domain](#shopping-domain)
- [Order Domain](#order-domain)
- [Review Domain](#review-domain)
- [Notification Domain](#notification-domain)
- [Relationship Design](#relationship-design)
- [Data Integrity](#data-integrity)
- [Soft Delete Strategy](#soft-delete-strategy)
- [Database Optimization](#database-optimization)
- [Scalability Considerations](#scalability-considerations)
- [Database Strengths](#database-strengths)

---

# Overview

Grace uses a relational database powered by **MySQL** and managed through Laravel's **Eloquent ORM**.

The database has been carefully designed to represent the business domain of a modern fashion e-commerce platform while maintaining data consistency, flexibility, and scalability.

Instead of storing everything inside a few large tables, the data model separates responsibilities into specialized entities connected through well-defined relationships.

This normalization reduces redundancy while improving maintainability and query efficiency.

---

# Database Philosophy

The database follows several important design principles.

- Data Normalization
- Referential Integrity
- Reusable Relationships
- Flexible Product Structure
- Business-Oriented Entity Design
- Soft Deletion
- Scalable Relationships

Every table represents a single business concept.

This approach minimizes duplicated information while making future enhancements easier to implement.

---

# Entity Relationship Overview

The database consists of several business domains.

```text
Users
│
├── Addresses
├── Wishlists
├── Carts
├── Checkout
├── Orders
├── Reviews
└── Notifications

Products
│
├── Images
├── Sizes
├── Reviews
├── Categories
└── Subcategories

Orders
│
├── Order Items
└── Payment Information

Categories & Subcategories
└── Products
```

Each domain remains independent while communicating through foreign-key relationships.

---

# Core Entities

The application revolves around several primary entities.

## User

Represents every registered customer, monitor, or administrator.

Responsibilities include:

- Authentication & Authorization
- Profile Information
- Address Management
- Orders
- Wishlist
- Cart
- Checkout
- Reviews
- Notifications

---

## Product

Represents an item available for purchase.

A product stores:

- Name
- Description (Short & Long)
- Images
- Category
- Subcategory
- Sizes
- Price (Old & New)
- Stock Status
- Reviews

Products are intentionally separated from their images and sizes to maintain a flexible structure.

---

## Category

Categories organize products into major groups.

Current categories include:

- Men
- Women
- Kids

Each category may contain multiple subcategories.

---

## Subcategory

Subcategories provide more detailed product classification.

Examples include:

- Jackets
- Sweaters & Shirts
- Pants
- Shoes
- Bags
- Accessories

This hierarchy improves navigation and product discovery.

---

# Product Domain

The product domain is one of the largest parts of the application.

It consists of several interconnected entities.

```mermaid
flowchart TD

    %% Nodes
    Cat[Category]
    Sub[Subcategory]
    Prod[Product Core]
    Img[Product Images]
    Size[Size Inventory]
    Rev[Customer Reviews]

    %% Hierarchical Classification
    Cat -->|Contains| Sub
    Sub -->|Groups| Prod

    %% Product Attributes & Relations
    Prod -->|Showcases| Img
    Prod -->|Offers Variances| Size
    Prod -->|Accumulates| Rev

    %% Cross-Relations for Deep Scannability
    Rev -.->|Influences Decisions On| Prod
    Size -.->|Tracks Stock For| Prod
```

This modular structure allows products to evolve independently without modifying unrelated tables.

---

# Customer Domain

Customers interact with several independent entities.

```text
User

├── Addresses

├── Wishlist

├── Cart

├── Checkout

├── Orders

├── Reviews

└── Notifications
```

Separating these entities keeps the user table lightweight while allowing unlimited expansion.

---

# Shopping Domain

Shopping operations are intentionally separated into dedicated entities.

```mermaid
flowchart TD
    
    %% Nodes
    User[User / Customer]
    Wish[Wishlist]
    Cart[Active Shopping Cart]
    Check[Checkout Pipeline]

    %% Intent & Accumulation Flow
    User -->|Saves for Later| Wish
    User -->|Intends to Buy| Cart

    %% Transition States
    Wish -->|Move to Cart| Cart
    Cart -->|Proceed to| Check

    %% Feedback loop if checkout is abandoned
    Check -.->|Abandonment Recovery| Cart
```

Each stage of the shopping journey has a dedicated responsibility.

This separation simplifies future enhancements such as discount engines or abandoned cart recovery.

---

# Order Domain

Orders represent the business transaction between customers and the store.

Each order contains:

- Customer
- Shipping Address
- Payment Method
- Order Status
- Order Items
- Total Price

The order lifecycle follows a predefined workflow.

```mermaid
flowchart TD

    %% Nodes
    Proc[Order Processing]
    Ship[Shipped / In Transit]
    Deliv[Delivered to Destination]
    Comp[Order Completed]
    Can[Order Cancelled]

    %% Main Fulfillment Lifecycle Flow
    Proc -->|Package Handled & Labeled| Ship
    Ship -->|Carrier Logistics & Last-Mile| Deliv
    Deliv -->|Customer Confirmation / Return Window Expires| Comp

    %% Cancellation Escape Routes
    Proc ---->|Cancel Order| Can
    Ship ---->|Cancel / Return in Transit| Can
    Deliv --->|Return / Reject Delivery| Can

    %% Terminal States Styling Context
    classDef processing fill:#ffd06f,stroke:#ffbe39,stroke-width:2px,color:#000000,font-weight:500;
    classDef shipped fill:#ebcdfe,stroke:#be56ff,stroke-width:2px,color:#000000,font-weight:500;
    classDef delivered fill:#98bdfb,stroke:#4e8ffb,stroke-width:2px,color:#000000,font-weight:500;
    classDef completed fill:#3fe480,stroke:#1eb258,stroke-width:2px,color:#000000,font-weight:500;
    classDef cancelled fill:#ff657e,stroke:#99051d,stroke-width:2px,color:#000000,font-weight:500;
    class Proc processing;
    class Ship shipped;
    class Deliv delivered;
    class Comp completed;
    class Can cancelled;
```

---

# Review Domain

Reviews connect customers with purchased products.

Each review stores:

- Rating
- Review Content
- Author
- Product

This relationship enables customers to share purchasing experiences while helping future buyers make informed decisions.

---

# Notification Domain

Notifications inform users about important events throughout the application.

Examples include:

- Order Updates
- Account Events
- Administrative Messages

Separating notifications into their own entity simplifies future expansion toward real-time communication.

---

# Relationship Design

Grace uses several relationship types.

## One-to-Many

Examples include:

- Product → Thumb Images
- Product → Sizes
- User → Orders
- User → Reviews
- User → Addresses
- User → Wishlists
- User → Carts

---

## Many-to-Many

Many-to-many relationships are implemented through pivot tables where appropriate.

Examples include:

- Categories → Subcategories
- Categories → Products
- Subcategories → Products

This design allows entities to remain independent while supporting flexible associations.

---

# Data Integrity

The database emphasizes consistency through:

- Primary Keys
- Foreign Keys
- Unique Constraints
- Cascading Relationships
- Validation Rules
- Controlled Deletion

These mechanisms help prevent invalid or orphaned data.

---

# Soft Delete Strategy

Several entities support soft deletion.

Instead of permanently removing records, deleted data remains recoverable.

Benefits include:

- Data Recovery
- Audit Capability
- Safer Administration
- Reduced Risk of Accidental Data Loss

Permanent deletion remains available only when explicitly required.

---

# Database Optimization

Several optimization techniques improve database performance.

These include:

- Indexed Relationships
- Optimized Foreign Keys
- Normalized Tables
- Smaller Entity Size
- Efficient Joins
- Optimized Eloquent Relationships

Together these practices contribute to faster queries and lower storage redundancy.

---

# Scalability Considerations

The schema has been designed with future growth in mind.

Possible future enhancements include:

- Coupons
- Product Variants
- Warehouses
- Inventory Tracking
- Multiple Vendors
- Loyalty Programs
- Product Recommendations
- Inventory Reservations
- Analytics

These additions can be introduced with minimal disruption to the existing schema due to the modular design.

---

# Database Strengths

The current database design provides:

- High normalization
- Clear business boundaries
- Strong data integrity
- Excellent maintainability
- Flexible product management
- Efficient relationship modeling
- Scalable architecture
- Easy future expansion

Rather than focusing solely on storing data, the schema has been designed to accurately model the real-world business processes of an online fashion store.

---

# Continue Reading

➡ **[Security](./security)**

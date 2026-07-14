# Project Workflow

---

# Table of Contents

- [Overview](#overview)
- [Request Lifecycle](#request-lifecycle)
- [MVC Workflow](#mvc-workflow)
- [Authentication Workflow](#authentication-workflow)
- [Shopping Workflow](#shopping-workflow)
- [Order Workflow](#order-workflow)
- [Payment Workflow](#payment-workflow)
- [Review Workflow](#review-workflow)
- [Notification Workflow](#notification-workflow)
- [Cache Workflow](#cache-workflow)
- [Error Handling Workflow](#error-handling-workflow)
- [Overall System Workflow](#overall-system-workflow)
- [Summary](#summary)

---

# Overview

Grace is built around Laravel's request lifecycle while extending it with reusable infrastructure, helper utilities, middleware, caching, and modular business components.

Every user action follows a predictable execution flow that keeps responsibilities separated and simplifies maintenance.

The following sections explain how requests travel through the application.

---

# Request Lifecycle

Every HTTP request follows the same high-level lifecycle.

```mermaid
flowchart TD
    
    %% Nodes
    Browser[Client Browser]
    Routes[Route Layer]
    Middleware[Middleware Layer]
    Controller[Controller Layer]
    Validation[Validation Layer]
    Logic[Business Logic Core]
    Helpers[Helper Utilities]
    Models[Eloquent Models]
    DB[Database Layer]
    View[Blade View]

    %% 1. Inbound Request Pipeline
    Browser -->|Initiates HTTP Request| Routes
    Routes -->|Directs to Group / URI| Middleware
    Middleware -->|Applies Guards & CSRF Filters| Controller

    %% 2. Core Processing Abstractions
    Controller -->|Delegates Input Sanitization| Validation
    Validation -->|Passes Cleaned Data Payload| Logic
    Logic -->|Utilizes Common Tooling| Helpers
    Helpers -->|Initiates Query / State Change| Models

    %% 3. Persistence & Egress Response
    Models -->|Performs Parameterized Transactions| DB
    DB -->|Returns Dataset / Hydrated Records| Models
    Models -->|Supplies Context Data| View
    View -->|Renders & Delivers HTTP Response| Browser

    %% Style Linkage for Non-Linear Exception Handling
    Validation -.->|Fails: Returns Validation Redirect| Browser
```

This workflow demonstrates the separation between presentation, business logic, and persistence.

---

# MVC Workflow

Grace follows Laravel's Model-View-Controller architecture.

```mermaid
flowchart TD
    
    %% Nodes
    Browser[Client Browser]
    Controller[Controller Layer]
    Model[Eloquent Models]
    DB[Database Layer]
    View[Blade Views]

    %% Inbound Request Flow
    Browser -->|1. Dispatches HTTP Request| Controller
    Controller -->|2. Delegates Business Logic| Model

    %% Data Persistence Cycle
    Model -->|3. Reads / Writes Records| DB
    DB -->|4. Hydrates Collections & Data Sets| Model

    %% Outbound Response Flow
    Model -->|5. Returns State & Domain Entities| Controller
    Controller -->|6. Injects Structural Context| View
    View -->|7. Compiles & Delivers HTTP Response| Browser
```

Each layer has a dedicated responsibility.

| Layer      | Responsibility                             |
|------------|--------------------------------------------|
| Model      | Business entities and database interaction |
| View       | User interface                             |
| Controller | Request coordination                       |

---

# Authentication Workflow

The authentication process verifies user identity before granting access to protected resources.

```mermaid
flowchart TD
    
    %% Nodes
    User[User / Visitor]
    Form[Login Form]
    Val[Validation Layer]
    Auth[Authentication Engine]
    Sess[Session Creation]
    Dash[Dashboard / Home]
    Logout[Logout Action]
    Destroy[Session Destroyed]

    %% Authentication Pathways
    User -->|Attempts to Access| Form

    %% Multi-Method Authentication Input
    Form -->|1. Traditional: Email & Password| Val
    Form -->|2. OAuth: Google / Facebook / GitHub| Auth

    %% Core Verification Pipeline
    Val -->|Passes Security Checks| Auth
    Auth -->|Credentials Verified| Sess
    Sess -->|Establishes User Context| Dash

    %% Session Lifespan & Termination
    Dash -->|Triggers Sign-Out| Logout
    Logout -->|Clears Tokens & Cookies| Destroy
    Destroy -->|Redirects back to Login| Form

    %% Error Fallback Loop
    Val -.->|Fails: Invalid Input Format| Form
    Auth -.->|Fails: Wrong Credentials| Form
```

Supported authentication methods include:

- Email & Password
- Google OAuth
- Facebook OAuth
- GitHub OAuth

---

# Shopping Workflow

The shopping experience represents the primary business workflow.

```mermaid
flowchart TD
    
    %% Nodes
    Browse[Browse Products]
    Details[Product Details]
    Select[Select Size & Quantity]
    AddToCart[Add To Cart]
    Cart[Shopping Cart]
    Checkout[Checkout Pipeline]
    Payment[Payment Gateway]
    Order[Order Created]

    %% Main Purchase Funnel
    Browse -->|Selects Item| Details
    Details -->|Configures Options| Select
    Select -->|Confirms Variation| AddToCart
    AddToCart -->|Persists Items| Cart

    %% Transaction Execution
    Cart -->|Initiates Purchase| Checkout
    Checkout -->|Submits Secure Payment| Payment
    Payment -->|Authorization Success| Order

    %% Optimization & Fallback Loops
    Order -.->|Provides Order Tracking| Browse
    Payment -.->|Failed Transaction / Retry| Checkout
```

This flow minimizes unnecessary steps while providing a familiar purchasing experience.

---

# Order Workflow

Orders progress through several business states.

```mermaid
flowchart TD
    %% Nodes
    Start([Order Created])
    Proc[Processing State]
    Ship[Shipped State]
    Deliv[Delivered State]
    Comp[Completed State]
    Cancel[Cancelled State]

    %% Main Success Path (Happy Path)
    Start -->|Initialize Order Lifecycle| Proc
    Proc -->|1. Dispatched from Warehouse| Ship
    Ship -->|2. Arrived at Destination| Deliv
    Deliv -->|3. Confirmed & Closed| Comp

    %% Exception Handling (Cancellation Paths)
    Proc --->|User Cancel / Stock Failure| Cancel
    Ship -.->|Refused / Lost in Transit| Cancel
    Deliv -.->|Return Initiated / Rejected| Cancel

    %% Terminal States Styling Context
    classDef terminal fill:#635a02,stroke:#a2a000,stroke-width:2px;
    class Comp terminal;
    class Cancel terminal;
```

Each status represents a real business milestone during order fulfillment.

---

# Payment Workflow

Grace currently supports two payment methods.

```mermaid
flowchart TD
    
    %% Nodes
    Check[Checkout Pipeline]
    Choose[Choose Payment Method]
    Stripe[Stripe Gateway]
    COD[Cash On Delivery]
    Confirm[Payment & Billing Confirmation]
    Order[Create Order Record]
    Notify[Notification Engine]

    %% Payment Pathway Selection
    Check -->|Initiates Payment Flow| Choose

    %% Dual Payment Methods (Online vs Offline)
    Choose -->|Online: Process Card securely| Stripe
    Choose -->|Offline: Pay at Doorstep| COD

    %% Transaction Reconciliation & State Update
    Stripe -->|Capture Success Token| Confirm
    COD -->|Acknowledge COD Terms| Confirm

    %% Order Creation and Alerts
    Confirm -->|Persist Order & Reduce Inventory| Order
    Order -->|Send Confirmation Email| Notify

    %% Error Handling Loop
    Stripe -.->|Declined Card / Retry| Choose
```

Stripe securely processes online transactions, while Cash on Delivery supports customers who prefer offline payment.

---

# Review Workflow

Product reviews help improve customer confidence.

```mermaid
flowchart TD
    
    %% Nodes
    Product[Delivered Product]
    Cust[Customer]
    Write[Write Review Form]
    Val[Validation Layer]
    Store[Store Review in DB]
    Update[Product Rating Recalculated]
    Display[Public Product Details]

    %% Review Writing & Submission Process
    Product -->|Delivered & Verified Purchase| Cust
    Cust -->|Opens Feedback Option| Write
    Write -->|Submits Rating & Text Review| Val

    %% Validation & State Update Pipeline
    Val -->|Passes Safety & Integrity Checks| Store
    Store -->|Triggers Average Rating Update| Update
    Update -->|Pushes Refreshed Score To| Display

    %% Guardrail Interceptions (Sanitization / Spam)
    Val -.->|Fails: Profanity, Spam, or Bad Rating Format| Write
```

Only validated review data is persisted.

---

# Notification Workflow

Notifications keep customers informed throughout their shopping journey.

```mermaid
flowchart TD
    
    %% Nodes
    Event[Business Event Trigger]
    Create[Notification Created]
    Store[Store Notification in DB]
    Dispatch[Notification Dispatcher]
    Display[Display To User]

    %% Event Classification Examples
    Event -->|Order Updates / Admin Messages / Account Alerts| Create

    %% Storage & Queue Pipeline
    Create -->|Build Context & Payload| Store
    Store -->|Queue Event Workers| Dispatch

    %% Delivery Real-time Channel
    Dispatch -->|Push SSE| Display

    %% Read Receipt Interaction Loop
    Display -.->|User Interacts / Marks as Read| Store
```

Typical events include:

- Order updates
- Administrative messages
- Account notifications

---

# Cache Workflow

Grace reduces unnecessary database operations through caching.

```mermaid
flowchart TD
    
    %% Nodes
    Req[Incoming Request]
    Check{Does Cache Exist?}
    Hit[Retrieve Cached Data]
    Miss[Query Database]
    Store[Hydrate & Store in Cache]
    Resp[Deliver Response to Client]

    %% Main Pipeline Flow
    Req -->|Inbound Cache Evaluation| Check

    %% Cache Hit Path (Fast Path)
    Check -->|Yes: Cache Hit| Hit
    Hit -->|Bypass Database & Return Context| Resp

    %% Cache Miss Path (Database Fallback)
    Check -->|No: Cache Miss| Miss
    Miss -->|Fetch Fresh Records from Storage| Store
    Store -->|Populate Cache & Build Payload| Resp
```

This approach significantly improves application responsiveness.

---

# Error Handling Workflow

Unexpected errors are handled gracefully.

```mermaid
flowchart TD
    
    %% Nodes
    Error[Unexpected Exception]
    Handler[Laravel Exception Handler]
    Log[Log Error & Stack Trace]
    Sanitize[Generate User-Friendly Response]
    Client[Return Response to Client]

    %% Main Inbound Exception Flow
    Error -->|Caught at Global Level| Handler

    %% Concurrent Logging and Sanitization Operations
    Handler -->|1. Record Error Context| Log
    Handler -->|2. Suppress Verbose Debug Details| Sanitize

    %% Output Delivery Pipeline
    Log -.->|Write to laravel.log / Sentry| Sanitize
    Sanitize -->|Deliver Generic Safe Payload| Client
```

Internal implementation details remain hidden from end users.

---

# Overall System Workflow

The following diagram summarizes the interaction between the major application modules.

```mermaid
flowchart TD
    
    %% Central Actor
    Cust[Customer / User Agent]

    %% Core Application Subsystems / Modules
    subgraph Identity [Identity & Session Management]
        Auth[Authentication Module]
    end

    subgraph Discovery [Product Discovery Pipeline]
        Catalog[Product Catalog]
        Reviews[Reviews & Ratings]
    end

    subgraph Commerce [Transactional Funnel]
        Wishlist[Wishlist Manager]
        Cart[Shopping Cart Engine]
        Checkout[Checkout Pipeline]
        Payment[Payment Gateway]
    end

    subgraph Operations [Fulfillment & Alerts]
        Orders[Order Management]
        Notify[Notification Engine]
    end

    %% Customer Interactions (Modular Boundaries)
    Cust -->|Verifies Identity & Session| Auth
    Cust -->|Explores SKUs & Categories| Catalog
    Cust -->|Saves Items for Later| Wishlist
    Cust -->|Manages Selected Items| Cart
    Cust -->|Initiates Order Funnel| Checkout
    Cust -->|Submits Secure Payment| Payment
    Cust -->|Tracks Purchase Progress| Orders
    Cust -->|Receives Real-time Updates| Notify
    Cust -->|Submits Verified Feedback| Reviews

    %% Inter-Module Relationships & Event Triggers
    Catalog -.->|Provides Rating Summaries| Reviews
    Wishlist -.->|Promotes Saved Items| Cart
    Cart -.->|Passes Line Items| Checkout
    Checkout -.->|Submits Payable Charge| Payment
    Payment -.->|Authorization Success| Orders
    Orders -.->|Triggers Status Notifications| Notify
```

Although each module operates independently, together they form a complete e-commerce workflow.

---

# Summary

Grace is organized around a modular request lifecycle where every component has a clearly defined responsibility.

By combining Laravel's MVC architecture with middleware, validation, reusable helpers, caching, Eloquent models, and Blade templates, the application maintains a clean execution flow that is easy to understand, extend, and maintain.

The documented workflows demonstrate not only how individual features operate but also how the different modules collaborate to deliver a complete online shopping experience.

---

# Continue Reading

➡ **12-routing-and-application-flow.md**

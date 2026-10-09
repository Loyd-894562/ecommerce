# Online Shopping / E-Commerce System

## Project Title
**Online Shopping / E-Commerce System (e.g., Shopee / Lazada)**

## Project Description
The Online Shopping / E-Commerce System is a Laravel web application backed by a MySQL database. Customers can browse products, add products to a cart, check out, pay for orders, track deliveries, and review products. Sellers manage product listings and prepare orders. Administrators manage users and product listings, while couriers update shipment and delivery status.

## Objectives
- Make product browsing and purchasing convenient for customers.
- Maintain cart, order, payment, and shipment records consistently.
- Confirm orders only after successful payment.
- Support seller order fulfillment and courier delivery updates.
- Provide administrative management of users and product listings.

## Key Features / Use Cases
- **Customer:** Browse products, add to cart, checkout, make payment, track order, and review product.
- **Seller:** Manage product listings and fulfill orders.
- **Admin:** Manage users and product listings.
- **Courier:** Update shipment and delivery status.

## Technology Stack
- **Backend framework:** Laravel (PHP)
- **Database:** MySQL
- **Frontend:** Laravel Blade templates, HTML, CSS, and JavaScript
- **Styling:** Bootstrap (CDN or locally installed assets, as appropriate)
- **Database access:** Laravel Eloquent ORM and migrations
- **Version control:** Git and GitHub
- **UML source format:** PlantUML (`.puml`) with PNG exports

## Project Structure
```text
ecommerce-capstone/
├── app/                 # Laravel models, controllers, middleware, and services
├── bootstrap/
├── config/
├── database/
│   ├── migrations/      # MySQL schema migrations
│   └── seeders/
├── public/              # Public assets
├── resources/
│   └── views/           # Blade templates
├── routes/
│   └── web.php
├── docs/
│   └── uml/
│       ├── use-case-diagram.png
│       ├── class-diagram.png
│       ├── sequence-diagram.png
│       ├── activity-diagram.png
│       └── *.puml       # Editable PlantUML source files
├── .env.example
├── composer.json
├── artisan
├── README.md
└── README.pdf
```

## Setup / Execution Instructions
These instructions describe the intended Laravel setup. They can be finalized after the actual Laravel project is initialized.

1. Install a compatible PHP version, Composer, MySQL, and a local development environment such as Laravel Herd, XAMPP (with compatible PHP), or another supported setup.
2. Create the Laravel project if it has not already been initialized:
   ```bash
   composer create-project laravel/laravel ecommerce-capstone
   cd ecommerce-capstone
   ```
3. Create a MySQL database, for example `ecommerce_capstone`.
4. Copy `.env.example` to `.env` and configure the database values:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ecommerce_capstone
   DB_USERNAME=your_mysql_username
   DB_PASSWORD=your_mysql_password
   ```
5. Generate the application key and run migrations:
   ```bash
   php artisan key:generate
   php artisan migrate
   ```
6. Start the Laravel development server:
   ```bash
   php artisan serve
   ```
7. Open the local URL shown by Artisan in your browser.

**Important:** Do not commit real credentials or secret keys. These commands are setup guidance; they do not mean that the application features have already been implemented.

## UML Diagrams

### 1. Use Case Diagram
![Use Case Diagram](docs/uml/use-case-diagram.png)

The four external actors are Customer, Seller, Admin, and Courier. Payment is modeled as a system function, not as a separate bank/payment-service actor.

### 2. Class Diagram
![Class Diagram](docs/uml/class-diagram.png)

The six core classes are Product, Cart, Order, User, Payment, and Shipment. The class diagram uses standard UML class compartments (name, attributes, operations), visibility symbols, association labels, and multiplicities. `User.role` distinguishes Customer, Seller, Admin, and Courier.

### 3. Sequence Diagram
![Sequence Diagram](docs/uml/sequence-diagram.png)

The sequence follows the required order: **Add to cart → Checkout → Payment → Order fulfillment → Delivery**. It uses participant headers, dashed lifelines, activation bars, call/return messages, and `alt` branches for stock/payment outcomes. The payment-failure alternative prevents order fulfillment until payment succeeds.

### 4. Activity Diagram
![Activity Diagram](docs/uml/activity-diagram.png)

The activity diagram uses UML swimlanes, an initial node, action nodes, decision diamonds with guarded paths, directed control flows, and a final node, similar to the provided sample. It includes stock validation and payment decision paths.

## Scope and Design Rules
- The four actors are exactly **Customer, Seller, Admin, and Courier**.
- Required customer use cases include **Browse products, Add to cart, Checkout, Track order, and Review product**. Seller, Admin, and Courier have supporting role-specific functions.
- The sequence follows **Add to cart → Checkout → Payment → Order fulfillment → Delivery**.
- The six core class concepts are exactly **Product, Cart, Order, User, Payment, and Shipment**.
- No separate bank/payment-service actor is included.
- A customer may place many orders; a seller may list many products; each order contains one or more purchased products; and an order can have a shipment after fulfillment preparation.
- Stock is validated during checkout. A failed payment must not confirm the order or trigger seller fulfillment.
- This package documents the proposed design. Implement the Laravel controllers, Eloquent models, migrations, validation, authorization, and Blade views as the next development step.

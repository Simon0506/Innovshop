# InnovShop – E-commerce Demo Project

InnovShop is a demo e-commerce web application developed as part of my training for the **Web and Mobile Web Developer (DWWM)** certification.

The goal of this project is to demonstrate the skills acquired during the training by building a complete web application including product catalog management, user authentication, shopping cart, and online payment.

This project uses the Symfony framework and follows the MVC architecture.

---

## Live Demo

The project is deployed and accessible at:

https://boutique-innovshop.alwaysdata.net/

Note:  
This website is intended for demonstration purposes only. Email sending is disabled, which prevents new users from confirming their account and placing real orders.

---

## Features

Main features of the application include:

- User registration with email confirmation
- User authentication
- Product catalog browsing
- Product detail pages
- Shopping cart management
- Order process
- Secure online payment with Stripe
- Admin dashboard to manage products and categories

---

## Technologies Used

This project was developed using the following technologies:

- PHP
- Symfony 7
- Doctrine ORM
- Twig
- TailwindCSS
- Stimulus
- Webpack Encore
- EasyAdmin
- Stripe (payment integration)
- MySQL

---

## Architecture

The application follows the **MVC (Model-View-Controller)** architecture:

- **Model**: Entities managed by Doctrine ORM represent the application's data.
- **View**: Twig templates generate the HTML interface displayed to users.
- **Controller**: Symfony controllers handle requests and manage the application logic.

This architecture improves code organization, maintainability, and scalability.

---

## Installation

To run the project locally:

### 1. Clone the repository

```bash
git clone https://github.com/your-username/innovshop.git
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure environment variables

Create a .env.local file and configure the database connection :

```bash
DATABASE_URL="mysql://user:password@127.0.0.1:3306/innovshop"
```

If you don't want to send emails during development :

```bash
MAILER_DSN=null://null
```

### 4. Create the database

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. Build front-end assets

```bash
npm install
npm run build
```

### 6. Run the Symfony server

```bash
symfony server:start
```

The application will be available at :

```bash
http://localhost:8000
```

---

## Admin Interface

The project includes an administration interface built with **EasyAdmin**.

It allows administrators to :

- manage products
- manage product categories
- manage product options
- manage customers profile
- manage order status

---

## Payment System

Payments are integrated using **Stripe**.

For security reasons, Stripe is configured in test mode. This allows payment simulation without performing real financial transactions.

---

## Hosting / Deployment

The application is deployed on **Alwaysdata**, a hosting platform that supports PHP applications such as Symfony.

The project files are uploaded to the server and the application is configured to run in a production environment.

### Deployment steps

The main steps required to deploy the application are:

1. Upload the project files to the server using FTP or SFTP.

2. Install the project dependencies with Composer:

```bash
composer install --no-dev --optimize-autoloader
```

3. Configure environment variables (database connection, mailer, Stripe keys).

4. Create the database and run migrations:

```bash
php bin/console doctrine:migrations:migrate
```

5. Build front-end assets using Webpack Encore:

```bash
npm install
npm run build
```

6. Clear and warm up the Symfony cache for the production environment:

```bash
php bin/console cache:clear --env=prod
```

Once deployed, the application becomes accessible online.

### Live Application

The deployed version of the application is available at:

https://boutique-innovshop.alwaysdata.net/

### Email configuration

Because this project is intended for demonstration purposes only, email sending has been disabled in the production environment:

```bash
MAILER_DSN=null://null
```

This prevents external users from confirming their email address during registration and therefore from placing real orders on the platform.

---

## Purpose of the project

This project was developed as a learning exercise during my training. Its purpose is to demonstrate my ability to :

- build a full-stack web application
- structure a project using a modern framework
- integrate third-party services
- deploy a web application

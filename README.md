# 📦 OOP PHP Stock System

A simple **stock management system** built with **PHP** using **Object-Oriented Programming (OOP)** principles.

---

## 📖 About

This project is a stock management system that allows users to:

* Create, edit and delete products;
* Control stock quantity through a dedicated stock handling module;
* Validate forms before submitting data;
* Maintain database referential integrity.

The main goal of this project is to practice **Object-Oriented Programming (OOP)** concepts and **database design principles** while building a real-world application.

---

## ✨ Features

* 📦 Product management (Create, Edit & Delete)
* 📊 Stock control
* 🔄 Stock handling history
* 🔒 Referential integrity
* ✅ Form validation

---

## 🛠 Technologies

| Technology | Purpose               |
| ---------- | --------------------- |
| PHP        | Backend               |
| JavaScript | Frontend interactions |
| MySQL      | Database              |

---

## 📂 Project Structure

```text
.
├── api
│   ├── handling.php
│   └── products.php
│
├── app
│   ├── controllers
│   │   ├── handlingController.php
│   │   └── productController.php
│   │
│   └── helper
│       ├── executeSQL.php
│       └── redirect.php
│
├── config
│   ├── app.php
│   └── database.php
│
├── pages
│   ├── handleEdit.php
│   ├── handleForm.php
│   ├── handles.php
│   ├── product.php
│   └── productEdit.php
│
├── resources
│   ├── components
│   │   ├── error.php
│   │   └── success.php
│   │
│   ├── css
│   │   └── main.css
│   │
│   └── js
│       ├── table-handles.js
│       └── table-index.js
│
├── index.php
└── README.md
```

---

## 🚀 Getting Started

### Requirements

* PHP 8.0+
* MySQL
* Apache Server (XAMPP, WAMP or similar)

### Installation

Clone the repository:

```bash
git clone https://github.com/00Jeffim00/php-oop-stock.git
```

Navigate to the project folder:

```bash
cd php-oop-stock
```

Create a database named:

```text
php_stock
```

Import the SQL file:

```text
database.sql
```

Configure the database connection in:

```text
config/database.php
```

Start Apache and MySQL, then open:

```text
http://localhost/php-oop-stock
```

---

## 🎯 Learning Objectives

This project was developed to improve knowledge in:

* Object-Oriented Programming (OOP)
* PHP architecture
* MySQL relationships
* CRUD operations
* Form validation
* Database referential integrity
* Code organization

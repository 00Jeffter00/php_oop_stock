?? OOP PHP Stock System
A simple stock management system created with PHP using OOP concepts. 

?? About
This project made a simple stock management, that allows the user to create/edit/delete their products and control their stock with a special stock handling module. Its have form validation and referential integrity.

The main objective with this project is practice OOP and Database rules.

?? Funcionalities
? Product registration/edit/delete
? Stock control
? Product handles control
? Referential integrity 
? Form validation

??? Techonologies
    
?? Project structure

¦   index.php
¦   README.md
¦   
+---api
¦       handling.php
¦       products.php
¦       
+---app
¦   +---controllers
¦   ¦       handlingController.php
¦   ¦       productController.php
¦   ¦       
¦   +---helper
¦           executeSQL.php
¦           redirect.php
¦           
+---config
¦       app.php
¦       database.php
¦       
+---pages
¦       handleEdit.php
¦       handleForm.php
¦       handles.php
¦       product.php
¦       productEdit.php
¦       
+---resources
    +---components
    ¦       error.php
    ¦       success.php
    ¦       
    +---css
    ¦       main.css
    ¦       
    +---js
            table-handles.js
            table-index.js

?? Como executar o projeto
Requirements:
PHP 8+
MySQL
Apache Server (XAMPP, WAMP ou similar)

Instalação
Clone the repository:

git clone https://github.com/00Jeffim00/php-oop-stock.git

Crie a database named: php_stock

Run the SQL script: database.sql

Configure the DB connection on file: conf/database.php

Start the server service and access: http://localhost/php-oop-stock
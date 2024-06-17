# MVC PHP Starter Kit

This is a simple MVC (Model-View-Controller) starter kit for PHP projects.

## Installation

1. Clone the repository
2. Run `composer install` to install dependencies
3. Ensure the `public` directory is the web root
4. Access the project in your browser

## Directory Structure

my-mvc-project/
├── app/
│ ├── Controllers/
| |├── Auth/
| | └── Login.php
| |
│ ├── Models/
│ ├── Views/
│ └── core/
│ ├── Controller.php
│ ├── Model.php
│ ├── View.php
│ └── App.php
├── public/
│ └── index.php
├── vendor/
├── composer.json
└── README.md

## Usage

- Controllers should be placed in `app/Controllers`
- Models should be placed in `app/Models`
- Views should be placed in `app/Views`
- Core application files are in `app/core`

# Command-Line User Management App

## Overview

This is a simple command-line application for managing users, doctors, and checkup schedules.

## Prerequisites

- PHP installed on your machine
- Composer installed ([https://getcomposer.org/](https://getcomposer.org/))

## Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/mrriyous/cli-app.git
    ```

2. Navigate to the project directory:

    ```bash
    cd cli-app
    ```

3. Install dependencies using Composer:

    ```bash
    composer install
    ```

4. Open cli-app.php change database details:

    ```php
    $this->pdoWrapper = new PDOWrapper('localhost', 'database', 'root', 'password');
    ```

5. Open migration.php change database details and run the migration:

    ```bash
    php migration.php
    ```

## Usage

Run the command-line app using PHP:

```bash
php cli-app.php
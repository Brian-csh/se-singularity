# Singularity EAM

Singularity EAM is an Enterprise Asset Management System designed to manage information for different organizations, departments, and users.

## Features

- Efficient management of organizational information
- Support for various departments and user roles
- Authentication with Feishu （飞书） or your username and password

## Installation

1. Ensure you have PHP installed on your system.
2. Clone the repository:
    ```bash
    git clone https://gitlab.secoder.net/Singularity/singularity-eam.git
    ```
3. Navigate to the project directory:
    ```bash
    cd singularity-eam
    ```
4. Install dependencies:
    ```bash
    composer install
    ```


## Configuration

1. Create a database called `singularity` with tables such as `user`, `role`, etc. (see `database.sql`).
2. Modify the `config/database.php` file to include the correct database connection information (host, username, password, etc.).

## Usage

1. Navigate to the project URL in your web browser.
2. Log in with your Feishu account or your Singularity EAM username and password.

## Contributing
We welcome contributions to improve Singularity EAM. To contribute, please follow these steps:

1. Fork the repository.
2. Create a new branch with a descriptive name.
3. Make changes in your new branch.
4. Submit a pull request to the main branch.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.
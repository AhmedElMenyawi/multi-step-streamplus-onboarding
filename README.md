# Multi-Step Onboarding Project

Welcome to my multi-step onboarding project, developed as part of the assigned task. This project collects user, address, and payment information in a step-by-step process, concluding with a final confirmation. The onboarding flow is designed to be clear and extendable for future enhancements. Below are the details to help you set up and run the project locally, specifically for review purposes.

## Getting Started

### Prerequisites
- PHP 8.3.11
- Laravel Framework 11.28.1
- Composer

### Installation
1. **Clone the Repository**

2. **Install Dependencies**
   Run the following command to install all the necessary PHP packages:
   composer install

3. **Environment Setup**
   - Copy `.env.example` to `.env`.
   - Update any required environment variables as needed.

4. **Run Migrations**
   To set up the database tables, run the migrations:
   php artisan migrate

### Running the Application
To run the application locally, start the Symfony server:
symfony server:start
The server will be running at `http://127.0.0.1:8000`.

### Usage
- Open your browser and navigate to `http://127.0.0.1:8000` to begin the onboarding process.
- The onboarding flow will guide you through user information, address information, and payment details (if applicable).

### Testing
- To test the application, you can use SQLite, which is already configured in the project for local testing.

### Notes
- The CVV and Card Number fields are encrypted using Laravel's Crypt function for enhanced security.
- Database Structure: Two approaches were considered:
Single Table Approach: Storing all user, address, and card data in a single table.
Separate Tables for Separation of Concerns: The final decision was to use separate tables for each type of data—user, address, and card information. This structure is better suited for scalability, allowing a user to have multiple addresses or cards in the future. While it may not seem different for this task, it provides flexibility for future enhancements.

### Thank You!
Thank you for taking the time to explore this project. If you have any feedback or questions, feel free to reach out!


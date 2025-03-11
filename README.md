<p align="center">
    <a href="https://packagist.org/packages/rgalstyan/larapi">
        <img src="https://img.shields.io/packagist/dt/rgalstyan/larapi" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/rgalstyan/larapi">
        <img src="https://img.shields.io/packagist/v/rgalstyan/larapi" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/rgalstyan/larapi">
        <img src="https://img.shields.io/packagist/l/rgalstyan/larapi" alt="License">
    </a>
    <a href="https://example.com">
        <img src="https://img.shields.io/badge/Test%20on-PI%20Sandbox-612F74?style=for-the-badge&logoColor=white" alt="Test on PI Sandbox">
    </a>
</p>

# Package Installation

## Requirements
- **PHP 8.1+**
- **Laravel 10+**
- **Composer**

---

## Step 1: Install via Composer
Run the following command to install the package:

```bash
composer require rgalstyan/larapi
```

---

## **Step 2: Publish the Configuration (Optional)**
If you need to modify the default settings, publish the config file:

```bash
php artisan vendor:publish --tag=larapi-config
php artisan vendor:publish --tag=larapi-migrations
```

---

## Step 3: Environment Configuration
Add the required environment variables to your `.env` file:

```env
PI_API_KEY=your_api_key
PI_API_URL=https://api.minepi.com
PI_API_VERSION=v2
```

---

## Step 4: Run Migrations
If you have exported the **larapi-migrations** configurations, run:

```bash
php artisan migrate
```

---

# Using Package Functions

## Overview
This package integrates the Pi Network API into Laravel and provides two main services:

- **LaraPiDbService**: Manages all database operations for Pi payments using `PaymentDTO` objects, including creating, updating, deleting, and retrieving payment records along with their associated statuses and transactions.
- **LaraPiPaymentService**: Handles communication with the Pi Network API. It provides methods to create, approve, complete, cancel payments, and retrieve payment details.

---

## **LaraPiDbService**

The **LaraPiDbService** is responsible for managing the payment data stored in your local database. Below are the key methods:

- **createPayment(PaymentDTO $dto): PiPayment** – Creates a new payment record. This method checks for duplicate payment identifiers, creates associated payment status and transaction records, and returns the created payment. In case of errors, the transaction is rolled back and the error is logged.
- **updatePayment(int \$paymentId, PaymentDTO \$dto): PiPayment** – Updates an existing payment record along with its related status or transaction data.
- **deletePayment(int $paymentId): bool** – Deletes a payment record by its ID.
- **getPaymentById(int $paymentId): ?PiPayment** – Retrieves a payment record by its ID.
- **getPaymentsByUserId(int $userId): ?object** – Retrieves all payment records for a specific user based on the user ID.
- **getPaymentsByUserUid(string $piUserUid): ?object** – Retrieves all payments associated with a specific Pi user UID.

---

## **LaraPiPaymentService**

The **LaraPiPaymentService** is used to interact directly with the Pi Network API. Its main methods include:

- **createPayment(float \$amount, string \$uid, string \$memo = 'A2U Payment', array $metadata = []): PaymentDTO|null** – Sends a payment creation request to the API. On success, it returns a `PaymentDTO` containing the payment details.
- **getPayment(string $paymentId): PaymentDTO|null** – Retrieves payment details from the API for the given payment ID.
- **approvePayment(string $paymentId): PaymentDTO|null** – Approves a pending payment by sending the appropriate request to the API.
- **completePayment(string \$paymentId, string $txid): PaymentDTO|null** – Completes a payment by providing the transaction ID (`txid`) to the API.
- **cancelPayment(string $paymentId): PaymentDTO|null** – Cancels a pending payment through an API call.
- **incompleteServerPayments(): object|null** – Retrieves server-side payments that have not yet been completed, allowing further processing or error handling.

---

## Example Usage

The following examples demonstrate how you can use these services within your Laravel application:

```php
use Rgalstyan\Larapi\Facades\LaraPiPayment;
use Rgalstyan\Larapi\Facades\LaraPiDb;
```

### Example 1: Creating a Payment

```php
// Create a payment via the Pi Network API
$paymentDTO = LaraPiPayment::createPayment(10.50, 'user-unique-id', 'Payment for Order #1234', ['order_id' => 1234]);

if ($paymentDTO) {
    // Store the payment information in your database using PaymentDTO
    $payment = LaraPiDb::createPayment($paymentDTO);
    return response()->json(['success' => true, 'message' => "Payment created successfully with ID: " . $payment->id]);
} else {
    return response()->json(['success' => true, 'message' => "Payment creation failed."]);
}
```

### Example 2: Updating a Payment

```php
// Assume $paymentDTO contains updated payment details and $paymentId holds the existing payment ID
$paymentId = 123; // Example payment ID
$updatedPayment = LaraPiDb::updatePayment($paymentId, $paymentDTO);
return response()->json(['success' => true, 'message' => "Payment updated successfully."]);
```

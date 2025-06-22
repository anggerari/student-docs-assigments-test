# Student Document Upload System

This is a Laravel application designed to handle the upload and management of student documents. It provides a user interface for counselors to upload documents (PDFs, images), which are then stored securely in an AWS S3 bucket. Metadata for each document is saved in a MySQL database, and the application displays a list of all documents with secure, temporary download links.

---

## Features

* File upload interface for counselors.
* Securely stores uploaded files in a private AWS S3 bucket.
* Saves document metadata (student ID, filename, S3 path) in a MySQL database.
* Displays a list of all uploaded documents.
* Generates secure, temporary (10-minute) download links for each document.
* Includes a full suite of unit tests to ensure functionality.

---

## Prerequisites

Before you begin, ensure you have the following installed on your local machine:

* PHP (version 8.1 or higher)
* Composer
* Node.js & npm (or yarn)
* A local database server (e.g., MySQL)
* An AWS Account

---

## Setup Instructions

### 1. Clone the Repository

First, clone this project to your local machine.

```bash
git clone <your-repository-url>
cd <project-folder>
```

---

### 2. Install Dependencies

Install the required PHP and JavaScript dependencies.

```bash
composer install
npm install
npm run dev
```

---

### 3. Environment Configuration

Copy the example environment file and generate an application key.

```bash
cp .env.example .env
php artisan key:generate
```

---

### 4. Set Up AWS S3

You will need to create an S3 bucket and an IAM user with the necessary permissions.

#### Create an S3 Bucket:

* Go to the **AWS S3 Console** and create a new bucket.
* Choose a unique name and a region (e.g., `ap-southeast-1`).
* Keep the bucket private (leave "Block all public access" checked).

#### Create an IAM User:

* Go to the **AWS IAM Console** and create a new user.
* Select **Programmatic access** to generate an **Access Key ID** and **Secret Access Key**.
* Attach a policy that gives the user permission to upload and download files from your bucket. Use the following policy:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "PublicReadForGetBucketObjects",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::YOUR-BUCKET-NAME-HERE/*"
        }
    ]
}
```

> **Note:** Replace `YOUR-BUCKET-NAME-HERE` with your actual bucket name.

---

### 5. Configure Your `.env` File

Open the `.env` file and update the database and AWS configuration.

```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=student_documents
DB_USERNAME=root
DB_PASSWORD=

# AWS Configuration
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=YOUR_AWS_ACCESS_KEY_ID
AWS_SECRET_ACCESS_KEY=YOUR_AWS_SECRET_ACCESS_KEY
AWS_DEFAULT_REGION=YOUR_AWS_REGION
AWS_BUCKET=YOUR_AWS_BUCKET_NAME
```

> **Note:** Replace the placeholders with your actual database and AWS credentials.

---

### 6. Run Database Migrations

Create the necessary database tables.

```bash
php artisan migrate
```

---

### 7. Running the Application

Start the local development server.

```bash
php artisan serve
```

You can now access the application in your browser at:

[http://127.0.0.1:8000](http://127.0.0.1:8000)

---

### 8. Running Tests

Run the full test suite to verify that the application is working correctly.

```bash
php artisan test
```

---

## Summary

✅ File upload system for student documents
✅ AWS S3 secure storage
✅ MySQL metadata management
✅ Temporary download links
✅ Fully tested with Laravel's testing suite

---

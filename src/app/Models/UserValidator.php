<?php

namespace App\Models;

class UserValidator 
{
    // firstname, surname, email, password_hash

    public function validate(array $user): array
    {
        $errors = [];

        if ($error = $this->validateFirstname($user['firstname'] ?? '')) {
            $errors['firstname'] = $error;
        }

        if ($error = $this->validateLastname($user['surname'] ?? '')) {
            $errors['lastname'] = $error;
        }

        if ($error = $this->validateEmail($user['email'] ?? '')) {
            $errors['email'] = $error;
        }
        
        if ($error = $this->validatePassword($user['password'] ?? '')) {
            $errors['password'] = $error;
        }

        return $errors;
    }

    private function validateFirstname(string $firstname): ?string
    {
        if (strlen($firstname) === 0) {
            return 'First name can\'t be empty';
        } else if (strlen($firstname) > 32) {
            return 'First name can\'t be longer than 32 symbols';
        }

        return null;
    }

    private function validateLastname(string $lastname): ?string
    {
        if (strlen($lastname) === 0) {
            return 'Last name can\'t be empty';
        } else if (strlen($lastname) > 32) {
            return 'Last name can\'t be longer than 32 symbols';
        }

        return null;
    }

    private function validateEmail(string $email): ?string {
        if ($email === '') {
            return 'Email can\'t be empty';
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return 'Invalid email format';
        }

        return null;
    }

    private function validatePassword(string $password): ?string {
        if (strlen($password) < 5) {
            return "Password can't be less than 5 symbols";
        }

        if (strlen($password) > 16) {
            return "Password can't be longer than 16 symbols";
        }

        return null;
    }
}
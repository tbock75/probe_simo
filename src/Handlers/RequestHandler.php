<?php

namespace App\Handlers;

class RequestHandler
{
    /**
     * This is were all sanitized values ARE STORED
     * @var array
     */
    private array $sanitizedInput = [];

    /**
     * Error Messages
     * @var array
     */
    private array $errors = [];

    private array|bool $required;

    public function __construct(array|bool $required = false)
    {
        $this->required = $required;
    }
    /**
     * Sanatizes and validates values
     *
     * @param array $input
     */
    public function sanitize(array $input): void
    {
        foreach ($input as $key => $value) {
            if($key === 'email' && $this->validateEmail($value) === false) {
                $this->errors[] = "Wert ist keine gültige E-Mail-Adresse";
            }
            $this->sanitizedInput[$key] = $this->sanitizeValue($value);
            $this->validateRequired($key, $value);
        }
    }

    /**
     * Reinigt einen einzelnen Eingabewert
     *
     * @param mixed $value Der zu reinigende Wert
     * @return string
     */
    private function sanitizeValue(mixed $value): string
    {
        if (is_array($value)) {
            return '';
        }

        $value = trim($value);
        $value = strip_tags($value);
        $value = htmlspecialchars($value);

        return $value;
    }

    /**
     * Validiert eine E-Mail-Adresse
     *
     * @param string $email
     * @return bool
     */
    public function validateEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'Ungültige E-Mail-Adresse';
            return false;
        }
        return true;
    }

    /**
     * tests all required variables if they are empty
     *
     * @param string $field
     * @return bool
     */
    public function validateRequired(string $field, string $value): bool
    {
        if($this->required !== false) {
            if (in_array($field, $this->required) && empty($value)) {
                $this->errors[] = "Das Feld '{$field}' ist erforderlich.";
                return false;
            }
        }
        return true;
    }

    /**
     * Returns one specific value
     *
     * @param string $key
     * @return string|null
     */
    public function get(string $key): ?string
    {
        return $this->sanitizedInput[$key] ?? null;
    }

    /**
     * Returns all sanitized variables
     *
     * @return array
     */
    public function all(): array
    {
        return $this->sanitizedInput;
    }

    /**
     * tests if there are any errors
     * returns true in case of errors
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * return all error messages
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
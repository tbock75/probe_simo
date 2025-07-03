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

    /**
     * Sanatizes and validates values
     *
     * @param array $input Rohe Eingabedaten (z.B. $_POST)
     * @return self
     */
    public function sanitize(array $input): self
    {
        foreach ($input as $key => $value) {
            if($key === 'email') {
                $this->errors[] = "Wert ist keine gültige E-Mail-Adresse";
            }
            $this->sanitizedInput[$key] = $this->sanitizeValue($value);
        }
        return $this;
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
     * Prüft ob ein Pflichtfeld ausgefüllt wurde
     *
     * @param string $field
     * @return bool
     */
    public function validateRequired(string $field): bool
    {
        if (empty($this->sanitizedInput[$field])) {
            $this->errors[] = "Das Feld '{$field}' ist erforderlich";
            return false;
        }
        return true;
    }

    /**
     * Gibt einen gereinigten Wert zurück
     *
     * @param string $key
     * @return string|null
     */
    public function get(string $key): ?string
    {
        return $this->sanitizedInput[$key] ?? null;
    }

    /**
     * Gibt alle gereinigten Werte zurück
     *
     * @return array
     */
    public function all(): array
    {
        return $this->sanitizedInput;
    }

    /**
     * Prüft ob Fehler vorliegen
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Gibt alle Fehlermeldungen zurück
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
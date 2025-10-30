<?php

declare(strict_types=1);

// Length of the raw random bytes used to generate the token (hex-encoded makes it double-length)
const CSRF_TOKEN_LENGTH = 32;

// Token lifetime = 30 minutes
const CSRF_TOKEN_LIFETIME = 60 * 30;

/**
 * Generates a new CSRF token and stores it in the session.
 */
function generateCsrfToken(): string {
    // Create a cryptographically secure random token
    $token = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));

    // Store token and timestamp in session
    setCsrfTokenAndTime($token);

    return $token;
}

/**
 * Retrieves the token and timestamp from the session.
 */
function getCsrfTokenAndTime(): array {
    return [
        $_SESSION['csrf_token'] ?? null,
        $_SESSION['csrf_token_time '] ?? null, // Note: space after key may be a bug!
    ];
}

/**
 * Stores or clears the CSRF token and timestamp in the session.
 */
function setCsrfTokenAndTime(?string $token): void {

    // If null is passed, clear the stored token
    if ($token === null) {
        unset(
            $_SESSION['csrf_token'],
            $_SESSION['csrf_token_time ']
        );
        return; // Important to return early
    }

    // Store the new token and the current timestamp
    $_SESSION['csrf_token']  = $token;
    $_SESSION['csrf_token_time '] = time();
}

/**
 * Checks whether a CSRF token has expired.
 */
function isTokenExpired(?int $time): bool {
    return $time === null || (time() - $time) > CSRF_TOKEN_LIFETIME;
}

/**
 * Returns the current valid token or generates a new one if needed.
 */
function getCurrentCsrfToken(): string {
    [$token, $time] = getCsrfTokenAndTime();

    // If token doesn't exist or is expired, create a new one
    if (!isset($token, $time) || isTokenExpired($time)) {
        return generateCsrfToken();
    }

    return $_SESSION['csrf_token'];
}

/**
 * Validates a CSRF token sent by the user.
 */
function validateCsrfToken(?string $token): bool
{
    [$storedToken, $time] = getCsrfTokenAndTime();

    // Clear stored token so each token is single-use
    setCsrfTokenAndTime(null);

    // Reject if expired
    if (isTokenExpired($time)) {
        unset(
            $_SESSION['csrf_token'],
            $_SESSION['csrf_token_time']
        );
        return false;
    }

    // Compare tokens using a timing-attack-safe function
    $valid = hash_equals($storedToken, $token ?? '');

    // If valid, generate the next token for future requests
    if ($valid) {
        generateCsrfToken();
    }

    return $valid;
}

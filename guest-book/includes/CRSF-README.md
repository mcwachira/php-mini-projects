README — CSRF Tokens in PHP
What is CSRF?

CSRF (Cross-Site Request Forgery) is a type of security attack where a malicious website tricks a logged-in user into performing actions on another site without their consent.
Example: a malicious site secretly submitting a form to yourbank.com/transfer-money.

Without protection, the server might think the user intended to do this.

How CSRF Tokens Protect You

A CSRF token is a secure, random, one-time value stored in the user's session and added to every form or state-changing request.

✅ How it prevents attacks:

Your application generates a token and stores it in the session.

The same token must be submitted with the form.

On submit, the server:

checks that the token matches the one in the session

checks that it hasn't expired

If anything is wrong → the request is rejected.

A malicious site cannot read the user’s session, so it cannot know the token, meaning the forged request fails.

Implementation Flow
On page load (inside your form):
$token = getCurrentCsrfToken();

In your HTML form:
<input type="hidden" name="csrf_token" value="<?= $token ?>">

When form is submitted:
if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
die("Invalid CSRF token");
}

Security Features in This Implementation

✅ Secure random tokens using random_bytes()
✅ Token expiration (30 minutes)
✅ One-time use tokens
✅ Automatically regenerates new tokens
✅ Uses hash_equals() to prevent timing attacksCSRF Protection (OOP Implementation)

This project includes a secure, object-oriented CSRF protection system written in PHP.
It prevents Cross-Site Request Forgery (CSRF) attacks by using:

✅ Cryptographically secure random tokens
✅ Session-stored, per-user tokens
✅ Single-use tokens
✅ Token expiration (30 minutes)
✅ Timing-safe comparison

✅ How It Works

When a form is loaded, the CSRF manager generates (or retrieves) a token.

The token is stored in the user’s session and inserted into the form.

When the form is submitted, the token must match the stored token.

The token is validated in a secure, timing-attack-safe way.

Tokens are one-time use: after validation they are removed and replaced.

✅ Why This Protects Your Application

Attackers cannot:

Read the user's session

Predict the random token

Bypass the timing-safe comparison

Use expired tokens

Reuse tokens (single-use)

Thus, any forged request without the valid token will fail.
```php

<?php

declare(strict_types=1);

class Csrf
{
    private const TOKEN_LENGTH = 32;
    private const TOKEN_LIFETIME = 1800; // 30 minutes

    private const KEY_TOKEN = 'csrf_token';
    private const KEY_TIME  = 'csrf_token_time';

    /**
     * Create and store a new CSRF token.
     */
    public function generate(): string
    {
        $token = bin2hex(random_bytes(self::TOKEN_LENGTH));
        $this->storeToken($token);
        return $token;
    }

    /**
     * Get the current token or create a new one if expired or missing.
     */
    public function getToken(): string
    {
        [$token, $time] = $this->getStoredToken();

        if ($token === null || $time === null || $this->isExpired($time)) {
            return $this->generate();
        }

        return $token;
    }

    /**
     * Validate a submitted token.
     * Token is single-use, so it's deleted immediately.
     */
    public function validate(?string $token): bool
    {
        [$storedToken, $time] = $this->getStoredToken();
        $this->clearToken(); // enforce single-use

        // Reject if expired  
        if ($this->isExpired($time)) {
            return false;
        }

        // Timing-safe comparison
        $valid = ($storedToken !== null && hash_equals($storedToken, $token ?? ''));

        // If valid → prepare next token
        if ($valid) {
            $this->generate();
        }

        return $valid;
    }

    /**
     * Check if token has expired.
     */
    private function isExpired(?int $timestamp): bool
    {
        return $timestamp === null || (time() - $timestamp) > self::TOKEN_LIFETIME;
    }

    /**
     * Store token + time in session.
     */
    private function storeToken(string $token): void
    {
        $_SESSION[self::KEY_TOKEN] = $token;
        $_SESSION[self::KEY_TIME]  = time();
    }

    /**
     * Retrieve stored token + timestamp.
     */
    private function getStoredToken(): array
    {
        return [
            $_SESSION[self::KEY_TOKEN] ?? null,
            $_SESSION[self::KEY_TIME] ?? null,
        ];
    }

    /**
     * Remove token + timestamp (for single-use tokens).
     */
    private function clearToken(): void
    {
        unset(
            $_SESSION[self::KEY_TOKEN],
            $_SESSION[self::KEY_TIME]
        );
    }
}


```

# Auth Refactor Notes

This project refactors authentication into small services, repositories, and form requests to improve testability and reuse.

Key additions
- `App\Repositories\UserRepository` — user data access helper (`findByEmail`, `update`).
- `App\Services\AuthService` — centralizes credential checks, login, password confirm and update.
- `App\Services\RegistrationService` — handles user creation and login after registration.
- `App\Services\PasswordService` — wraps the password broker (`sendResetLink`, `reset`).
- `App\Services\VerificationService` — wraps verification actions (`sendVerification`, `markVerified`).

Requests
- `app/Http/Requests/Auth/RegisterRequest.php`
- `app/Http/Requests/Auth/LoginRequest.php` (delegates to `AuthService`)
- `app/Http/Requests/Auth/PasswordResetLinkRequest.php`
- `app/Http/Requests/Auth/ResetPasswordRequest.php`

Controllers
Controllers in `app/Http/Controllers/Auth` were updated to delegate to services and use the request classes above.

Bindings
Bindings for the repository and services are registered in `App\Providers\AppServiceProvider::register`.

Why
- Keeps controllers thin and focused on HTTP concerns.
- Makes authentication logic easier to unit test by isolating it in services.
- Enables swapping implementations or adding logging/auditing around auth flows.

Next steps
- Add unit tests for `AuthService`, `PasswordService`, and `RegistrationService`.
- (Optional) Extract interfaces for the services and bind interfaces to implementations for easier mocking.

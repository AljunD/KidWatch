# KidWatch Routes Documentation

This document describes the HTTP routes defined in `web.php` for the KidWatch Laravel 11 application.

---

## Authentication Routes

### Login
- **GET** `/login` → `TeacherLoginController@showLoginForm`  
  Displays the login form.  
  Named route: `login.form`

- **POST** `/login` → `TeacherLoginController@login`  
  Handles login submission.  
  Middleware: `throttle:5,1` (limits to 5 attempts per minute).  
  Named route: `login`

### Logout
- **POST** `/logout` → `TeacherLoginController@logout`  
  Logs out the current user.  
  Named route: `logout`

### Forgot Password
- **GET** `/forgot-password` → `TeacherLoginController@showForgotPasswordForm`  
  Displays the forgot password form.  
  Named route: `password.request`

- **POST** `/forgot-password` → `TeacherLoginController@sendResetLink`  
  Sends a password reset link.  
  Named route: `password.email`

### Reset Password
- **GET** `/reset-password/{token}` → Closure returning `reset-password` view  
  Displays the reset password form with token.  
  Named route: `password.reset`

- **POST** `/reset-password` → `TeacherLoginController@resetPassword`  
  Handles password reset submission.  
  Named route: `password.update`

---

## Email Verification Routes

- **GET** `/email/verify` → `VerificationController@notice`  
  Shows the email verification notice.  
  Middleware: `auth`  
  Named route: `verification.notice`

- **GET** `/email/verify/{id}/{hash}` → `VerificationController@verify`  
  Handles verification link.  
  Middleware: `auth`, `signed`  
  Named route: `verification.verify`

- **POST** `/email/resend` → `VerificationController@resend`  
  Resends verification email.  
  Middleware: `auth`, `throttle:6,1`  
  Named route: `verification.resend`

---

## Dashboard

- **GET** `/dashboard` → `DashboardController@index`  
  Displays the dashboard.  
  Middleware: `TeacherAuthMiddleware`, `ContentSecurityPolicy`, `verified`  
  Named route: `dashboard`

---

## Student Routes

Protected by `TeacherAuthMiddleware`, rate limited (`throttle:60,1`), and require verified email.

- **GET** `/students` → `StudentController@index`  
  Lists students.  
  Named route: `students`

- **POST** `/students/store-with-guardian` → `StudentController@storeWithGuardian`  
  Creates a student with guardian.  
  Named route: `students.storeWithGuardian`

- **PUT** `/students/{id}` → `StudentController@update`  
  Updates student record.  
  Named route: `students.update`

- **DELETE** `/students/{id}` → `StudentController@destroy`  
  Deletes student record.  
  Named route: `students.destroy`

### Trash Routes
- **GET** `/students/trash` → `StudentController@trash`  
  Shows trashed students.  
  Named route: `students.trash`

- **POST** `/students/{id}/restore` → `StudentController@restore`  
  Restores a trashed student.  
  Named route: `students.restore`

- **DELETE** `/students/{id}/force-delete` → `StudentController@forceDelete`  
  Permanently deletes a student.  
  Named route: `students.forceDelete`

---

## Progress

- **GET** `/progress` → Closure returning `progress` view  
  Displays progress tracking.  
  Middleware: `TeacherAuthMiddleware`, `ContentSecurityPolicy`, `verified`  
  Named route: `progress`

---

## Middleware Summary
- **ContentSecurityPolicy** → Applied to all authentication and verification routes.  
- **TeacherAuthMiddleware** → Protects dashboard, students, trash, and progress routes.  
- **verified** → Ensures email verification before accessing protected routes.  
- **throttle** → Rate limiting applied to login (5/min) and student routes (60/min).

Here’s a complete **Documentation.md** you can drop into your KidWatch repo. It’s structured to be a standalone developer manual, covering everything from overview to troubleshooting:

```markdown
# 📖 KidWatch Documentation

## 1. Project Overview
KidWatch is a mobile-first application for Barangay Balite Day Care Center that enables teachers to digitally record and monitor children’s weekly learning progress per subject. It replaces manual logbooks with a structured digital workflow, providing organized data management and AI-assisted summarized insights using pure PHP/Laravel logic.

### Goals
1. Enable teachers to record weekly subject-based progress.
2. Provide structured data management for student records.
3. Generate AI-assisted summaries using built-in PHP/Laravel logic.

### Target Users
- **Teachers (Admin)**: Create/manage student profiles, input weekly progress, view records, access summaries.
- **Parents (View-only)**: View their child’s progress and summaries.

### Scope & Limitations
- ✅ Student profiles, weekly progress, AI summaries, JWT authentication with RBAC.
- ❌ No messaging, notifications, enrollment, attendance, finance, or medical/behavioral features.

---

## 2. Folder Structure
- **backend/** – Laravel 11 RESTful API
  - `app/` – Models, controllers, services
  - `routes/` – API routes
  - `database/` – Migrations, seeders
  - `config/` – Laravel configs
  - `.env.example` – Environment template
- **mobile/** – React Native (Expo) app
  - `App.tsx` – Root component
  - `index.tsx` – Entry point
  - `app.json` – Expo config
  - `eas.json` – Expo build config
- **README.md** – Quick start guide
- **Documentation.md** – Full developer manual
- **.gitignore** – Excludes dependencies, env files, build artifacts

---

## 3. API Documentation

| Method | Route              | Description                  | Auth            | Example Request | Example Response |
|--------|--------------------|------------------------------|-----------------|-----------------|------------------|
| POST   | /api/auth/login    | User login (JWT)             | Public          | `{ "email":"...","password":"..." }` | `{ "token":"..." }` |
| GET    | /api/students      | List all students            | Teacher         | —               | `[{"id":1,"name":"..."}]` |
| POST   | /api/students      | Create student               | Teacher         | `{ "name":"..." }` | `{ "id":1,"name":"..." }` |
| GET    | /api/progress/{id} | Get weekly progress          | Teacher/Parent  | —               | `{ "subject":"Math","week":1,"notes":"..." }` |
| POST   | /api/progress      | Add weekly progress          | Teacher         | `{ "student_id":1,"subject":"Math","week":1,"notes":"..." }` | `{ "id":1,... }` |
| GET    | /api/summary/{id}  | AI summary of progress       | Teacher/Parent  | —               | `{ "summary":"This week’s insights..." }` |

---

## 4. Setup Instructions for New Developers

### Clone the repository
```bash
git clone <repo-url> KidWatch
cd KidWatch
```

### Backend setup (Laravel + Laragon)
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Frontend setup (React Native + Expo)
```bash
cd ../mobile
npm install
npx expo start
```

### Database configuration
- Create a MySQL database named `kidwatch` in Laragon.
- Update `backend/.env`:
  ```env
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=kidwatch
  DB_USERNAME=root
  DB_PASSWORD=
  ```

### Environment variables
- Backend: `.env` for DB and JWT secret.
- Mobile: API base URL in `mobile/constants/api.ts`.

### Running locally
- Backend: `php artisan serve` → http://127.0.0.1:8000
- Mobile: `npx expo start` → connect via Expo Go or emulator.

### Deploying
- Backend: Deploy Laravel to any PHP/MySQL hosting.
- Mobile: Use Expo EAS to build and publish to Google Play Store.

---

## 5. Available Commands

**Backend**
- `php artisan serve` – Start server
- `php artisan migrate:fresh --seed` – Reset DB

**Frontend**
- `npx expo start` – Start Expo
- `npx expo start -c` – Clear cache

**Git**
- `git add -A && git commit -m "msg" && git push origin main`

---

## 6. Change Monitoring & Version Control
- Branching: `main` (stable), `dev` (integration), `feature/*` (features).
- Commit conventions: `feat:`, `fix:`, `docs:`, `chore:`.
- Maintain `CHANGELOG.md`.

---

## 7. Deployment Guides
- **Backend**: Upload Laravel project to hosting, configure `.env`, run migrations.
- **Mobile**: Use Expo EAS to build `.apk` or `.aab`, then publish to Google Play Store.

---

## 8. Built-in AI Summarization Logic
- Implemented as a Laravel service class.
- Analyzes weekly progress records per subject.
- Produces natural-language summaries (e.g., “Math shows improvement, Reading needs attention”).
- No external APIs or models — pure PHP logic.

---

## 9. Troubleshooting
- **DB connection error** → Check `.env` credentials.
- **Expo not loading** → Clear cache: `npx expo start -c`.
- **Missing tables** → Run `php artisan migrate --seed`.
- **JWT errors** → Ensure `JWT_SECRET` is set in `.env`.

---
```
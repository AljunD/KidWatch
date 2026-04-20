# KidWatch Models Documentation

This document describes the Eloquent models defined in the KidWatch Laravel 11 application.

---

## Guardian Model

**Namespace:** `App\Models\Guardian`  
**Traits:** `HasFactory`, `SoftDeletes`

### Fillable Attributes
- `user_id`
- `first_name`
- `middle_name`
- `last_name`
- `relationship_to_child`
- `contact_number`
- `address`

### Relationships
- **user()** → `BelongsTo(User::class)`  
  Links guardian to its user account.

- **students()** → `HasMany(Student::class, 'guardian_id')`  
  A guardian can have many students.

### Helpers
- **getFullNameAttribute()** → Returns `"first_name last_name"`.

---

## Student Model

**Namespace:** `App\Models\Student`  
**Traits:** `HasFactory`, `SoftDeletes`

### Fillable Attributes
- `guardian_id`
- `first_name`
- `middle_name`
- `last_name`
- `gender`
- `date_of_birth`
- `nationality`
- `religion`

### Casts
- `date_of_birth` → `date`

### Relationships
- **guardian()** → `BelongsTo(Guardian::class, 'guardian_id')`  
  Each student belongs to one guardian.

- **progressRecords()** → `HasMany(ProgressRecord::class)`  
  A student can have many progress records.

- **weeklySummaries()** → `HasMany(WeeklySummary::class)`  
  A student can have many weekly summaries.

### Helpers
- **getAgeAttribute()** → Returns age in years based on `date_of_birth`.

---

## ProgressRecord Model

**Namespace:** `App\Models\ProgressRecord`  
**Traits:** `HasFactory`, `SoftDeletes`

### Fillable Attributes
- `student_id`
- `week_number`
- `subject`
- `rating`

### Relationships
- **student()** → `BelongsTo(Student::class)`  
  Each progress record belongs to one student.

---

## WeeklySummary Model

**Namespace:** `App\Models\WeeklySummary`  
**Traits:** `HasFactory`, `SoftDeletes`

### Fillable Attributes
- `student_id`
- `week_number`
- `summary_text`

### Relationships
- **student()** → `BelongsTo(Student::class)`  
  Each weekly summary belongs to one student.

---

## RecommendationEngineConfig Model

**Namespace:** `App\Models\RecommendationEngineConfig`  
**Traits:** `HasFactory`, `SoftDeletes`

### Table
- Explicitly sets `$table = 'recommendation_engine_configs'`.

### Fillable Attributes
- `subject`
- `rating`
- `intervention_text`

---

## Summary of Relationships

- **Guardian → User**: One guardian belongs to one user.  
- **Guardian → Students**: One guardian has many students.  
- **Student → Guardian**: Each student belongs to one guardian.  
- **Student → ProgressRecords**: One student has many progress records.  
- **Student → WeeklySummaries**: One student has many weekly summaries.  
- **ProgressRecord → Student**: Each progress record belongs to one student.  
- **WeeklySummary → Student**: Each weekly summary belongs to one student.  
- **RecommendationEngineConfig**: Standalone configuration table, no direct relationships.

---

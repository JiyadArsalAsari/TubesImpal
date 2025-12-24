# Learning Development Database Structure

## Table Structures

### 1. users
Stores authentication data for all users.

Fields:
- `id` (BIGINT, UNSIGNED, AUTO_INCREMENT, PRIMARY KEY)
- `name` (VARCHAR)
- `email` (VARCHAR, UNIQUE)
- `username` (VARCHAR, UNIQUE)
- `email_verified_at` (TIMESTAMP, NULLABLE)
- `password` (VARCHAR)
- `role` (ENUM: 'mahasiswa', 'dosen')
- `remember_token` (VARCHAR, NULLABLE)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

### 2. mahasiswa
Stores academic data for students, linked to users.

Fields:
- `id` (BIGINT, UNSIGNED, AUTO_INCREMENT, PRIMARY KEY)
- `mahasiswaID` (VARCHAR, UNIQUE)
- `nama` (VARCHAR)
- `user_id` (BIGINT, UNSIGNED, FOREIGN KEY to users.id)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

### 3. learning_developments
Tracks overall learning development records for students.

Fields:
- `id` (BIGINT, UNSIGNED, AUTO_INCREMENT, PRIMARY KEY)
- `mahasiswa_id` (BIGINT, UNSIGNED, FOREIGN KEY to mahasiswa.id)
- `description` (TEXT, NULLABLE)
- `recorded_at` (DATE)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

### 4. exercises
Stores exercise/quiz materials.

Fields:
- `id` (BIGINT, UNSIGNED, AUTO_INCREMENT, PRIMARY KEY)
- `title` (VARCHAR)
- `description` (TEXT, NULLABLE)
- `type` (ENUM: 'materi', 'latihan', 'quiz')
- `max_score` (INTEGER, DEFAULT 100)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

### 5. exercise_results
Stores results of exercise attempts by students.

Fields:
- `id` (BIGINT, UNSIGNED, AUTO_INCREMENT, PRIMARY KEY)
- `mahasiswa_id` (BIGINT, UNSIGNED, FOREIGN KEY to mahasiswa.id)
- `exercise_id` (BIGINT, UNSIGNED, FOREIGN KEY to exercises.id)
- `score` (DECIMAL 5,2)
- `attempted_at` (DATETIME)
- `status` (ENUM: 'lulus', 'tidak_lulus')
- `attempt_number` (INTEGER, DEFAULT 1)
- `feedback` (TEXT, NULLABLE)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

## Relationships

1. `users` ←→ `mahasiswa` (One-to-One)
   - users.id ← mahasiswa.user_id

2. `mahasiswa` ←→ `learning_developments` (One-to-Many)
   - mahasiswa.id ← learning_developments.mahasiswa_id

3. `mahasiswa` ←→ `exercise_results` (One-to-Many)
   - mahasiswa.id ← exercise_results.mahasiswa_id

4. `exercises` ←→ `exercise_results` (One-to-Many)
   - exercises.id ← exercise_results.exercise_id

## Example Queries

### 1. Get average scores over time for a specific student

```sql
SELECT 
    DATE(er.attempted_at) as date,
    AVG(er.score) as average_score
FROM exercise_results er
WHERE er.mahasiswa_id = ?
GROUP BY DATE(er.attempted_at)
ORDER BY DATE(er.attempted_at);
```

### 2. Get monthly average scores for trend analysis

```sql
SELECT 
    YEAR(er.attempted_at) as year,
    MONTH(er.attempted_at) as month,
    AVG(er.score) as average_score,
    COUNT(er.id) as total_attempts
FROM exercise_results er
WHERE er.mahasiswa_id = ?
GROUP BY YEAR(er.attempted_at), MONTH(er.attempted_at)
ORDER BY YEAR(er.attempted_at), MONTH(er.attempted_at);
```

### 3. Get exercise performance trends

```sql
SELECT 
    e.title,
    e.type,
    COUNT(er.id) as total_attempts,
    AVG(er.score) as average_score,
    MAX(er.attempted_at) as last_attempted
FROM exercises e
JOIN exercise_results er ON e.id = er.exercise_id
WHERE er.mahasiswa_id = ?
GROUP BY e.id, e.title, e.type
ORDER BY AVG(er.score) DESC;
```

### 4. Get passing rate over time

```sql
SELECT 
    DATE(er.attempted_at) as date,
    COUNT(CASE WHEN er.status = 'lulus' THEN 1 END) as passed_count,
    COUNT(*) as total_count,
    (COUNT(CASE WHEN er.status = 'lulus' THEN 1 END) * 100.0 / COUNT(*)) as pass_rate
FROM exercise_results er
WHERE er.mahasiswa_id = ?
GROUP BY DATE(er.attempted_at)
ORDER BY DATE(er.attempted_at);
```

### 5. Get overall learning development summary

```sql
SELECT 
    COUNT(DISTINCT er.exercise_id) as total_exercises_completed,
    AVG(er.score) as overall_average_score,
    COUNT(CASE WHEN er.status = 'lulus' THEN 1 END) as total_passed,
    COUNT(*) as total_attempts,
    MIN(er.attempted_at) as first_attempt,
    MAX(er.attempted_at) as latest_attempt
FROM exercise_results er
WHERE er.mahasiswa_id = ?;
```

These queries provide the foundation for generating the time-series charts and analytics for the Learning Development feature.
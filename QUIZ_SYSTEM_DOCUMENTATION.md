# Quiz System - Complete Implementation Guide

## Overview
This document describes the complete quiz creation and submission system for the LMS platform. Teachers can create quizzes for their courses, and students can take and submit them with automatic scoring.

---

## Database Schema

### 1. **quizzes** Table (Modified)
Added columns:
- `course_id` (INT, FK to courses) - Direct course association
- `teacher_id` (INT, FK to users) - Teacher who created the quiz

Existing columns:
- `id` (Primary Key)
- `lesson_id` (INT, nullable) - Optional lesson association
- `title` (VARCHAR 200)
- `description` (TEXT, nullable)
- `question` (TEXT)
- `question_type` (ENUM: multiple_choice, true_false, essay, fill_blank)
- `option_a`, `option_b`, `option_c`, `option_d` (VARCHAR 255)
- `correct_answer` (VARCHAR 10)
- `points` (INT, default 1)
- `created_at`, `updated_at` (DATETIME)

### 2. **quiz_submissions** Table (New)
- `id` (Primary Key)
- `quiz_id` (INT, FK to quizzes)
- `student_id` (INT, FK to users)
- `score` (DECIMAL 5,2) - Percentage score
- `total_points` (INT) - Total possible points
- `submitted_at` (DATETIME)
- `created_at`, `updated_at` (DATETIME)

### 3. **quiz_answers** Table (New)
- `id` (Primary Key)
- `submission_id` (INT, FK to quiz_submissions)
- `quiz_id` (INT, FK to quizzes)
- `question_index` (INT) - Index of question in quiz
- `student_answer` (TEXT) - Student's answer
- `is_correct` (TINYINT 1) - 1 if correct, 0 if wrong
- `points_earned` (INT) - Points awarded
- `created_at` (DATETIME)

---

## Migrations

### Files Created:
1. `2025-01-15-100000_ModifyQuizzesAddCourseTeacher.php` - Adds course_id and teacher_id to quizzes table
2. `2025-01-15-100001_CreateQuizSubmissionsTable.php` - Creates quiz_submissions table
3. `2025-01-15-100002_CreateQuizAnswersTable.php` - Creates quiz_answers table

### Running Migrations:
```bash
php spark migrate
```

---

## Models

### 1. **QuizModel** (`app/Models/QuizModel.php`)
**Methods:**
- `getQuizzesByCourse($courseId)` - Get all quizzes for a course
- `getQuizWithStats($courseId)` - Get quizzes with submission count and pass rate
- `getQuizDetails($quizId)` - Get quiz with teacher and course info
- `createQuiz($data)` - Create new quiz
- `deleteQuiz($quizId)` - Delete quiz

**Validation Rules:**
- `course_id` - Required, integer
- `teacher_id` - Required, integer
- `title` - Required, max 200 characters
- `question` - Required
- `question_type` - Required, in_list[multiple_choice, true_false, essay, fill_blank]
- `points` - Required, integer, greater than 0

### 2. **QuizSubmissionModel** (`app/Models/QuizSubmissionModel.php`)
**Methods:**
- `hasSubmitted($quizId, $studentId)` - Check if student already submitted
- `getStudentSubmission($quizId, $studentId)` - Get student's submission
- `getQuizSubmissions($quizId)` - Get all submissions for a quiz
- `getStudentSubmissions($studentId)` - Get all submissions by a student
- `createSubmission($data)` - Create new submission
- `updateScore($submissionId, $score, $totalPoints)` - Update submission score

### 3. **QuizAnswerModel** (`app/Models/QuizAnswerModel.php`)
**Methods:**
- `getSubmissionAnswers($submissionId)` - Get all answers for a submission
- `saveAnswers($answers)` - Batch save student answers
- `getAnswer($submissionId, $questionIndex)` - Get specific answer

---

## Controllers

### Teacher Controller (`app/Controllers/Teacher.php`)

#### **1. getQuizzes($courseId)** - GET
**URL:** `/teacher/quizzes/{courseId}`
**Purpose:** Get all quizzes for a course with submission stats
**Response:**
```json
{
  "status": "success",
  "quizzes": [
    {
      "id": 1,
      "title": "Midterm Quiz",
      "description": "Covers chapters 1-5",
      "submissions": 15,
      "passed": 12,
      "created_at": "2025-01-15 10:00:00"
    }
  ]
}
```

#### **2. createQuiz()** - POST
**URL:** `/teacher/quiz/create`
**Purpose:** Create a new quiz
**Request Data:**
- `course_id` (required)
- `title` (required)
- `description` (optional)
- `question` (required)
- `question_type` (required: multiple_choice, true_false)
- `option_a`, `option_b`, `option_c`, `option_d` (required for multiple choice)
- `correct_answer` (required: A, B, C, D)
- `points` (required, integer)

**Response:**
```json
{
  "status": "success",
  "message": "Quiz created successfully",
  "quiz_id": 5
}
```

**Notifications:** Sends notification to all enrolled students

#### **3. deleteQuiz($quizId)** - POST
**URL:** `/teacher/quiz/delete/{quizId}`
**Purpose:** Delete a quiz and all its submissions
**Response:**
```json
{
  "status": "success",
  "message": "Quiz deleted successfully"
}
```

#### **4. getQuizSubmissions($quizId)** - GET
**URL:** `/teacher/quiz/{quizId}/submissions`
**Purpose:** Get all student submissions for a quiz
**Response:**
```json
{
  "status": "success",
  "submissions": [
    {
      "id": 1,
      "student_name": "John Doe",
      "student_email": "john@example.com",
      "score": 85.00,
      "submitted_at": "2025-01-15 11:30:00"
    }
  ]
}
```

---

### Auth Controller (`app/Controllers/Auth.php`) - Student Methods

#### **1. getCourseQuizzes($courseId)** - GET
**URL:** `/student/course/{courseId}/quizzes`
**Purpose:** Get all quizzes for enrolled course
**Response:**
```json
{
  "status": "success",
  "quizzes": [
    {
      "id": 1,
      "title": "Midterm Quiz",
      "description": "Covers chapters 1-5",
      "points": 10,
      "submitted": true,
      "score": 85.00
    }
  ]
}
```

#### **2. getQuiz($quizId)** - GET
**URL:** `/student/quiz/{quizId}`
**Purpose:** Get quiz details for taking
**Response:**
```json
{
  "status": "success",
  "quiz": {
    "id": 1,
    "title": "Midterm Quiz",
    "question": "What is 2+2?",
    "question_type": "multiple_choice",
    "option_a": "3",
    "option_b": "4",
    "option_c": "5",
    "option_d": "6",
    "points": 10
  }
}
```

#### **3. submitQuiz()** - POST
**URL:** `/student/quiz/submit`
**Purpose:** Submit quiz answer
**Request Data:**
- `quiz_id` (required)
- `student_answer` (required: A, B, C, D)

**Response:**
```json
{
  "status": "success",
  "message": "Quiz submitted successfully!",
  "score": 100.00,
  "is_correct": true,
  "points_earned": 10,
  "total_points": 10
}
```

**Scoring Logic:**
- Compares student answer with `correct_answer`
- Calculates percentage: `(points_earned / total_points) * 100`
- Passing score: 75%

**Notifications:** Sends notification to teacher with student name and score

---

## Frontend Implementation

### Teacher Dashboard (`app/Views/teacher_dashboard.php`)

#### Modals:
1. **Quizzes Modal** (#quizzesModal) - List all quizzes
2. **Create Quiz Modal** (#createQuizModal) - Create new quiz
3. **View Quiz Submissions Modal** (#viewQuizSubmissionsModal) - View student submissions

#### JavaScript Functions:
- `openQuizzesModal(courseId, courseCode)` - Open quizzes modal
- `loadQuizzes(courseId)` - AJAX load quizzes with stats
- `renderQuizzesList(quizzes)` - Render quiz cards
- `showCreateQuizForm(courseId)` - Show create modal
- `createQuiz()` - Submit new quiz via AJAX
- `deleteQuiz(id)` - Delete quiz with confirmation
- `viewQuizSubmissions(quizId)` - Show submissions modal
- `loadQuizSubmissions(quizId)` - AJAX load submissions
- `renderQuizSubmissionsList(submissions)` - Render submission table

#### Features:
- Dynamic question type switching (Multiple Choice ↔ True/False)
- True/False auto-fills options A="True", B="False"
- Multiple Choice requires all 4 options
- Submission stats (total submissions, passed count)
- Color-coded scores (green ≥ 75%, red < 75%)

---

### Student Dashboard (`app/Views/auth/dashboard.php`)

#### Modals:
1. **Student Quizzes Modal** (#studentQuizzesModal) - List available quizzes
2. **Take Quiz Modal** (#takeQuizModal) - Answer quiz questions

#### JavaScript Functions:
- `loadStudentQuizzes(courseId)` - AJAX load available quizzes
- `renderStudentQuizzesList(quizzes)` - Render quiz list
- `takeQuiz(quizId)` - Load quiz for taking
- `displayQuiz(quiz)` - Display quiz in modal
- `submitStudentQuiz()` - Submit quiz answer via AJAX

#### UI Elements:
- **Quizzes Button** added to enrolled course cards
- Quiz list shows:
  - Title and description
  - Points available
  - "Take Quiz" button (if not submitted)
  - Score badge (if submitted, color-coded)
- Quiz taking interface:
  - Radio buttons for options
  - Submit button
  - Result alert with score

---

## Routes

### Teacher Routes (app/Config/Routes.php)
```php
$routes->group('teacher', ['filter' => 'roleauth'], function($routes){
    $routes->get('quizzes/(:num)', 'Teacher::getQuizzes/$1');
    $routes->post('quiz/create', 'Teacher::createQuiz');
    $routes->post('quiz/delete/(:num)', 'Teacher::deleteQuiz/$1');
    $routes->get('quiz/(:num)/submissions', 'Teacher::getQuizSubmissions/$1');
});
```

### Student Routes (app/Config/Routes.php)
```php
$routes->group('student', ['filter' => 'roleauth'], function($routes){
    $routes->get('course/(:num)/quizzes', 'Auth::getCourseQuizzes/$1');
    $routes->get('quiz/(:num)', 'Auth::getQuiz/$1');
    $routes->post('quiz/submit', 'Auth::submitQuiz');
});
```

---

## Testing Workflow

### Teacher Workflow:
1. ✅ Login as teacher
2. ✅ Navigate to Teacher Dashboard
3. ✅ Click "Quizzes" button for a course
4. ✅ Click "Create New Quiz"
5. ✅ Fill in:
   - Title: "Chapter 1 Quiz"
   - Description: "Test your knowledge"
   - Question: "What is 2+2?"
   - Question Type: Multiple Choice
   - Options: A=3, B=4, C=5, D=6
   - Correct Answer: B
   - Points: 10
6. ✅ Submit → Students receive notification
7. ✅ View quiz list → shows "0 submitted, 0 passed"
8. ✅ After student submission → shows "1 submitted, 1 passed" (if score ≥ 75%)
9. ✅ Click "View Submissions" → see student scores
10. ✅ Delete quiz (with confirmation)

### Student Workflow:
1. ✅ Login as student
2. ✅ Navigate to Student Dashboard
3. ✅ Expand enrolled course card
4. ✅ Click "Quizzes" button
5. ✅ See list of available quizzes
6. ✅ Click "Take Quiz" button
7. ✅ Read question and select answer
8. ✅ Click "Submit Quiz"
9. ✅ See immediate result: "Correct! Score: 100.00%" or "Incorrect. Score: 0.00%"
10. ✅ Teacher receives notification
11. ✅ Quiz marked as "Submitted" with score badge
12. ✅ Cannot retake quiz (button replaced with score)

---

## Notification System Integration

### When Teacher Creates Quiz:
```php
foreach ($students as $student) {
    $notificationModel->createNotification([
        'user_id' => $student['user_id'],
        'message' => 'New quiz "' . $title . '" has been created in ' . $course['name'],
        'type' => 'quiz'
    ]);
}
```

### When Student Submits Quiz:
```php
$notificationModel->createNotification([
    'user_id' => $quiz['teacher_id'],
    'message' => $student['name'] . ' submitted quiz "' . $quiz['title'] . '" - Score: ' . $scorePercentage . '%',
    'type' => 'quiz_submission'
]);
```

---

## Security & Validation

### Teacher Side:
- ✅ Verifies teacher owns the course before creating quiz
- ✅ Verifies teacher owns the quiz before deleting
- ✅ Validates all required fields (title, question, correct_answer, points)
- ✅ Question type enum validation
- ✅ Points must be integer > 0

### Student Side:
- ✅ Checks student is enrolled in course before showing quizzes
- ✅ Prevents submitting if already submitted
- ✅ Validates answer is provided
- ✅ Automatic scoring prevents tampering

---

## Database Relationships

```
courses
  ├── quizzes (via course_id)
  │     ├── quiz_submissions (via quiz_id)
  │     │     └── quiz_answers (via submission_id)
  │     └── quiz_answers (via quiz_id)
  └── enrollments (via course_id)

users (teachers)
  └── quizzes (via teacher_id)

users (students)
  ├── quiz_submissions (via student_id)
  └── enrollments (via user_id)
```

---

## Key Features

✅ **Instant Feedback:** Students see their score immediately after submission
✅ **Auto-Grading:** Automatic scoring based on correct_answer comparison
✅ **Prevention:** Cannot retake quiz once submitted
✅ **Stats Tracking:** Teacher sees submission count and pass rate
✅ **Notifications:** Real-time alerts for both teachers and students
✅ **Two Question Types:** Multiple Choice (4 options) and True/False (2 options)
✅ **Points System:** Flexible point values per quiz
✅ **Responsive UI:** Bootstrap modals and cards
✅ **AJAX:** No page reloads, smooth user experience

---

## Future Enhancements (Not Implemented)

- Multiple questions per quiz
- Essay and fill-in-the-blank question types
- Quiz time limits
- Quiz availability dates (start/end)
- Randomized question order
- Question bank/reuse
- Detailed answer explanations
- Quiz attempts limit
- Partial credit for multiple choice
- Student answer review after submission

---

## File Structure

```
app/
├── Controllers/
│   ├── Teacher.php (Quiz CRUD for teachers)
│   └── Auth.php (Quiz taking for students)
├── Models/
│   ├── QuizModel.php
│   ├── QuizSubmissionModel.php
│   └── QuizAnswerModel.php
├── Views/
│   ├── teacher_dashboard.php (Quiz creation UI)
│   └── auth/dashboard.php (Quiz taking UI)
├── Config/
│   └── Routes.php (Quiz routes)
└── Database/
    └── Migrations/
        ├── 2025-01-15-100000_ModifyQuizzesAddCourseTeacher.php
        ├── 2025-01-15-100001_CreateQuizSubmissionsTable.php
        └── 2025-01-15-100002_CreateQuizAnswersTable.php
```

---

## Summary

This quiz system provides:
- **Teachers:** Easy quiz creation, automatic grading, submission tracking
- **Students:** Interactive quiz taking, instant feedback, score tracking
- **System:** Secure, validated, notification-integrated, fully AJAX-driven

All features follow existing LMS patterns (assignments, materials) for consistency.

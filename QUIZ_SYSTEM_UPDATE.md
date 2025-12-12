# Quiz System Update - Multi-Question Support with Multiple Choice & Sentence Answer Types

## Overview
The quiz system has been completely refactored to support multiple questions per quiz with two question types:
1. **Multiple Choice** - Auto-graded questions with 4 options (A, B, C, D)
2. **Sentence Answer** - Written response questions requiring manual teacher grading

## Database Changes

### New Table: `quiz_questions`
Created via migration `2025-01-15-100003_CreateQuizQuestionsTable.php`

```sql
CREATE TABLE quiz_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    question TEXT NOT NULL,
    question_type ENUM('multiple_choice', 'sentence') NOT NULL,
    option_a VARCHAR(255) NULL,
    option_b VARCHAR(255) NULL,
    option_c VARCHAR(255) NULL,
    option_d VARCHAR(255) NULL,
    correct_answer TEXT NULL,
    points INT NOT NULL DEFAULT 1,
    question_order INT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    INDEX idx_quiz_id (quiz_id),
    INDEX idx_question_type (question_type),
    INDEX idx_question_order (question_order)
);
```

### Modified Table: `quiz_answers`
Modified via migration `2025-01-15-100004_ModifyQuizAnswersForQuestions.php`

- Added `question_id` column (INT) with foreign key to `quiz_questions.id`
- Links each answer to a specific question instead of the quiz header

### Modified Table: `quizzes`
- **Removed fields**: question, question_type, option_a, option_b, option_c, option_d, correct_answer, points
- **Kept fields**: Only quiz header info (title, description, course_id, teacher_id, lesson_id)

## Backend Changes

### New Model: `QuizQuestionModel.php`
**Location**: `app/Models/QuizQuestionModel.php`

**Key Methods**:
- `getQuizQuestions($quizId)` - Retrieve all questions for a quiz, ordered by question_order
- `saveQuestions($questions)` - Batch insert multiple questions
- `getTotalPoints($quizId)` - Calculate total points for a quiz
- `deleteQuizQuestions($quizId)` - Delete all questions for a quiz

**Validation Rules**:
- quiz_id: required|is_natural_no_zero
- question: required
- question_type: required|in_list[multiple_choice,sentence]
- points: permit_empty|greater_than[0]

### Updated Model: `QuizModel.php`
**Changes**:
- **allowedFields**: Reduced to [course_id, teacher_id, lesson_id, title, description]
- **getQuizWithStats()**: Added subqueries for:
  - `question_count` - COUNT(*) from quiz_questions
  - `total_points` - SUM(points) from quiz_questions

### Updated Controller: `Teacher.php`
**Import Added**: `use App\Models\QuizQuestionModel;`

#### Method: `createQuiz()`
**Complete Rewrite** - Now accepts multiple questions

**Input Format**:
```json
{
    "course_id": 1,
    "title": "Quiz Title",
    "description": "Quiz Description",
    "questions": [
        {
            "question": "What is 2+2?",
            "question_type": "multiple_choice",
            "option_a": "3",
            "option_b": "4",
            "option_c": "5",
            "option_d": "6",
            "correct_answer": "B",
            "points": 10
        },
        {
            "question": "Explain the water cycle.",
            "question_type": "sentence",
            "correct_answer": "Optional reference answer",
            "points": 5
        }
    ]
}
```

**Logic**:
1. Creates quiz header (title, description)
2. Validates each question based on type:
   - MCQ: Requires option_a, option_b, option_c, option_d, correct_answer
   - Sentence: Only requires question text (correct_answer optional for teacher reference)
3. Batch inserts all questions with quiz_id and question_order
4. Notifies enrolled students
5. Returns quiz_id

#### Method: `gradeAnswer()` (NEW)
**Route**: `POST teacher/quiz/grade-answer`

**Input**:
```json
{
    "answer_id": 123,
    "submission_id": 456,
    "points_earned": 8
}
```

**Logic**:
1. Updates quiz_answers.points_earned and is_correct
2. Recalculates total score: `SUM(points_earned) / total_points * 100`
3. Updates quiz_submissions.score
4. Notifies student with updated score

#### Method: `getQuizSubmissions()`
**Updated** to return:
- Submissions with student info
- All questions for the quiz
- All answers for each submission (grouped by submission)

### Updated Controller: `Auth.php`
**Import Added**: `use App\Models\QuizQuestionModel;`

#### Method: `getCourseQuizzes()`
**Updated** to use `getQuizWithStats()` which includes:
- question_count
- total_points
- submissions count
- submitted status per student

#### Method: `getQuiz()`
**Updated** to return:
```json
{
    "status": "success",
    "quiz": {
        "id": 1,
        "title": "Quiz Title",
        "description": "Description"
    },
    "questions": [
        {
            "id": 1,
            "question": "Question text",
            "question_type": "multiple_choice",
            "option_a": "...",
            "option_b": "...",
            "option_c": "...",
            "option_d": "...",
            "points": 10
        },
        ...
    ]
}
```

#### Method: `submitQuiz()`
**Complete Rewrite**

**Input Format**:
```json
{
    "quiz_id": 1,
    "answers": {
        "question_id_1": "B",
        "question_id_2": "Written answer text",
        "question_id_3": "A"
    }
}
```

**Logic**:
1. Creates submission record
2. Loops through each question:
   - **MCQ**: Compares answer to correct_answer, auto-grades, sets points_earned
   - **Sentence**: Sets points_earned=0, is_correct=0 (requires manual grading)
3. If ANY question is sentence type: sets submission.score = NULL
4. Otherwise: Calculates score = `SUM(points_earned) / total_points * 100`
5. Batch saves all answers with submission_id and question_id
6. Notifies teacher

### Updated Routes: `Config/Routes.php`
**Added Route**:
```php
$routes->post('teacher/quiz/grade-answer', 'Teacher::gradeAnswer', ['filter' => 'roleauth:teacher']);
```

## Frontend Changes

### Teacher Dashboard: `app/Views/teacher_dashboard.php`

#### Create Quiz Modal (Lines 1291-1330)
**Updated HTML**:
- Changed `modal-lg` to `modal-xl` for more space
- Added scrollable body (`max-height: 70vh; overflow-y: auto`)
- Removed single question fields
- Added empty `#questionsContainer` div
- Added "Add Question" button → `onclick="addQuestion()"`

#### Grade Sentence Modal (NEW - after line 1362)
**HTML**:
```html
<div class="modal fade" id="gradeSentenceModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Grade Sentence Answer</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="gradeAnswerId">
                <input type="hidden" id="gradeSubmissionId">
                <input type="hidden" id="gradeMaxPoints">
                
                <div class="mb-3">
                    <label class="form-label">Student's Answer:</label>
                    <p id="studentAnswerText" class="border p-2 rounded bg-light"></p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Points Earned:</label>
                    <input type="number" class="form-control" id="pointsEarned" min="0">
                    <small class="text-muted">Max Points: <span id="maxPointsDisplay"></span></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveGrade()">Save Grade</button>
            </div>
        </div>
    </div>
</div>
```

#### JavaScript Functions (Lines ~745-900)

##### `showCreateQuizForm(courseId)`
- Opens modal
- Resets form and questionsContainer
- Adds first question by default

##### `addQuestion()` (NEW)
Dynamically creates question card with:
- Question text textarea
- Type selector dropdown (multiple_choice | sentence)
- Points input
- Conditional fields container for MCQ options (hidden initially)
- Conditional fields container for sentence expected answer (hidden initially)
- Remove button

##### `removeQuestion(index)` (NEW)
Removes question card from DOM

##### `toggleQuestionType(index)` (NEW)
Event handler for type selector:
- If MCQ: Shows 4 option inputs + correct answer dropdown, sets required
- If Sentence: Shows expected answer textarea (optional), hides MCQ fields

##### `createQuiz()` (UPDATED)
1. Collects title, description, course_id
2. Loops through all `.question-item` elements
3. For each question:
   - Validates required fields
   - If MCQ: validates all 4 options + correct answer
   - Builds question object
4. Validates at least 1 question exists
5. POSTs JSON: `{course_id, title, description, questions: [...]}`
6. On success: closes modal, reloads quiz list

##### `renderQuizSubmissionsList(submissions, questions)` (UPDATED)
Displays submissions with expandable answers:
- Shows student name, score, submitted_at
- For each answer:
  - **MCQ**: Shows selected option, checkmark/X if correct/incorrect, points
  - **Sentence**: Shows written text, "Grade" button if ungraded, points if graded
- Grade button calls `showGradeModal()`

##### `showGradeModal(answerId, submissionId, answerText, maxPoints)` (NEW)
- Sets hidden field values
- Displays student's answer text
- Shows max points
- Opens grade modal

##### `saveGrade()` (NEW)
- Validates points <= max points
- POSTs to `/teacher/quiz/grade-answer`
- On success: closes modal, reloads submissions

### Student Dashboard: `app/Views/auth/dashboard.php`

#### JavaScript Functions (Lines ~2756-2870)

##### `takeQuiz(quizId)` (UPDATED)
- Fetches quiz + questions via GET `/student/quiz/{id}`
- Calls `displayQuiz(quiz, questions)`

##### `displayQuiz(quiz, questions)` (UPDATED)
1. Shows quiz title and description
2. Displays total questions and total points
3. For each question:
   - **Multiple Choice**:
     - Renders 4 radio buttons for options A-D
     - Name = `answer_{question_id}`
   - **Sentence Answer**:
     - Renders textarea
     - Class = `sentence-answer`
     - data-question-id attribute

##### `submitStudentQuiz()` (UPDATED)
1. Collects all answers:
   - Radio buttons: loops through checked radios
   - Textareas: loops through `.sentence-answer` elements
2. Builds answers object: `{question_id: student_answer, ...}`
3. Validates all questions answered
4. POSTs JSON: `{quiz_id, answers: {...}}`
5. Shows result:
   - If score is not null: "Score: X%"
   - If score is null: "Awaiting grading"

## Workflow

### Teacher Creates Quiz
1. Click "Quizzes" → "Create Quiz"
2. Enter title and description
3. Click "Add Question" (can add multiple)
4. For each question:
   - Select type (Multiple Choice or Sentence)
   - Enter question text
   - Set points
   - If MCQ: Fill in 4 options + select correct answer
   - If Sentence: Optionally enter expected answer (for reference)
5. Click "Create Quiz"
6. All enrolled students are notified

### Student Takes Quiz
1. Click "Take Quiz"
2. Sees all questions displayed:
   - MCQ: Radio buttons
   - Sentence: Textarea
3. Answers all questions
4. Click "Submit Quiz"
5. If all MCQ: Sees instant score
6. If any Sentence: Sees "Awaiting grading" message

### Teacher Grades Sentence Answers
1. Click "View Submissions"
2. Sees list of students with scores
3. Expands submission to see individual answers
4. For sentence answers: Clicks "Grade" button
5. Reads student's answer
6. Enters points earned (0 to max points)
7. Clicks "Save Grade"
8. Score recalculates automatically
9. Student is notified with updated score

## Validation Rules

### Multiple Choice Questions
- **Required**: question, option_a, option_b, option_c, option_d, correct_answer, points
- **correct_answer**: Must be 'A', 'B', 'C', or 'D'

### Sentence Questions
- **Required**: question, points
- **Optional**: correct_answer (for teacher reference only)

### Submission
- **Required**: All questions must be answered
- **Auto-grading**: MCQ only
- **Manual grading**: Sentence type
- **Score calculation**: If any sentence type, score = null until all graded

## Notifications

### Quiz Created
- Sent to: All enrolled students
- Title: "New Quiz: {quiz_title}"
- Message: "A new quiz has been posted in {course_name}"

### Quiz Submitted (All MCQ)
- Sent to: Teacher
- Title: "{student_name} submitted quiz"
- Message: "Score: {score}%"

### Quiz Submitted (Has Sentence)
- Sent to: Teacher
- Title: "{student_name} submitted quiz"
- Message: "Requires grading"

### Sentence Graded
- Sent to: Student
- Title: "Quiz Graded"
- Message: "Your quiz has been graded. Score: {score}%"

## Testing Checklist

### Teacher Workflow
- [ ] Create quiz with only MCQ questions
- [ ] Create quiz with only Sentence questions
- [ ] Create quiz with mixed question types
- [ ] Add/remove questions dynamically
- [ ] Verify validation for MCQ (all options required)
- [ ] Verify validation for Sentence (options not required)
- [ ] View submissions
- [ ] Grade sentence answers
- [ ] Verify score recalculation after grading

### Student Workflow
- [ ] View quiz list
- [ ] See question count and total points
- [ ] Take quiz with MCQ questions (see radio buttons)
- [ ] Take quiz with Sentence questions (see textareas)
- [ ] Take quiz with mixed types
- [ ] Submit quiz - verify instant score for all MCQ
- [ ] Submit quiz - verify "awaiting grading" for sentence types
- [ ] Receive notification when graded
- [ ] View updated score

### Edge Cases
- [ ] Quiz with 0 questions (should fail validation)
- [ ] MCQ without correct answer (should fail validation)
- [ ] Submit quiz without answering all questions (should fail)
- [ ] Grade with points > max points (should fail validation)
- [ ] Multiple students submitting same quiz
- [ ] Deleting quiz (should cascade delete questions and submissions)

## Files Modified

### Database Migrations
- ✅ `app/Database/Migrations/2025-01-15-100003_CreateQuizQuestionsTable.php` (NEW)
- ✅ `app/Database/Migrations/2025-01-15-100004_ModifyQuizAnswersForQuestions.php` (NEW)

### Models
- ✅ `app/Models/QuizQuestionModel.php` (NEW)
- ✅ `app/Models/QuizModel.php` (UPDATED)

### Controllers
- ✅ `app/Controllers/Teacher.php` (UPDATED - createQuiz, gradeAnswer, getQuizSubmissions)
- ✅ `app/Controllers/Auth.php` (UPDATED - getCourseQuizzes, getQuiz, submitQuiz)

### Routes
- ✅ `app/Config/Routes.php` (ADDED - teacher/quiz/grade-answer)

### Views
- ✅ `app/Views/teacher_dashboard.php` (UPDATED - Modal HTML + JavaScript)
- ✅ `app/Views/auth/dashboard.php` (UPDATED - JavaScript)

## Migration Status
✅ Migrations run successfully via `php spark migrate`

## Status: COMPLETE
All backend and frontend updates are complete. The system is ready for testing.

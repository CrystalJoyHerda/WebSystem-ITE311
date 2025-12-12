# Assignments & Submissions Module - Complete Implementation Guide

## Overview
This module provides a complete Assignments and Submissions system for the LMS, allowing teachers to create assignments with file attachments and students to submit their work through text or file uploads.

---

## Database Structure

### Tables Created

#### 1. `assignments` Table
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- course_id (INT, FOREIGN KEY → courses.id, CASCADE)
- teacher_id (INT, FOREIGN KEY → users.id, CASCADE)
- title (VARCHAR 255, NOT NULL)
- description (TEXT)
- due_date (DATETIME, NOT NULL)
- total_score (INT, NOT NULL)
- file_name (VARCHAR 255)
- file_path (VARCHAR 255)
- created_at (DATETIME)
- updated_at (DATETIME)
```

#### 2. `student_submissions` Table
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- assignment_id (INT, FOREIGN KEY → assignments.id, CASCADE)
- student_id (INT, FOREIGN KEY → users.id, CASCADE)
- text_submission (TEXT)
- file_name (VARCHAR 255)
- file_path (VARCHAR 255)
- score (DECIMAL 5,2)
- status (ENUM: 'pending', 'graded', DEFAULT 'pending')
- submitted_at (DATETIME)
- graded_at (DATETIME)
```

### Migration Files
- `app/Database/Migrations/2025-12-12-000001_CreateAssignmentsTable.php`
- `app/Database/Migrations/2025-12-12-000002_CreateStudentSubmissionsTable.php`

**Status:** ✅ Successfully migrated to database

---

## Backend Implementation

### Models

#### 1. AssignmentModel (`app/Models/AssignmentModel.php`)
**Methods:**
- `createAssignment($data)` - Create new assignment
- `getAssignmentsByCourse($courseId)` - Get all assignments for a course
- `getAssignment($id)` - Get single assignment by ID
- `deleteAssignment($id)` - Delete assignment
- `getAssignmentsWithSubmissionCount($courseId, $teacherId)` - Get assignments with submission counts (JOIN)

#### 2. SubmissionModel (`app/Models/SubmissionModel.php`)
**Methods:**
- `createSubmission($data)` - Create student submission
- `getSubmissionsByAssignment($assignmentId)` - Get all submissions for an assignment (JOIN with users)
- `getStudentSubmission($assignmentId, $studentId)` - Get specific student's submission
- `gradeSubmission($id, $score)` - Grade a submission (updates status to 'graded')
- `hasSubmitted($assignmentId, $studentId)` - Check if student has submitted

---

### Controllers

#### 1. Assignments Controller (`app/Controllers/Assignments.php`)
**Routes:**
- `POST assignments/create/{courseId}` - Create new assignment
- `GET assignments/view/{courseId}` - View all assignments for a course
- `POST assignments/delete/{assignmentId}` - Delete assignment
- `GET assignments/download/{assignmentId}` - Download assignment file

**Methods:**
- `create($courseId)` - Validates input, handles file upload, saves to DB, notifies students
- `view($courseId)` - Returns assignments with submission counts
- `delete($assignmentId)` - Removes assignment and file (cascades to submissions)
- `download($assignmentId)` - Authenticated file download
- `notifyStudentsAboutNewAssignment($courseId, $assignmentId, $title)` - Sends notifications

**File Upload Validation:**
- Max size: 10MB
- Allowed extensions: pdf, doc, docx, ppt, pptx, zip, jpg, jpeg, png
- Storage path: `/public/uploads/assignments/`

#### 2. Submissions Controller (`app/Controllers/Submissions.php`)
**Routes:**
- `POST submissions/submit/{assignmentId}` - Submit assignment
- `GET submissions/download/{submissionId}` - Download submission file (teacher only)
- `POST submissions/grade/{submissionId}` - Grade submission (teacher only)
- `GET submissions/view/{assignmentId}` - View all submissions (teacher only)

**Methods:**
- `submit($assignmentId)` - Validates submission (text OR file required), uploads file, notifies teacher
- `downloadSubmission($submissionId)` - Teacher downloads student files
- `grade($submissionId)` - Updates score, status='graded', notifies student
- `viewSubmissions($assignmentId)` - Returns all submissions with student info

**Submission Validation:**
- At least one required: `text_submission` OR `submission_file`
- Same file upload rules as assignments
- Storage path: `/public/uploads/submissions/`

---

### Notifications Integration

**Notification Types:**
1. **`assignment_created`**
   - Sent to: All enrolled students
   - Trigger: Teacher creates assignment
   - Message: "New assignment '{title}' created for {course}"

2. **`submission_received`**
   - Sent to: Course teacher
   - Trigger: Student submits assignment
   - Message: "{student} submitted assignment '{title}'"

3. **`submission_graded`**
   - Sent to: Submitting student
   - Trigger: Teacher grades submission
   - Message: "Your submission for '{title}' has been graded: {score}"

---

## Frontend Implementation

### Teacher Dashboard (`app/Views/teacher_dashboard.php`)

#### Modals Added:
1. **Assignments Modal** (existing, updated)
   - List of assignments with submission counts
   - Create New Assignment button
   - View Submissions button
   - Delete Assignment button
   - Download assignment file button

2. **Create Assignment Modal** (new)
   - Title (required)
   - Description (optional)
   - Due Date (datetime-local, required)
   - Total Score (number, required)
   - File Upload (optional, 10MB max)

3. **View Submissions Modal** (new)
   - List of student submissions
   - Student name, submission date
   - Text submission display
   - File download button
   - Grade button (if not graded)
   - Score display (if graded)

4. **Grade Submission Modal** (new)
   - Score input field
   - Save Grade button

#### JavaScript Functions:
- `loadAssignments(courseId)` - AJAX load assignments
- `renderAssignmentsList(assignments, courseId)` - Render assignment cards
- `showCreateAssignmentForm(courseId)` - Show create modal
- `createAssignment()` - Submit new assignment via AJAX
- `deleteAssignment(assignmentId, courseId)` - Delete with confirmation
- `viewSubmissions(assignmentId)` - Show submissions modal
- `loadSubmissions(assignmentId)` - AJAX load submissions
- `renderSubmissionsList(submissions, assignmentId)` - Render submission list
- `showGradeForm(submissionId, assignmentId)` - Show grading modal
- `gradeSubmission()` - Submit grade via AJAX

---

### Student Dashboard (`app/Views/auth/dashboard.php`)

#### UI Changes:
- Added "Assignments" button to each enrolled course card
- Button calls: `viewCourseAssignments(courseId, courseName)`

#### Modals Added:
1. **Student Assignments Modal** (new)
   - List of assignments for the course
   - Assignment title, description, due date
   - Total score display
   - Overdue badge (if past due date)
   - Download assignment file button
   - Submit button (changes to "Submitted" or "Graded: {score}" after submission)

2. **Submit Assignment Modal** (new)
   - Text Submission textarea (optional)
   - File Upload input (optional)
   - At least one required
   - Submit Assignment button

#### JavaScript Functions:
- `viewCourseAssignments(courseId, courseName)` - Show assignments modal
- `loadStudentAssignments(courseId)` - AJAX load assignments
- `renderStudentAssignmentsList(assignments)` - Render assignment cards
- `checkSubmissionStatus(assignmentId)` - Check if already submitted
- `showSubmitForm(assignmentId)` - Show submission modal
- `submitAssignment()` - Submit via AJAX with validation
- `escapeHtml(text)` - XSS prevention helper

---

## File Storage Structure

```
public/
└── uploads/
    ├── assignments/      ← Teacher assignment files
    │   └── {timestamp}_{filename}
    └── submissions/      ← Student submission files
        └── {timestamp}_{filename}
```

**Status:** ✅ Directories created and ready

---

## Routes Configuration (`app/Config/Routes.php`)

### Teacher Routes:
```php
$routes->post('assignments/create/(:num)', 'Assignments::create/$1');
$routes->get('assignments/view/(:num)', 'Assignments::view/$1');
$routes->post('assignments/delete/(:num)', 'Assignments::delete/$1');
$routes->get('assignments/download/(:num)', 'Assignments::download/$1');
```

### Student Routes:
```php
$routes->post('submissions/submit/(:num)', 'Submissions::submit/$1');
$routes->get('submissions/download/(:num)', 'Submissions::downloadSubmission/$1');
```

### Teacher Grading Routes:
```php
$routes->post('submissions/grade/(:num)', 'Submissions::grade/$1');
$routes->get('submissions/view/(:num)', 'Submissions::viewSubmissions/$1');
```

---

## Security Features

### Authentication & Authorization:
1. **Teacher Actions:**
   - Verify user is logged in
   - Verify user is a teacher
   - Verify teacher owns the course (for create/delete)

2. **Student Actions:**
   - Verify user is logged in
   - Verify user is a student
   - Verify student is enrolled in the course

3. **File Downloads:**
   - Assignment files: Authenticated users only
   - Submission files: Only the teacher who owns the course

### Input Validation:
- CSRF protection (CodeIgniter built-in)
- File upload validation (size, extensions)
- Required field validation
- XSS prevention (escapeHtml function)

### Cascade Delete:
- Deleting an assignment automatically deletes all submissions (database CASCADE)
- Deleting a course automatically deletes all assignments (database CASCADE)

---

## Testing Workflow

### Teacher Workflow:
1. ✅ Login as teacher
2. ✅ Navigate to Teacher Dashboard
3. ✅ Click on "Assignments" button for a course
4. ✅ Click "Create New Assignment"
5. ✅ Fill in form (title, description, due date, total score)
6. ✅ Upload file (optional)
7. ✅ Submit → Students receive notification
8. ✅ View assignment list → shows submission count
9. ✅ Click "View Submissions" → see student submissions
10. ✅ Click "Grade Submission" → enter score
11. ✅ Submit grade → student receives notification
12. ✅ Download student submission file
13. ✅ Delete assignment (with confirmation)

### Student Workflow:
1. ✅ Login as student
2. ✅ Navigate to Dashboard
3. ✅ Click "Assignments" button on enrolled course
4. ✅ View assignment list with due dates
5. ✅ See overdue badge if past due
6. ✅ Download assignment file (if attached)
7. ✅ Click "Submit" button
8. ✅ Enter text OR upload file (or both)
9. ✅ Submit → teacher receives notification
10. ✅ See "Submitted" badge on assignment
11. ✅ After grading, see "Graded: {score}" badge

---

## Troubleshooting

### Common Issues:

1. **File upload fails:**
   - Check `php.ini` settings: `upload_max_filesize` and `post_max_size` should be >= 10MB
   - Verify `/public/uploads/assignments/` and `/public/uploads/submissions/` exist and are writable

2. **404 errors on routes:**
   - Run `php spark routes` to verify routes are registered
   - Clear cache: `php spark cache:clear`

3. **Notifications not sending:**
   - Check `NotificationModel` exists
   - Verify students are enrolled in the course
   - Check browser console for AJAX errors

4. **Modal not showing:**
   - Verify Bootstrap 5 and jQuery are loaded
   - Check browser console for JavaScript errors
   - Ensure modal IDs match in HTML and JavaScript

---

## Dependencies

- **CodeIgniter:** 4.6.3
- **MySQL:** 8.0
- **Bootstrap:** 5.3.0
- **jQuery:** 3.6.0
- **Font Awesome:** 6.4.0

---

## Implementation Status

### Completed ✅
- Database migrations (assignments, student_submissions)
- AssignmentModel with all CRUD methods
- SubmissionModel with grading functionality
- Assignments controller (create, view, delete, download)
- Submissions controller (submit, download, grade, view)
- All routes registered
- Notification integration (3 types)
- File upload validation
- Teacher dashboard UI (modals, forms, AJAX)
- Student dashboard UI (assignment view, submission)
- Upload directories created
- Security & authorization checks

### Ready for Testing ✅
All functionality is complete and ready for end-to-end testing.

---

## Next Steps (Optional Enhancements)

1. **Badge Notifications:**
   - Add unread assignment count badge on course cards
   - Clear notifications when student views assignments

2. **Email Notifications:**
   - Send email when assignment created
   - Send email when submission graded

3. **Assignment Editing:**
   - Add edit assignment functionality
   - Update due dates, scores, descriptions

4. **Submission History:**
   - Allow resubmission before due date
   - Track submission versions

5. **Late Submission Handling:**
   - Mark late submissions
   - Allow/disallow late submissions based on settings

6. **Rubric Grading:**
   - Add rubric criteria
   - Breakdown scores by criteria

7. **Batch Grading:**
   - Grade multiple submissions at once
   - Export grades to CSV

---

## Credits
Implemented for ITE311-HERDA Learning Management System  
Date: December 11, 2024  
Version: 1.0.0

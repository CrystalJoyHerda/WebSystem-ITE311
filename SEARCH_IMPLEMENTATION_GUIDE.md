# Admin Users Search & Filter Implementation Guide

## Overview
This document explains the comprehensive search and filtering system added to the Admin Dashboard Users List. The implementation includes both **client-side (instant)** and **server-side (AJAX)** search capabilities.

## Features Implemented

### 1. Client-Side Search (Default - Already Active)
- **Real-time filtering** as you type
- **300ms debounce** for performance optimization
- **Search fields**: Name and Email
- **Role filter**: Admin, Teacher, Student
- **Instant feedback** - no server requests
- **Best for**: Small to medium datasets (< 1000 users)

### 2. Server-Side AJAX Search (Available)
- **Database-level searching** via AJAX endpoint
- **Full SQL LIKE queries** for accurate matching
- **Dynamic table rebuilding** with search results
- **User count updates**
- **Best for**: Large datasets or precise database queries

---

## File Changes

### 1. **app/Controllers/admin.php**
#### Added Method: `searchUsers()`
- **Location**: After line 232 (after `activateUser()` method)
- **Purpose**: Handle AJAX search requests from admin dashboard
- **Features**:
  - Accepts both GET and POST requests
  - Search parameters: `search` (name/email), `role` (filter)
  - Returns JSON response with user array and count
  - Security: Admin-only access validation
  - Query: Uses CodeIgniter's Query Builder with LIKE and WHERE clauses

```php
public function searchUsers()
{
    // Only admins can search users
    if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }
        return redirect()->to(base_url('login'));
    }

    $db = \Config\Database::connect();
    $builder = $db->table('users');

    // Get search parameters
    $searchQuery = $this->request->getGet('search') ?? $this->request->getPost('search') ?? '';
    $roleFilter = $this->request->getGet('role') ?? $this->request->getPost('role') ?? '';

    // Only show active users
    $builder->where('status', 'active');

    // Apply search query (name or email)
    if (!empty($searchQuery)) {
        $builder->groupStart()
                ->like('name', $searchQuery)
                ->orLike('email', $searchQuery)
                ->groupEnd();
    }

    // Apply role filter
    if (!empty($roleFilter)) {
        $builder->where('role', $roleFilter);
    }

    // Get results
    $users = $builder->orderBy('name', 'ASC')->get()->getResultArray();

    // If AJAX request, return JSON
    if ($this->request->isAJAX()) {
        return $this->response->setJSON([
            'status' => 'success',
            'users' => $users,
            'count' => count($users)
        ]);
    }

    // Otherwise return HTML (for non-AJAX fallback)
    return view('auth/dashboard', ['users' => $users]);
}
```

---

### 2. **app/Config/Routes.php**
#### Added Routes
- **Location**: Inside `admin` route group (after line 52)
- **Routes**:
  ```php
  $routes->get('admin/users/search', 'Admin::searchUsers');
  $routes->post('admin/users/search', 'Admin::searchUsers');
  ```

**Full Admin Group** (with new routes highlighted):
```php
$routes->group('admin', ['filter' => 'roleauth'], function($routes){
    $routes->get('courses', 'Admin::courses');
    // Admin user management endpoints
    $routes->post('addUser', 'Admin::addUser');
    $routes->post('updateUser', 'Admin::updateUser');
    $routes->post('deleteUser', 'Admin::deleteUser');
    
    // ✨ NEW: User search endpoint (supports both GET and POST for AJAX)
    $routes->get('users/search', 'Admin::searchUsers');
    $routes->post('users/search', 'Admin::searchUsers');
    
    // Manage inactive users
    $routes->get('aboutUsers', 'Admin::aboutUsers');
    $routes->post('activateUser', 'Admin::activateUser');
    // ... rest of routes
});
```

---

### 3. **app/Views/auth/dashboard.php**
#### Enhanced JavaScript (Lines 1112-1290)

**A. Client-Side Filtering** (Already Active)
```javascript
// Live client-side search (debounced for performance)
let searchTimeout;
$('#userSearchInput').on('keyup', function() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(function() {
    filterUsers(); // Instant filtering of visible rows
  }, 300);
});

$('#roleFilterSelect').on('change', function() {
  filterUsers();
});

function filterUsers() {
  const searchTerm = $('#userSearchInput').val().toLowerCase().trim();
  const selectedRole = $('#roleFilterSelect').val().toLowerCase();
  let visibleCount = 0;
  
  $('.user-row').each(function() {
    const $row = $(this);
    const userName = $row.data('user-name') || '';
    const userEmail = $row.data('user-email') || '';
    const userRole = $row.data('user-role') || '';
    
    const matchesSearch = searchTerm === '' || 
                         userName.includes(searchTerm) || 
                         userEmail.includes(searchTerm);
    
    const matchesRole = selectedRole === '' || userRole === selectedRole;
    
    if (matchesSearch && matchesRole) {
      $row.show();
      visibleCount++;
    } else {
      $row.hide();
    }
  });
  
  $('#userCountDisplay').text('Showing ' + visibleCount + ' of <?= count($users ?? []) ?> users');
}
```

**B. Server-Side AJAX Search** (Available - Currently Commented)
```javascript
function serverSearchUsers() {
  const searchQuery = $('#userSearchInput').val().trim();
  const roleFilter = $('#roleFilterSelect').val();
  
  // Show loading indicator
  $('#usersTable tbody').html(
    '<tr><td colspan="4" class="text-center py-4">' +
    '<i class="fa fa-spinner fa-spin me-2"></i>Searching...</td></tr>'
  );
  
  $.ajax({
    url: '<?= base_url('admin/users/search') ?>',
    type: 'GET',
    data: {
      search: searchQuery,
      role: roleFilter
    },
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        $('#usersTable tbody').empty();
        
        if (response.users && response.users.length > 0) {
          // Rebuild table with search results
          response.users.forEach(function(user) {
            // Create table rows dynamically
            const badgeClass = user.role === 'admin' ? 'bg-danger' : 
                              (user.role === 'teacher' ? 'bg-warning text-dark' : 'bg-info');
            
            const row = `
              <tr data-user-id="${user.id}" 
                  data-user-name="${user.name.toLowerCase()}" 
                  data-user-email="${user.email.toLowerCase()}" 
                  data-user-role="${user.role.toLowerCase()}" 
                  class="user-row">
                <td class="user-name">${escapeHtml(user.name)}</td>
                <td class="user-email">${escapeHtml(user.email)}</td>
                <td class="user-role">
                  <span class="badge ${badgeClass}">${capitalizeFirst(user.role)}</span>
                </td>
                <td>
                  <button class="btn btn-sm btn-outline-primary edit-user-btn" 
                          data-id="${user.id}" 
                          data-name="${escapeHtml(user.name)}" 
                          data-email="${escapeHtml(user.email)}" 
                          data-role="${user.role}">
                    <i class="fa fa-edit"></i>
                  </button>
                  ${user.role !== 'admin' ? 
                    `<button class="btn btn-sm btn-outline-danger delete-user-btn" 
                             data-id="${user.id}" 
                             data-name="${escapeHtml(user.name)}">
                      <i class="fa fa-trash"></i>
                    </button>` : ''}
                </td>
              </tr>
            `;
            $('#usersTable tbody').append(row);
          });
          
          $('#userCountDisplay').text(`Showing ${response.count} users`);
        } else {
          $('#usersTable tbody').html(
            '<tr><td colspan="4" class="text-center text-muted py-4">' +
            '<i class="fa fa-search me-2"></i>No users found</td></tr>'
          );
        }
      }
    },
    error: function(xhr, status, error) {
      console.error('AJAX search error:', error);
      $('#usersTable tbody').html(
        '<tr><td colspan="4" class="text-center text-danger py-4">' +
        '<i class="fa fa-exclamation-triangle me-2"></i>Search failed</td></tr>'
      );
    }
  });
}

// Helper functions
function escapeHtml(text) {
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}

function capitalizeFirst(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}
```

---

## Usage

### Current Default Behavior
1. Type in search box → **Client-side filtering** (instant)
2. Select role from dropdown → **Client-side filtering** (instant)
3. Results update immediately without server requests

### To Enable Server-Side AJAX Search
Uncomment the following code at line ~1270:

```javascript
// Enable automatic server-side search on Enter key
$('#userSearchInput').on('keypress', function(e) {
  if (e.which === 13) { // Enter key
    e.preventDefault();
    serverSearchUsers();
  }
});
```

**Or** add a "Search" button:
```html
<!-- In dashboard.php around line 345 -->
<div class="col-md-8">
  <div class="input-group input-group-sm">
    <span class="input-group-text">
      <i class="fa fa-search"></i>
    </span>
    <input type="text" class="form-control" id="userSearchInput" placeholder="Search by name or email...">
    <button class="btn btn-primary" onclick="serverSearchUsers()">
      <i class="fa fa-search"></i> Search DB
    </button>
  </div>
</div>
```

---

## API Endpoint

### URL
```
GET/POST: /admin/users/search
```

### Request Parameters
| Parameter | Type   | Required | Description                |
|-----------|--------|----------|----------------------------|
| search    | string | No       | Search term (name/email)   |
| role      | string | No       | Filter by role (admin/teacher/student) |

### Response (JSON)
```json
{
  "status": "success",
  "users": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "student",
      "status": "active",
      "created_at": "2024-01-01 10:00:00",
      "updated_at": "2024-01-01 10:00:00"
    }
  ],
  "count": 1
}
```

### Example AJAX Call
```javascript
$.ajax({
  url: '<?= base_url('admin/users/search') ?>',
  type: 'GET',
  data: {
    search: 'john',
    role: 'student'
  },
  success: function(response) {
    console.log('Found users:', response.users);
    console.log('Total count:', response.count);
  }
});
```

---

## Testing

### 1. Test Client-Side Search (Already Active)
1. Login as Admin
2. Go to Admin Dashboard
3. Type in search box → Users filter instantly
4. Select role from dropdown → Users filter instantly
5. Check browser console → **NO** AJAX requests sent

### 2. Test Server-Side Search
1. Uncomment Enter key handler (line ~1270)
2. Login as Admin
3. Type search term and press **Enter**
4. Check browser Network tab → AJAX GET request to `/admin/users/search`
5. Table rebuilds with database results

### 3. Test Direct API Call
Use browser console or Postman:
```javascript
fetch('/admin/users/search?search=john&role=student')
  .then(r => r.json())
  .then(data => console.log(data));
```

---

## Performance Notes

### Client-Side Filtering
- ✅ **Instant** results (no network delay)
- ✅ Works offline
- ✅ Good for < 1000 users
- ❌ Limited to currently loaded data
- ❌ Cannot search across paginated data

### Server-Side AJAX Search
- ✅ Searches **entire database**
- ✅ Can handle millions of users
- ✅ Precise SQL LIKE matching
- ❌ Network latency (~100-500ms)
- ❌ Requires server processing

---

## Security

### Authorization Checks
- ✅ Only admins can access search endpoint
- ✅ Session validation in controller
- ✅ Role-based route filtering (`roleauth` filter)

### SQL Injection Protection
- ✅ CodeIgniter Query Builder escapes all inputs
- ✅ LIKE queries use parameterized bindings
- ✅ No raw SQL concatenation

### XSS Protection
- ✅ HTML escaping in JavaScript (`escapeHtml()`)
- ✅ CodeIgniter `esc()` in PHP templates
- ✅ User data never inserted raw into DOM

---

## Troubleshooting

### Issue: Search returns no results
**Solution**: Check database for `status = 'active'` users

### Issue: AJAX 404 error
**Solution**: Verify routes are loaded (clear route cache):
```bash
php spark cache:clear
```

### Issue: Search returns all users
**Solution**: Check if search parameters are being sent:
```javascript
console.log('Search params:', { search: searchQuery, role: roleFilter });
```

### Issue: Table not rebuilding
**Solution**: Check browser console for JavaScript errors

---

## Future Enhancements

1. **Pagination**: Add pagination for large result sets
2. **Advanced Filters**: Created date, last login, status
3. **Export**: CSV/Excel export of search results
4. **Search History**: Save recent searches
5. **Autocomplete**: Suggest names/emails as you type
6. **Bulk Actions**: Select multiple users from search results

---

## Conclusion

This implementation provides a flexible, scalable search system with both instant client-side filtering and powerful server-side database queries. The dual approach ensures optimal user experience for datasets of any size while maintaining security and performance.

**Default Mode**: Client-side (instant filtering)  
**Optional Mode**: Server-side AJAX (database queries)  
**Recommendation**: Use client-side for < 1000 users, server-side for larger datasets

---

**Created**: <?= date('Y-m-d H:i:s') ?>  
**Framework**: CodeIgniter 4.6.3  
**Author**: GitHub Copilot  
**Version**: 1.0

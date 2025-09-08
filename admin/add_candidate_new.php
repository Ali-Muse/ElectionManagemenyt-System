<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_role('admin');

// Fetch elections for dropdown
$elections = [];
$sql = "SELECT id, title FROM elections ORDER BY start_date DESC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $elections[] = $row;
    }
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username    = trim($_POST['username']);
    $password    = trim($_POST['password']);
    $full_name   = trim($_POST['full_name']);
    $email       = trim($_POST['email']);
    $party       = trim($_POST['party']);
    $election_id = intval($_POST['election_id']); // from dropdown
    $manifesto   = trim($_POST['manifesto'] ?? '');
  
    $image_name  = null;

    // Validate required fields
    if (empty($username) || empty($password) || empty($full_name) || empty($email)) {
        $error = "Username, password, full name, and email are required fields.";
    } else {
        // Check if username already exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "Username already exists. Please choose a different username.";
        } else {
            // Handle image upload if provided
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/';
                
                // Create directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Generate unique filename
                $original_name = basename($_FILES['profile_image']['name']);
                $extension = pathinfo($original_name, PATHINFO_EXTENSION);
                $image_name = 'candidate_' . uniqid() . '.' . strtolower($extension);
                $target_path = $upload_dir . $image_name;
                
                // Move uploaded file
                if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_path)) {
                    $error = "Failed to upload image. Please try again.";
                }
            }
            
            if (empty($error)) {
                // Begin transaction
                $conn->begin_transaction();
                
                try {
                    // Insert into users table with plain text password
                    $user_stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'candidate')");
                    $user_stmt->bind_param("ss", $username, $password);
                    $user_stmt->execute();
                    
                    // Get the new user ID
                    $user_id = $conn->insert_id;
                    
                    // Insert into candidates table
                    $stmt = $conn->prepare("INSERT INTO candidates (user_id, full_name, email, party, manifesto, profile_image, election_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("isssssi", $user_id, $full_name, $email, $party, $manifesto, $image_name, $election_id);
                    $stmt->execute();
                    
                    // Commit transaction
                    $conn->commit();
                    
                    $success = "Candidate registered successfully! They can now login with username: $username";
                    
                    // Clear form data after successful submission
                    $username = $password = $full_name = $email = $party = $manifesto = '';
                    $election_id = 0;
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $conn->rollback();
                    $error = "Error registering candidate: " . $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Candidate | EMS Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body class="bg-gray-100 font-sans">

<div class="flex min-h-screen">
  <!-- Sidebar -->
  <aside class="w-64 bg-gray-800 text-white flex flex-col">
    <div class="p-6 border-b border-gray-700 flex items-center space-x-3">
      <div class="rounded-full bg-blue-600 w-10 h-10 flex items-center justify-center">
        <i class="fas fa-user-shield text-xl"></i>
      </div>
      <div>
        <div class="text-xl font-bold">EMS Admin</div>
        <div class="text-xs text-gray-400"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Administrator'); ?></div>
      </div>
    </div>
    
    <nav class="flex-1 p-4 space-y-1">
      <a href="dashboard.php" class="flex items-center py-2 px-4 rounded hover:bg-gray-700 text-gray-300 hover:text-white">
        <i class="fas fa-tachometer-alt w-6"></i>
        <span>Dashboard</span>
      </a>
      <a href="elections.php" class="flex items-center py-2 px-4 rounded hover:bg-gray-700 text-gray-300 hover:text-white">
        <i class="fas fa-vote-yea w-6"></i>
        <span>Elections</span>
      </a>
      <a href="candidates.php" class="flex items-center py-2 px-4 rounded bg-gray-700 text-white">
        <i class="fas fa-user-tie w-6"></i>
        <span>Candidates</span>
      </a>
      <a href="voters.php" class="flex items-center py-2 px-4 rounded hover:bg-gray-700 text-gray-300 hover:text-white">
        <i class="fas fa-users w-6"></i>
        <span>Voters</span>
      </a>
      <a href="results.php" class="flex items-center py-2 px-4 rounded hover:bg-gray-700 text-gray-300 hover:text-white">
        <i class="fas fa-chart-bar w-6"></i>
        <span>Results</span>
      </a>
      <a href="messages.php" class="flex items-center py-2 px-4 rounded hover:bg-gray-700 text-gray-300 hover:text-white">
        <i class="fas fa-envelope w-6"></i>
        <span>Messages</span>
      </a>
      <a href="settings.php" class="flex items-center py-2 px-4 rounded hover:bg-gray-700 text-gray-300 hover:text-white">
        <i class="fas fa-cog w-6"></i>
        <span>Settings</span>
      </a>
      
      <div class="pt-4 mt-6 border-t border-gray-700">
        <a href="logout.php" class="flex items-center py-2 px-4 rounded bg-red-600 hover:bg-red-700 text-white">
          <i class="fas fa-sign-out-alt w-6"></i>
          <span>Logout</span>
        </a>
      </div>
    </nav>
    
    <div class="p-4 text-xs text-gray-400">
      <p>Election Management System</p>
      <p>© 2023 All Rights Reserved</p>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-8 overflow-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-2xl font-bold text-gray-800">Add New Candidate</h1>
      <a href="candidates.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded shadow flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Candidates
      </a>
    </div>

    <?php if ($success): ?>
      <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
        <div class="flex items-center">
          <i class="fas fa-check-circle mr-2"></i>
          <p><?= htmlspecialchars($success) ?></p>
        </div>
      </div>
    <?php elseif ($error): ?>
      <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <div class="flex items-center">
          <i class="fas fa-exclamation-circle mr-2"></i>
          <p><?= htmlspecialchars($error) ?></p>
        </div>
      </div>
    <?php endif; ?>

    <!-- Add Candidate Form -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="bg-blue-50 px-6 py-4 border-b border-blue-100">
        <h2 class="text-lg font-semibold text-blue-800">Candidate Registration Form</h2>
      </div>
      
      <form action="add_candidate_new.php" method="POST" enctype="multipart/form-data" class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Account Information -->
          <div class="col-span-2 border-b pb-4 mb-4">
            <h3 class="text-md font-semibold mb-3 flex items-center text-gray-700">
              <i class="fas fa-user-lock mr-2 text-blue-500"></i> Account Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user text-gray-400"></i>
                  </div>
                  <input type="text" id="username" name="username" required 
                         class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
              </div>
              
              <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                  </div>
                  <input type="password" id="password" name="password" required 
                         class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
              </div>
            </div>
          </div>
          
          <!-- Personal Information -->
          <div class="col-span-2 border-b pb-4 mb-4">
            <h3 class="text-md font-semibold mb-3 flex items-center text-gray-700">
              <i class="fas fa-address-card mr-2 text-green-500"></i> Personal Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                <input type="text" id="full_name" name="full_name" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
              </div>
              
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400"></i>
                  </div>
                  <input type="email" id="email" name="email" required 
                         class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
              </div>
            </div>
          </div>
          
          <!-- Election Information -->
          <div class="col-span-2 border-b pb-4 mb-4">
            <h3 class="text-md font-semibold mb-3 flex items-center text-gray-700">
              <i class="fas fa-vote-yea mr-2 text-purple-500"></i> Election Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="party" class="block text-sm font-medium text-gray-700 mb-1">Party</label>
                <input type="text" id="party" name="party" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
              </div>
              
              <div>
                <label for="election_id" class="block text-sm font-medium text-gray-700 mb-1">Election <span class="text-red-500">*</span></label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-poll text-gray-400"></i>
                  </div>
                  <select id="election_id" name="election_id" required 
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
                    <option value="">-- Select Election --</option>
                    <?php foreach ($elections as $election): ?>
                      <option value="<?= $election['id'] ?>"><?= htmlspecialchars($election['title']) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400"></i>
                  </div>
                </div>
              </div>
              
              <div class="md:col-span-2">
                <label for="manifesto" class="block text-sm font-medium text-gray-700 mb-1">Manifesto</label>
                <textarea id="manifesto" name="manifesto" rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Enter candidate's manifesto or campaign promises..."></textarea>
              </div>
            </div>
          </div>
          
          <!-- Profile Image -->
          <div class="col-span-2">
            <h3 class="text-md font-semibold mb-3 flex items-center text-gray-700">
              <i class="fas fa-image mr-2 text-yellow-500"></i> Profile Image
            </h3>
            
            <div class="flex items-center space-x-6">
              <div class="shrink-0">
                <img class="h-16 w-16 object-cover rounded-full" src="https://via.placeholder.com/150?text=Preview" alt="Profile preview" id="preview-image" />
              </div>
              <label class="block">
                <span class="sr-only">Choose profile photo</span>
                <input type="file" name="profile_image" accept="image/*" 
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                       onchange="document.getElementById('preview-image').src = window.URL.createObjectURL(this.files[0])" />
                <p class="mt-1 text-sm text-gray-500">PNG, JPG, or JPEG up to 5MB</p>
              </label>
            </div>
          </div>
        </div>
        
        <div class="mt-8 flex justify-end space-x-3">
          <a href="candidates.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md">
            Cancel
          </a>
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md flex items-center">
            <i class="fas fa-user-plus mr-2"></i> Register Candidate
          </button>
        </div>
      </form>
    </div>
  </main>
</div>

</body>
</html>
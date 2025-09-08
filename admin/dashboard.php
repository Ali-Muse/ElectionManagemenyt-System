<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/image_utils.php';
require_role('admin');

// Get statistics
$total_elections = $conn->query("SELECT COUNT(*) FROM elections")->fetch_row()[0];
$active_elections = $conn->query("SELECT COUNT(*) FROM elections WHERE status = 'active'")->fetch_row()[0];
$upcoming_elections = $conn->query("SELECT COUNT(*) FROM elections WHERE status = 'upcoming'")->fetch_row()[0];
$ended_elections = $conn->query("SELECT COUNT(*) FROM elections WHERE status = 'ended'")->fetch_row()[0];

$total_candidates = $conn->query("SELECT COUNT(*) FROM candidates")->fetch_row()[0];
$total_voters = $conn->query("SELECT COUNT(*) FROM voters")->fetch_row()[0];
$total_votes = $conn->query("SELECT COUNT(*) FROM votes")->fetch_row()[0];

// Get recent elections
$recent_elections = $conn->query("SELECT id, title, status, start_date, end_date FROM elections ORDER BY start_date DESC LIMIT 5");

// Get recent candidates
$recent_candidates = $conn->query("SELECT c.id, c.full_name, c.email, c.profile_image, e.title as election_title 
                                  FROM candidates c 
                                  LEFT JOIN elections e ON c.election_id = e.id 
                                  ORDER BY c.id DESC LIMIT 5");

// Get admin info
$admin_id = $_SESSION['user_id'];
$admin_info = $conn->query("SELECT username FROM users WHERE id = $admin_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard | EMS</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body class="bg-gray-100 text-gray-800">

  <!-- Layout: Sidebar + Main Content -->
  <div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-purple-800 text-xl text-white flex flex-col">
      <div class="p-6 border-b border-purple-700 flex items-center space-x-3">
        <div class="rounded-full bg-purple-600 w-10 h-10 flex items-center justify-center">
          <i class="fas fa-user-shield text-xl"></i>
        </div>
        <div>
          <div class="text-xl font-bold">EMS Admin</div>
          <div class="text-xs text-purple-300"><?php echo htmlspecialchars($admin_info['username']); ?></div>
        </div>
      </div>
      
       <nav class="flex-1 p-4 space-y-1">
        <a href="dashboard.php" class="flex items-center py-2 px-4 rounded hover:bg-purple-700 text-purple-200 hover:text-white">
          <i class="fas fa-tachometer-alt w-6"></i>
          <span class="px-4">Dashboard</span>
        </a>
        <a href="elections.php" class="flex items-center py-2 px-4 rounded bg-purple-700 text-white">
          <i class="fas fa-vote-yea w-6"></i>
          <span class="px-4">Elections</span>
        </a>
        <a href="candidates.php" class="flex items-center py-2 px-4 rounded hover:bg-purple-700 text-purple-200 hover:text-white">
          <i class="fas fa-user-tie w-6"></i>
          <span class="px-4">Candidates</span>
        </a>
        <a href="voters.php" class="flex items-center py-2 px-4 rounded hover:bg-purple-700 text-purple-200 hover:text-white">
          <i class="fas fa-users w-6"></i>
          <span class="px-4">Voters</span>
        </a>
        <a href="results.php" class="flex items-center py-2 px-4 rounded hover:bg-purple-700 text-purple-200 hover:text-white">
          <i class="fas fa-chart-bar w-6"></i>
          <span class="px-4">Results</span>
        </a>
        <a href="messages.php" class="flex items-center py-2 px-4 rounded hover:bg-purple-700 text-purple-200 hover:text-white">
          <i class="fas fa-envelope w-6"></i>
          <span class="px-4">Messages</span>
        </a>
        <a href="settings.php" class="flex items-center py-2 px-4 rounded hover:bg-purple-700 text-purple-200 hover:text-white">
          <i class="fas fa-cog w-6"></i>
          <span class="px-4">Settings</span>
        </a>
        
        <div class="pt-4 mt-6 border-t border-purple-700">
          <a href="logout.php" class="flex items-center py-2 px-4 rounded bg-red-600 hover:bg-red-700 text-white">
            <i class="fas fa-sign-out-alt w-6"></i>
            <span class="px-4">Logout</span>
          </a>
        </div>
      </nav>
      
      <div class="p-4 text-xs text-purple-300">
        <p>Election Management System</p>
        <p>© 2023 All Rights Reserved</p>
      </div>
    </aside>

    <!-- Main Dashboard Content -->
    <main class="flex-1 p-8 overflow-auto">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
        <div class="text-sm text-gray-600">
          <?php echo date('l, F j, Y'); ?>
        </div>
      </div>
      
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gray-200 rounded-lg shadow p-6 border-l-4 border-purple-500">
          <div class="flex items-center">
            <div class="rounded-full bg-purple-100 p-3 mr-4">
              <i class="fas fa-vote-yea text-purple-500 text-xl"></i>
            </div>
            <div>
              <div class="text-lg font-medium text-gray-500">Total Elections</div>
              <div class="text-2xl font-bold text-gray-800"><?php echo $total_elections; ?></div>
            </div>
          </div>
          <div class="mt-4 flex justify-between text-sm">
            <span class="text-green-500"><i class="fas fa-circle"></i> Active: <?php echo $active_elections; ?></span>
            <span class="text-yellow-500"><i class="fas fa-circle"></i> Upcoming: <?php echo $upcoming_elections; ?></span>
            <span class="text-gray-500"><i class="fas fa-circle"></i> Ended: <?php echo $ended_elections; ?></span>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
          <div class="flex items-center">
            <div class="rounded-full bg-green-100 p-3 mr-4">
              <i class="fas fa-user-tie text-green-500 text-xl"></i>
            </div>
            <div>
              <div class="text-lg font-medium text-gray-500">Candidates</div>
              <div class="text-2xl font-bold text-gray-800"><?php echo $total_candidates; ?></div>
            </div>
          </div>
          <div class="mt-4">
            <a href="candidates.php" class="text-green-600 hover:text-green-800 text-lg font-medium">
              View all candidates <i class="fas fa-arrow-right ml-1"></i>
            </a>
          </div>
        </div>
        
        <div class="bg-gray-200 rounded-lg shadow p-6 border-l-4 border-purple-500">
          <div class="flex items-center">
            <div class="rounded-full bg-purple-100 p-3 mr-4">
              <i class="fas fa-users text-purple-500 text-xl"></i>
            </div>
            <div>
              <div class="text-lg font-medium text-gray-500">Registered Voters</div>
              <div class="text-2xl font-bold text-gray-800"><?php echo $total_voters; ?></div>
            </div>
          </div>
          <div class="mt-4">
            <a href="voters.php" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
              View all voters <i class="fas fa-arrow-right ml-1"></i>
            </a>
          </div>
        </div>
        
        <div class="bg-purple-100 rounded-lg shadow p-6 border-l-4 border-yellow-500">
          <div class="flex items-center">
            <div class="rounded-full bg-yellow-100 p-3 mr-4">
              <i class="fas fa-check-square text-yellow-500 text-xl"></i>
            </div>
            <div>
              <div class="text-lg font-medium text-gray-500">Total Votes Cast</div>
              <div class="text-2xl font-bold text-gray-800"><?php echo $total_votes; ?></div>
            </div>
          </div>
          <div class="mt-4">
            <a href="results.php" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">
              View results <i class="fas fa-arrow-right ml-1"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Elections -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="bg-purple-50 px-6 py-4 border-b border-purple-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-purple-800"><i class="fas fa-vote-yea mr-2"></i>Recent Elections</h2>
            <a href="elections.php" class="text-purple-600 hover:text-purple-800 text-sm flex items-center">
              <i class="fas fa-external-link-alt mr-1"></i> View All
            </a>
          </div>
          <div class="p-6">
            <?php if ($recent_elections && $recent_elections->num_rows > 0): ?>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead>
                    <tr>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <i class="fas fa-heading text-blue-500 mr-1"></i> Title
                      </th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <i class="fas fa-info-circle text-green-500 mr-1"></i> Status
                      </th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <i class="fas fa-calendar-alt text-purple-500 mr-1"></i> Dates
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200">
                    <?php while ($election = $recent_elections->fetch_assoc()): ?>
                      <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm"><?php echo htmlspecialchars($election['title']); ?></td>
                        <td class="px-4 py-3">
                          <?php if ($election['status'] === 'active'): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                          <?php elseif ($election['status'] === 'upcoming'): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Upcoming</span>
                          <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Ended</span>
                          <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm">
                          <?php echo date('M j, Y', strtotime($election['start_date'])); ?> - 
                          <?php echo date('M j, Y', strtotime($election['end_date'])); ?>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-gray-500 text-center py-4">No elections found.</p>
            <?php endif; ?>
          </div>
        </div>
        
        <!-- Recent Candidates -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="bg-purple-50 px-6 py-4 border-b border-purple-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-purple-800"><i class="fas fa-user-tie mr-2"></i>Recent Candidates</h2>
            <a href="candidates.php" class="text-purple-600 hover:text-purple-800 text-sm flex items-center">
              <i class="fas fa-external-link-alt mr-1"></i> View All
            </a>
          </div>
          <div class="p-6">
            <?php if ($recent_candidates && $recent_candidates->num_rows > 0): ?>
              <div class="space-y-4">
                <?php while ($candidate = $recent_candidates->fetch_assoc()): ?>
                  <div class="flex items-center p-3 hover:bg-gray-50 rounded">
                    <?= getCandidateImageHtml(
                      $candidate['profile_image'], 
                      $candidate['full_name'], 
                      'w-10 h-10 rounded-full object-cover mr-4'
                    ) ?>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate"><?php echo htmlspecialchars($candidate['full_name']); ?></p>
                      <p class="text-xs text-gray-500 truncate"><?php echo htmlspecialchars($candidate['email']); ?></p>
                    </div>
                    <div class="text-xs text-right">
                      <p class="text-gray-500">Election:</p>
                      <p class="font-medium"><?php echo htmlspecialchars($candidate['election_title'] ?? 'N/A'); ?></p>
                    </div>
                  </div>
                <?php endwhile; ?>
              </div>
            <?php else: ?>
              <p class="text-gray-500 text-center py-4">No candidates found.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
      <!-- Quick Actions -->
      <div class="mt-8 bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
          <h2 class="text-lg font-semibold text-gray-800"><i class="fas fa-bolt mr-2"></i>Quick Actions</h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <a href="elections.php" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100">
            <div class="rounded-full bg-blue-100 p-3 mr-4">
              <i class="fas fa-plus text-blue-500"></i>
            </div>
            <div>
              <p class="font-medium text-blue-800">Add Election</p>
              <p class="text-xs text-blue-600">Create a new election</p>
            </div>
          </a>
          
          <a href="add_candidate.php" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100">
            <div class="rounded-full bg-green-100 p-3 mr-4">
              <i class="fas fa-user-plus text-green-500"></i>
            </div>
            <div>
              <p class="font-medium text-green-800">Add Candidate</p>
              <p class="text-xs text-green-600">Register a new candidate</p>
            </div>
          </a>
          
          <a href="add_voter.php" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100">
            <div class="rounded-full bg-purple-100 p-3 mr-4">
              <i class="fas fa-user-plus text-purple-500"></i>
            </div>
            <div>
              <p class="font-medium text-purple-800">Add Voter</p>
              <p class="text-xs text-purple-600">Register a new voter</p>
            </div>
          </a>
          
          <a href="results.php" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100">
            <div class="rounded-full bg-yellow-100 p-3 mr-4">
              <i class="fas fa-chart-pie text-yellow-500"></i>
            </div>
            <div>
              <p class="font-medium text-yellow-800">View Results</p>
              <p class="text-xs text-yellow-600">Check election results</p>
            </div>
          </a>
        </div>
      </div>
    </main>
  </div>
</body>
</html>

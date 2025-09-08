<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/image_utils.php';
require_role('candidate');
$user_id = $_SESSION['user_id'];

// Get candidate information
$candidate_query = "SELECT id, full_name, profile_image, election_id FROM candidates WHERE user_id = $user_id";
$candidate_result = $conn->query($candidate_query);
$candidate = $candidate_result->fetch_assoc();
$candidate_id = $candidate['id'];
$election_id = $candidate['election_id'];

// Fetch statistics
$total_elections = $conn->query("SELECT COUNT(*) FROM elections")->fetch_row()[0];
$active_elections = $conn->query("SELECT COUNT(*) FROM elections WHERE status = 'active'")->fetch_row()[0];
$registered_candidates = $conn->query("SELECT COUNT(*) FROM candidates")->fetch_row()[0];

// Get election information
$election_query = "SELECT title FROM elections WHERE id = $election_id";
$election_result = $conn->query($election_query);
$election = $election_result->fetch_assoc();

// Get all candidates for this election with vote counts
$candidates_query = "
    SELECT 
        c.id,
        c.full_name,
        c.party,
        c.profile_image,
        (SELECT COUNT(*) FROM votes WHERE candidate_id = c.id) AS vote_count
    FROM candidates c
    WHERE c.election_id = $election_id
    ORDER BY vote_count DESC, c.full_name ASC
";
$candidates_result = $conn->query($candidates_query);

// Get total votes for this election
$total_votes_query = "SELECT COUNT(*) FROM votes WHERE election_id = $election_id";
$total_votes_result = $conn->query($total_votes_query);
$total_votes = $total_votes_result->fetch_row()[0];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Candidate Dashboard | EMS</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body class="bg-gray-100 font-sans">

<div class="flex min-h-screen">
  <!-- Sidebar -->
  <aside class="w-64 bg-purple-800 text-xl text-white flex flex-col">
    <div class="p-6 border-b border-purple-700 flex items-center space-x-3">
      <div class="rounded-full bg-purple-600 w-10 h-10 flex items-center justify-center">
        <i class="fas fa-user-tie text-xl"></i>
      </div>
      <div>
        <div class="text-xl font-bold">EMS Candidate</div>
        <div class="text-xs text-purple-300"><?php echo htmlspecialchars($candidate['full_name'] ?? 'Candidate'); ?></div>
      </div>
    </div>
    
    <nav class="flex-1 p-4 space-y-1">
      <a href="/EMS2/candidate/dashboard.php" class="flex items-center py-2 px-4 rounded bg-purple-700 text-white">
        <i class="fas fa-tachometer-alt w-6"></i>
        <span class="ml-2">Dashboard</span>
      </a>
      <a href="/EMS2/candidate/active_elections.php" class="flex items-center py-2 px-4 rounded hover:bg-purple-700 text-purple-200 hover:text-white">
        <i class="fas fa-vote-yea w-6"></i>
        <span class="ml-2">Active Elections</span>
      </a>
      <a href="/EMS2/candidate/profile.php" class="flex items-center py-2 px-4 rounded hover:bg-purple-700 text-purple-200 hover:text-white">
        <i class="fas fa-user-circle w-6"></i>
        <span class="ml-2">My Profile</span>
      </a>
      <a href="/EMS2/candidate/results.php" class="flex items-center py-2 px-4 rounded hover:bg-purple-700 text-purple-200 hover:text-white">
        <i class="fas fa-chart-bar w-6"></i>
        <span class="ml-2">Results</span>
      </a>
      
      <div class="pt-4 mt-6 border-t border-purple-700">
        <a href="/EMS2/candidate/logout.php" class="flex items-center py-2 px-4 rounded bg-red-600 hover:bg-red-700 text-white">
          <i class="fas fa-sign-out-alt w-6"></i>
          <span class="ml-2">Logout</span>
        </a>
      </div>
    </nav>
    
    <div class="p-4 text-xs text-purple-300">
      <p>Election Management System</p>
      <p>© 2023 All Rights Reserved</p>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-8 overflow-auto">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Welcome, <?php echo htmlspecialchars($candidate['full_name']); ?>!</h1>
      <div class="text-sm text-gray-600">
        <?php echo date('l, F j, Y'); ?>
      </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
        <div class="flex items-center">
          <div class="rounded-full bg-purple-100 p-3 mr-4">
            <i class="fas fa-poll-h text-purple-500 text-xl"></i>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-500">Total Elections</div>
            <div class="text-2xl font-bold text-purple-800"><?php echo $total_elections; ?></div>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center">
          <div class="rounded-full bg-green-100 p-3 mr-4">
            <i class="fas fa-vote-yea text-green-500 text-xl"></i>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-500">Active Elections</div>
            <div class="text-2xl font-bold text-green-700"><?php echo $active_elections; ?></div>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center">
          <div class="rounded-full bg-blue-100 p-3 mr-4">
            <i class="fas fa-check-square text-blue-500 text-xl"></i>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-500">Total Votes in Your Election</div>
            <div class="text-2xl font-bold text-blue-700"><?php echo $total_votes; ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Election Results -->
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
      <div class="bg-blue-50 px-6 py-4 border-b border-blue-100 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-blue-800">Current Election Results</h2>
        <a href="results.php" class="text-blue-600 hover:text-blue-800 text-sm">View Detailed Results</a>
      </div>
      <div class="p-6">
      
      <?php if ($candidates_result && $candidates_result->num_rows > 0): ?>
        <div class="overflow-x-auto">
          <table class="min-w-full bg-white">
            <thead class="bg-gray-100">
              <tr>
                <th class="py-3 px-4 text-left"><i class="fas fa-trophy text-yellow-500 mr-1"></i> Rank</th>
                <th class="py-3 px-4 text-left"><i class="fas fa-image text-gray-500 mr-1"></i> Photo</th>
                <th class="py-3 px-4 text-left"><i class="fas fa-user-tie text-purple-500 mr-1"></i> Candidate</th>
                <th class="py-3 px-4 text-left"><i class="fas fa-flag text-blue-500 mr-1"></i> Party</th>
                <th class="py-3 px-4 text-center"><i class="fas fa-check-square text-green-500 mr-1"></i> Votes</th>
                <th class="py-3 px-4 text-center"><i class="fas fa-chart-pie text-red-500 mr-1"></i> Percentage</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                $rank = 1;
                while ($row = $candidates_result->fetch_assoc()): 
                  $percentage = $total_votes > 0 ? round(($row['vote_count'] / $total_votes) * 100, 1) : 0;
                  $is_current_candidate = ($row['id'] == $candidate_id);
                  $row_class = $is_current_candidate ? 'bg-green-50' : '';
              ?>
                <tr class="<?= $row_class ?>">
                  <td class="py-3 px-4 border-b"><?= $rank++ ?></td>
                  <td class="py-3 px-4 border-b">
                    <?= getCandidateImageHtml(
                      $row['profile_image'], 
                      $row['full_name'], 
                      'w-10 h-10 rounded-full object-cover'
                    ) ?>
                  </td>
                  <td class="py-3 px-4 border-b font-medium <?= $is_current_candidate ? 'text-green-700' : '' ?>">
                    <?= htmlspecialchars($row['full_name']) ?>
                    <?= $is_current_candidate ? ' (You)' : '' ?>
                  </td>
                  <td class="py-3 px-4 border-b"><?= htmlspecialchars($row['party'] ?: 'Independent') ?></td>
                  <td class="py-3 px-4 border-b text-center font-bold"><?= $row['vote_count'] ?></td>
                  <td class="py-3 px-4 border-b text-center"><?= $percentage ?>%</td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-gray-600">No candidates found for this election.</p>
      <?php endif; ?>
    </div>

    <!-- Call to Action -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <div class="bg-purple-50 px-6 py-4 border-b border-purple-100">
        <h2 class="text-lg font-semibold text-purple-800"><i class="fas fa-bolt mr-2"></i>Quick Actions</h2>
      </div>
      <div class="p-6">
        <p class="text-lg text-gray-700 mb-4 flex items-center">
          <i class="fas fa-info-circle text-blue-500 mr-2"></i>
          View more detailed results and statistics for your elections.
        </p>
        <div class="flex flex-wrap gap-4 mt-6">
          <a href="results.php" class="flex items-center bg-purple-600 hover:bg-purple-700 text-white px-5 py-3 rounded-md transition shadow-sm">
            <i class="fas fa-chart-bar mr-2"></i> View Detailed Results
          </a>
          <a href="profile.php" class="flex items-center bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-md transition shadow-sm">
            <i class="fas fa-user-edit mr-2"></i> Update My Profile
          </a>
        </div>
      </div>
    </div>
  </main>
</div>

</body>
</html>

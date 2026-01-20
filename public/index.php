<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Get the request path and method
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Normalize path - remove base directory paths for different server setups
// This handles both Apache (with /gymm/public) and PHP built-in server
$path = preg_replace('#^/gymm/public#', '', $path);
$path = preg_replace('#^/gymm#', '', $path);
$path = preg_replace('#^/public#', '', $path);
if ($path === '' || $path === '/') $path = '/';

require_once dirname(__DIR__).'/app/controllers/AuthController.php';
require_once dirname(__DIR__).'/app/controllers/AdminController.php';
require_once dirname(__DIR__).'/app/controllers/TrainerController.php';
require_once dirname(__DIR__).'/app/controllers/MemberController.php';
require_once dirname(__DIR__).'/app/controllers/ApiController.php';

$auth    = new AuthController();
$admin   = new AdminController();
$trainer = new TrainerController();
$member  = new MemberController();
$api     = new ApiController();

// Redirect root to login page
if ($path === '/' && $method === 'GET') {
  header("Location: /login");
  exit;
}

/* AUTH */
if ($path === '/login' && $method === 'GET')  { $auth->showLogin(); exit; }
if ($path === '/login' && $method === 'POST') { $auth->login(); exit; }

if ($path === '/register' && $method === 'GET')  { $auth->showRegister(); exit; }
if ($path === '/register' && $method === 'POST') { $auth->register(); exit; }

if ($path === '/logout' && $method === 'GET') { $auth->logout(); exit; }
if ($path === '/dashboard' && $method === 'GET') { $auth->dashboard(); exit; }

/* ADMIN ROUTES */
if ($path === '/admin/users' && $method === 'GET') { $admin->usersPage(); exit; }
if ($path === '/admin/plans' && $method === 'GET') { $admin->plansPage(); exit; }
if ($path === '/admin/payments' && $method === 'GET') { $admin->paymentsPage(); exit; }
if ($path === '/admin/schedules' && $method === 'GET') { $admin->schedulesPage(); exit; }
if ($path === '/admin/assignments' && $method === 'GET') { $admin->assignmentsPage(); exit; }

/* TRAINER ROUTES */
if ($path === '/trainer/members' && $method === 'GET') { $trainer->membersPage(); exit; }
if ($path === '/trainer/workouts' && $method === 'GET') { $trainer->workoutsPage(); exit; }
if ($path === '/trainer/schedule' && $method === 'GET') { $trainer->schedulePage(); exit; }

/* MEMBER ROUTES */
if ($path === '/member/profile' && $method === 'GET') { $member->profilePage(); exit; }
if ($path === '/member/password' && $method === 'GET') { $member->changePasswordPage(); exit; }
if ($path === '/member/membership' && $method === 'GET') { $member->membershipPage(); exit; }
if ($path === '/member/workouts' && $method === 'GET') { $member->workoutsPage(); exit; }
if ($path === '/member/payments' && $method === 'GET') { $member->paymentsPage(); exit; }

// Member API routes
if ($path === '/api/member/membership' && $method === 'GET') { $api->getMemberMembership(); exit; }
if ($path === '/api/payments/create' && $method === 'POST') { $api->createPayment(); exit; }

/* API ROUTES */
if ($path === '/api/profile' && $method === 'GET') { $api->getProfile(); exit; }
if ($path === '/api/profile' && $method === 'PUT') { $api->updateProfile(); exit; }

if ($path === '/api/users' && $method === 'GET') { $api->getUsers(); exit; }
// Match /api/users/{id} pattern for getting single user
if (preg_match('#^/api/users/(\d+)$#', $path, $matches) && $method === 'GET') {
  $api->getUserById((int)$matches[1]);
  exit;
}
if ($path === '/api/users' && $method === 'POST') { $api->createUser(); exit; }
if ($path === '/api/users/update' && $method === 'POST') { $api->updateUser(); exit; }
if ($path === '/api/users/toggle' && $method === 'POST') { $api->toggleUserStatus(); exit; }
if ($path === '/api/users/delete' && $method === 'POST') { $api->deleteUser(); exit; }

// Plan routes
if ($path === '/api/plans' && $method === 'GET') { $api->getPlans(); exit; }
if (preg_match('#^/api/plans/(\d+)$#', $path, $matches) && $method === 'GET') {
  $api->getPlanById((int)$matches[1]);
  exit;
}
if ($path === '/api/plans' && $method === 'POST') { $api->createPlan(); exit; }
if ($path === '/api/plans/update' && $method === 'POST') { $api->updatePlan(); exit; }
if ($path === '/api/plans/delete' && $method === 'POST') { $api->deletePlan(); exit; }

// Payment routes
if ($path === '/api/payments' && $method === 'GET') { $api->getPayments(); exit; }
if ($path === '/api/payments/stats' && $method === 'GET') { $api->getPaymentStats(); exit; }
if ($path === '/api/payments/dues' && $method === 'GET') { $api->getMemberDues(); exit; }

// Schedule routes
if ($path === '/api/schedules' && $method === 'GET') { $api->getSchedules(); exit; }
if ($path === '/api/schedules' && $method === 'POST') { $api->createSchedule(); exit; }
if ($path === '/api/schedules/delete' && $method === 'POST') { $api->deleteSchedule(); exit; }

// Membership assignment routes
if ($path === '/api/assign-member' && $method === 'POST') { $api->assignMemberToTrainer(); exit; }
if (preg_match('#^/api/member/(\d+)/trainer$#', $path, $matches) && $method === 'GET') {
  $api->getMemberTrainer((int)$matches[1]);
  exit;
}
if (preg_match('#^/api/trainer/(\d+)/members$#', $path, $matches) && $method === 'GET') {
  $api->getAssignedMembers((int)$matches[1]);
  exit;
}

if ($path === '/api/plans' && $method === 'GET') { $api->getPlans(); exit; }
if ($path === '/api/plans' && $method === 'POST') { $api->createPlan(); exit; }

if ($path === '/api/payments' && $method === 'GET') { $api->getPayments(); exit; }

if ($path === '/api/workouts' && $method === 'GET') { $api->getWorkouts(); exit; }
if ($path === '/api/workouts' && $method === 'POST') { $api->createWorkout(); exit; }
if ($path === '/api/workouts/delete' && $method === 'POST') { $api->deleteWorkout(); exit; }
if (preg_match('#^/api/workouts/(\d+)/assignments$#', $path, $matches) && $method === 'GET') {
  $api->getWorkoutAssignments((int)$matches[1]);
  exit;
}

if ($path === '/api/trainer/members' && $method === 'GET') { $api->getTrainerMembers(); exit; }
if ($path === '/api/trainer/schedule' && $method === 'GET') { $api->getSchedule(); exit; }

if ($path === '/api/member/membership' && $method === 'GET') { $api->getMembership(); exit; }

if ($path === '/api/password' && $method === 'POST') { $api->changePassword(); exit; }

http_response_code(404);
echo "404 Not Found";

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Lawyer Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      background-color: #f8f9fa;
    }
    .hero {
      background: linear-gradient(to right, rgba(0,0,0,0.6), rgba(0,0,0,0.2)), url('https://source.unsplash.com/1600x600/?courtroom,lawyer') no-repeat center center/cover;
      color: white;
      text-align: center;
      padding: 60px 20px;
    }
    .hero .profile-img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #fff;
      margin-bottom: 15px;
    }
    .card {
      border-radius: 15px;
      transition: transform 0.3s ease;
    }
    .card:hover {
      transform: scale(1.03);
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">LexAsk Lawyer</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="#">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Clients</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Appointments</a></li>
          <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="#">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <img src="https://i.pravatar.cc/120?u=lawyer" alt="Lawyer Profile" class="profile-img" />
    <h2 class="mb-1">Welcome, Ayesha Rahman</h2>
    <p>Manage your profile, schedule & tasks with ease</p>
    <button class="btn btn-outline-light mt-2" data-bs-toggle="modal" data-bs-target="#profileModal">Edit Profile</button>
  </section>

  <!-- Action Buttons -->
  <div class="container mt-4">
    <div class="row text-center">
      <div class="col-md-4 mb-3">
        <button class="btn btn-primary w-100 p-3" data-bs-toggle="modal" data-bs-target="#profileModal">Update Profile</button>
      </div>
      <div class="col-md-4 mb-3">
        <button class="btn btn-success w-100 p-3" data-bs-toggle="modal" data-bs-target="#scheduleModal">Update Schedule</button>
      </div>
      <div class="col-md-4 mb-3">
        <button class="btn btn-warning w-100 p-3" data-bs-toggle="modal" data-bs-target="#taskModal">Add Daily Task</button>
      </div>
    </div>
  </div>

  <!-- Dashboard Stats -->
  <div class="container mt-4">
    <div class="row text-center">
      <div class="col-md-4 mb-3">
        <div class="card p-3 shadow-sm">
          <h5>📂 Running Cases</h5>
          <h3>10</h3>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card p-3 shadow-sm">
          <h5>✅ Finished Cases</h5>
          <h3>25</h3>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card p-3 shadow-sm">
          <h5>⏳ Pending Cases</h5>
          <h3>5</h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Meetings & Tasks -->
  <div class="container mt-4">
    <div class="row">
      <div class="col-md-8 mb-3">
        <div class="card p-3 shadow-sm">
          <h4>📅 Upcoming Meetings</h4>
          <ul>
            <li>John Smith - April 24, 2025 - 2:00 PM</li>
            <li>Emily Johnson - April 25, 2025 - 11:00 AM</li>
          </ul>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card p-3 shadow-sm">
          <h4>📝 Daily Tasks</h4>
          <ul>
            <li>Prepare client report</li>
            <li>Submit court documents</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-dark text-white mt-5 pt-4 pb-2">
    <div class="container">
      <div class="row text-center text-md-start">
        <div class="col-md-4 mb-3">
          <h5>Newsletter</h5>
          <input type="email" class="form-control mb-2" placeholder="Your email address">
          <button class="btn btn-primary w-100">Subscribe</button>
        </div>
        <div class="col-md-4 mb-3">
          <h5>Contact</h5>
          <p>Email: support@lexask.com</p>
          <p>Phone: +8801XXXXXXXXX</p>
        </div>
        <div class="col-md-4 mb-3">
          <h5>Follow Us</h5>
          <a href="#" class="text-white me-2">Facebook</a>
          <a href="#" class="text-white me-2">Twitter</a>
          <a href="#" class="text-white">LinkedIn</a>
        </div>
      </div>
      <div class="text-center mt-3">
        <small>© 2025 LexAsk. All Rights Reserved.</small>
      </div>
    </div>
  </footer>

  <!-- Modals -->
  <!-- Profile Modal -->
  <div class="modal fade" id="profileModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5>Update Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form>
            <input type="text" class="form-control mb-2" placeholder="Full Name" value="Ayesha Rahman">
            <input type="text" class="form-control mb-2" placeholder="Specialization" value="Criminal Law">
            <input type="email" class="form-control mb-2" placeholder="Email">
            <input type="tel" class="form-control mb-2" placeholder="Phone Number">
            <input type="file" class="form-control mb-2">
            <button class="btn btn-primary w-100">Save Changes</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Schedule Modal -->
  <div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5>Update Schedule</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form>
            <input type="date" class="form-control mb-2">
            <input type="text" class="form-control mb-2" placeholder="Days Off">
            <button class="btn btn-success w-100">Save</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Task Modal -->
  <div class="modal fade" id="taskModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5>Add Daily Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form>
            <input type="date" class="form-control mb-2">
            <input type="text" class="form-control mb-2" placeholder="Task Description">
            <button class="btn btn-warning w-100">Save Task</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

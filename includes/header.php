<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .navbar-glass {
        background-color: rgba(0, 35, 102, 0.8) !important; /* Matches the dark-blue admin sidebar theme */
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
    }
    .nav-link {
        font-size: 1.05rem;
        transition: color 0.3s ease;
    }
    .nav-link:hover {
        color: #0d6efd !important;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark navbar-glass fixed-top py-3">
  <div class="container">
    <a class="navbar-brand fw-bold fs-3" href="index.php">
        <i class="fa-solid fa-book-open me-2" style="color: #0d6efd;"></i> UnivLibrary
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center gap-2">
        <li class="nav-item">
          <a class="nav-link text-white fw-medium active" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white fw-medium" href="auth/Login.php">Login</a>
        </li>
        <li class="nav-item ms-lg-2">
          <a class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm" href="auth/register.php">
            Get Started
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>
    .custom-footer {
        background: linear-gradient(180deg, #002366, #001845);
        color: #ffffff;
        padding: 40px 0 20px;
        margin-top: auto;
    }
    .footer-link {
        color: #adb5bd;
        text-decoration: none;
        transition: color 0.3s;
    }
    .footer-link:hover {
        color: #ffffff;
        text-decoration: underline;
    }
    .social-icons a {
        color: #adb5bd;
        font-size: 1.2rem;
        margin-right: 15px;
        transition: color 0.3s;
    }
    .social-icons a:hover {
        color: #0d6efd;
    }
</style>

<footer class="custom-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold"><i class="fa-solid fa-book-open me-2" style="color: #0d6efd;"></i> UnivLibrary</h5>
                <p class="text" style="font-size: 0.95rem;">
                    Empowering learning through seamless access to knowledge. Your ultimate gateway to a world of books and educational resources.
                </p>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="/webbasedfinal_exam/index.php" class="footer-link">Home</a></li>
                    <li class="mb-2"><a href="/webbasedfinal_exam/auth/Login.php" class="footer-link">Student / Admin Login</a></li>
                    <li class="mb-2"><a href="/webbasedfinal_exam/auth/register.php" class="footer-link">Create Account</a></li>
                    <li class="mb-2"><a href="/webbasedfinal_exam/auth/logout.php" class="footer-link">logout</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3">Connect With Us</h5>
                <div class="social-icons mt-3">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class=" text-center" style="color: #adb5bd;">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> UnivLibrary. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<?php
/**
 * LearnSphere – Semantic Footer Component
 */
?>
    <!-- Semantic Footer Component -->
    <footer class="site-footer">
        <div class="container footer-grid">
            <!-- Col 1: About Platform -->
            <div class="footer-col">
                <img src="assets/images/logo.svg" alt="LearnSphere Logo" style="filter: brightness(0) invert(1); height: 42px; margin-bottom: 18px;">
                <p>
                    LearnSphere is an accredited online academic learning and course enrollment portal empowering global learners with job-ready tech, engineering, and design skills.
                </p>
                <div style="display: flex; gap: 12px; margin-top: 16px;">
                    <a href="https://github.com" target="_blank" rel="noopener noreferrer" style="color: #94A3B8; font-size: 1.2rem;" aria-label="GitHub"><i class="fab fa-github"></i></a>
                    <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" style="color: #94A3B8; font-size: 1.2rem;" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                    <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" style="color: #94A3B8; font-size: 1.2rem;" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" style="color: #94A3B8; font-size: 1.2rem;" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Col 2: Quick Links -->
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.php"><i class="fas fa-chevron-right" style="font-size: 0.7rem; margin-right: 6px;"></i> Home Portal</a></li>
                    <li><a href="courses.php"><i class="fas fa-chevron-right" style="font-size: 0.7rem; margin-right: 6px;"></i> All Academic Courses</a></li>
                    <li><a href="register.php"><i class="fas fa-chevron-right" style="font-size: 0.7rem; margin-right: 6px;"></i> Student Registration</a></li>
                    <li><a href="about.php"><i class="fas fa-chevron-right" style="font-size: 0.7rem; margin-right: 6px;"></i> About Platform</a></li>
                    <li><a href="about.php#contact"><i class="fas fa-chevron-right" style="font-size: 0.7rem; margin-right: 6px;"></i> Contact Academic Support</a></li>
                    <li><a href="admin.php"><i class="fas fa-chevron-right" style="font-size: 0.7rem; margin-right: 6px;"></i> Admin Dashboard</a></li>
                </ul>
            </div>

            <!-- Col 3: Popular Tracks -->
            <div class="footer-col">
                <h4>Course Tracks</h4>
                <ul class="footer-links">
                    <li><a href="courses.php?category=1"><i class="fas fa-chevron-right" style="font-size: 0.7rem; margin-right: 6px;"></i> Web Engineering</a></li>
                    <li><a href="courses.php?category=2"><i class="fas fa-chevron-right" style="font-size: 0.7rem; margin-right: 6px;"></i> Artificial Intelligence</a></li>
                    <li><a href="courses.php?category=3"><i class="fas fa-chevron-right" style="font-size: 0.7rem; margin-right: 6px;"></i> Cloud &amp; DevOps</a></li>
                    <li><a href="courses.php?category=4"><i class="fas fa-chevron-right" style="font-size: 0.7rem; margin-right: 6px;"></i> Cybersecurity</a></li>
                    <li><a href="courses.php?category=5"><i class="fas fa-chevron-right" style="font-size: 0.7rem; margin-right: 6px;"></i> UI/UX Design</a></li>
                </ul>
            </div>

            <!-- Col 4: Academic Headquarters & Hours -->
            <div class="footer-col">
                <h4>Academic Desk</h4>
                <p><i class="fas fa-map-marker-alt" style="color: var(--accent-color); margin-right: 8px;"></i> 100 University Avenue, Tech Park Suite 400, Cambridge, MA 02138</p>
                <p><i class="fas fa-envelope" style="color: var(--accent-color); margin-right: 8px;"></i> admissions@learnsphere.edu</p>
                <p><i class="fas fa-phone-alt" style="color: var(--accent-color); margin-right: 8px;"></i> +1 (800) 555-LEARN</p>
                <p><i class="fas fa-clock" style="color: var(--accent-color); margin-right: 8px;"></i> Mon – Fri: 08:00 AM – 06:00 PM EST</p>
            </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="footer-bottom">
            <div class="container footer-bottom-flex">
                <p>&copy; <?php echo date('Y'); ?> LearnSphere Academic Systems. All Rights Reserved. Fully W3C Semantic HTML5 &amp; CSS3 Compliant.</p>
                <p>Developed with PHP 8, MySQL PDO, &amp; Vanilla CSS3 Architecture.</p>
            </div>
        </div>
    </footer>

    <!-- Main Interactive Client JavaScript -->
    <script src="assets/js/script.js"></script>
</body>
</html>

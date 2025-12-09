<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ARTHEMIS - Waste Management Solution</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        /* Cloud decorations */
        .cloud {
            position: absolute;
            background: white;
            border-radius: 100px;
            opacity: 0.9;
            z-index: 2;
        }

        .cloud-1 {
            width: 180px;
            height: 80px;
            top: 80px;
            left: 50px;
            animation: float 8s ease-in-out infinite;
        }

        .cloud-2 {
            width: 220px;
            height: 90px;
            top: 150px;
            right: 100px;
            animation: float 10s ease-in-out infinite 2s;
        }

        .cloud-3 {
            width: 150px;
            height: 70px;
            bottom: 200px;
            left: 100px;
            animation: float 9s ease-in-out infinite 1s;
        }

        .cloud-4 {
            width: 200px;
            height: 85px;
            bottom: 150px;
            right: 150px;
            animation: float 11s ease-in-out infinite 3s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .hero-content {
            flex: 1;
            max-width: 650px;
            z-index: 10;
            position: relative;
            padding-right: 40px;
        }

        .hero-content h1 {
            font-size: 56px;
            color: white;
            line-height: 1.2;
            margin-bottom: 24px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-content p {
            font-size: 20px;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 32px;
            line-height: 1.6;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .cta-btn {
            padding: 16px 40px;
            background: rgba(15, 32, 39, 0.9);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .cta-btn:hover {
            background: rgba(15, 32, 39, 1);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
        }
        /* Features Section */
        .features {
            padding: 100px 60px;
            background: white;
        }

        .features h2 {
            text-align: center;
            font-size: 42px;
            color: #2c3e50;
            margin-bottom: 60px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            padding: 40px;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 16px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.2);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #10b981, #047857);
            border-radius: 50%;
            margin: 0 auto 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
        }

        .feature-card h3 {
            font-size: 24px;
            color: #047857;
            margin-bottom: 16px;
        }

        .feature-card p {
            color: #64748b;
            line-height: 1.6;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            nav {
                padding: 15px 20px;
            }

            .nav-links {
                display: none;
            }

            .hero {
                flex-direction: column;
                padding: 100px 20px 40px 20px;
                text-align: center;
            }

            .hero-content {
                padding-right: 0;
                max-width: 100%;
            }

            .hero-content h1 {
                font-size: 36px;
            }

            .hero-illustration {
                margin-top: 40px;
                max-width: 100%;
            }

            .features {
                padding: 60px 20px;
            }

            .cloud {
                display: none;
            }
        }

        /* Hero Section */
        .hero {
            position: relative;
            height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url('{{ asset('images/background-landing.png') }}') center/cover no-repeat;
            display: flex;
            align-items: center;
            color: white;
        }

        /* Navigation */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 60px;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        nav.scrolled {
            background: rgba(0, 0, 20, 0.95);
            padding: 15px 60px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 4px;
            color: white;
            text-decoration: none;
        }

        .logo::before {
            content: '🌿';
            font-size: 28px;
        }

        .nav-links {
            display: flex;
            gap: 40px;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: #10b981;
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .login-btn {
            padding: 12px 28px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .login-btn::before {
            content: '👤';
        }

        .login-btn:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(16, 185, 129, 0.4);
        }

        .logout-btn:hover {
            background: #dc2626 !important;
            box-shadow: 0 5px 20px rgba(239, 68, 68, 0.4) !important;
        }

        /* Hero Content */
        .hero-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 60px;
            margin-top: 80px;
        }

        .hero h1 {
            font-size: 72px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 30px;
            max-width: 800px;
            animation: fadeInUp 1s ease;
        }

        .hero h1 span {
            color: #10b981;
        }

        .contact-btn {
            padding: 16px 40px;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            animation: fadeInUp 1.2s ease;
        }

        .contact-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        /* Mobile Menu Toggle */
        .menu-toggle {
            display: none;
            flex-direction: column;
            gap: 6px;
            cursor: pointer;
            padding: 10px;
        }

        .menu-toggle span {
            width: 30px;
            height: 3px;
            background: white;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Scroll Indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            animation: bounce 2s infinite;
        }

        .scroll-indicator::before {
            content: '⌄';
            font-size: 32px;
            color: white;
            opacity: 0.7;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateX(-50%) translateY(0);
            }
            40% {
                transform: translateX(-50%) translateY(-10px);
            }
            60% {
                transform: translateX(-50%) translateY(-5px);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            nav {
                padding: 15px 30px;
            }

            .nav-links {
                position: fixed;
                top: 70px;
                left: -100%;
                flex-direction: column;
                background: rgba(0, 0, 20, 0.98);
                width: 100%;
                padding: 40px;
                gap: 30px;
                transition: left 0.3s ease;
            }

            .nav-links.active {
                left: 0;
            }

            .menu-toggle {
                display: flex;
            }

            .hero h1 {
                font-size: 48px;
            }

            .hero-content {
                padding: 0 30px;
            }

            .login-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav id="navbar">
        <a href="{{ route('home') }}" class="logo">ARTHEMIS</a>
        
        <div class="menu-toggle" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <ul class="nav-links" id="navLinks">
            @auth
                <li><a href="{{ route('dashboard') }}">DASHBOARD</a></li>
            @else
                <li><a href="{{ route('auth.page') }}#dashboard">DASHBOARD</a></li>
            @endauth
            <li><a href="#maps">MAPS</a></li>
            <li><a href="#features">FEATURES</a></li>
            <li><a href="#about">ABOUT US</a></li>
            <li>
                @auth
                    <a href="{{ route('dashboard') }}" class="login-btn">Dashboard</a>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="login-btn logout-btn" style="background: #ef4444; margin-left: 10px;">Logout</button>
                    </form>
                @else
                    <a href="{{ route('auth.page') }}" class="login-btn">Login</a>
                @endauth
            </li>
        </ul>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>
                Elevate Your Environment:<br>
                Redefining Waste for a<br>
                <span>Cleaner, Greener Tomorrow!</span>
            </h1>
            <button class="contact-btn" onclick="scrollToContact()">Contact Us</button>
        </div>
        <div class="scroll-indicator"></div>
    </section>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Mobile menu toggle
        function toggleMenu() {
            const navLinks = document.getElementById('navLinks');
            navLinks.classList.toggle('active');
        }

        // Contact button scroll
        function scrollToContact() {
            // Add your contact section scroll or modal logic here
            alert('Contact form coming soon!');
        }

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
                // Close mobile menu after clicking
                document.getElementById('navLinks').classList.remove('active');
            });
        });
    </script>
      <!-- Features Section -->
    <section class="features" id="features">
        <h2>Why Choose ARTHEMIS?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Real-Time Tracking</h3>
                <p>Monitor waste collection and management in real-time with our advanced dashboard system.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🗺️</div>
                <h3>Interactive Maps</h3>
                <p>Visualize waste collection points and routes with our comprehensive mapping features.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">♻️</div>
                <h3>Sustainability Focus</h3>
                <p>Promote recycling and proper waste segregation for a greener environment.</p>
            </div>
        </div>
    </section>
</body>
</html>
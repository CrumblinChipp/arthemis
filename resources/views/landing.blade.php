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
</body>
</html>
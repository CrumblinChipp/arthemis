<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ARTHEMIS - Login & Register</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: flex;
            min-height: 550px;
        }

        .form-container {
            flex: 1;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-section {
            flex: 1;
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .brand-logo {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .brand-tagline {
            font-size: 18px;
            opacity: 0.9;
            line-height: 1.6;
        }

        .form-title {
            color: #047857;
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .form-subtitle {
            color: #6b7280;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-top: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .form-footer {
            text-align: center;
            margin-top: 25px;
            color: #6b7280;
            font-size: 14px;
        }

        .form-footer a {
            color: #10b981;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .form-footer a:hover {
            color: #047857;
        }

        .tabs {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e5e7eb;
        }

        .tab {
            padding: 10px 20px;
            cursor: pointer;
            color: #6b7280;
            font-weight: 600;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .tab.active {
            color: #10b981;
            border-bottom-color: #10b981;
        }

        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 15px 0;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            cursor: pointer;
        }

        .checkbox-group label {
            margin: 0;
            font-weight: 400;
            cursor: pointer;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #c00;
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .brand-section {
                padding: 30px 20px;
            }

            .form-container {
                padding: 30px 20px;
            }

            .brand-logo {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="brand-section">
            <div class="brand-logo">ARTHEMIS</div>
            <div class="brand-tagline">
                Welcome to our platform. Join us today and experience excellence in every interaction.
            </div>
        </div>

        <div class="form-container">
            <div class="tabs">
                <div class="tab active" onclick="switchTab('login')">Login</div>
                <div class="tab" onclick="switchTab('register')">Register</div>
            </div>

            <!-- Login Form -->
            <div id="login-form" class="form-section active">
                <h2 class="form-title">Welcome Back</h2>
                <p class="form-subtitle">Please enter your credentials to login</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="login-email">Email Address</label>
                        <input type="email" id="login-email" name="email" required placeholder="your@email.com" value="{{ old('email') }}">
                    </div>

                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <input type="password" id="login-password" name="password" required placeholder="Enter your password">
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>

                    <button type="submit" class="btn">Login</button>
                </form>

                <div class="form-footer">
                    <a href="#">Forgot password?</a>
                </div>
            </div>

            <!-- Register Form -->
<div id="register-form" class="form-section">
    <h2 class="form-title">Create Account</h2>
    <p class="form-subtitle">Please fill in the information below</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="register-name">Full Name</label>
            <input type="text" id="register-name" name="name" required placeholder="John Doe" value="{{ old('name') }}">
        </div>

        <div class="form-group">
            <label for="register-sr-code">SR Code</label>
            <input type="text" id="register-sr-code" name="sr_code" required placeholder="Enter your SR Code" value="{{ old('sr_code') }}">
        </div>

        <div class="form-group">
            <label for="register-email">Email Address</label>
            <input type="email" id="register-email" name="email" required placeholder="your@email.com" value="{{ old('email') }}">
        </div>

        <div class="form-group">
            <label for="register-password">Password</label>
            <input type="password" id="register-password" name="password" required placeholder="Create a password">
        </div>

        <div class="form-group">
            <label for="register-password-confirm">Confirm Password</label>
            <input type="password" id="register-password-confirm" name="password_confirmation" required placeholder="Confirm your password">
        </div>

        <div class="checkbox-group">
            <input type="checkbox" id="terms" name="terms" required>
            <label for="terms">I agree to the Terms & Conditions</label>
        </div>

        <button type="submit" class="btn">Create Account</button>
    </form>

    <div class="form-footer">
        Already have an account? <a href="#" onclick="switchTab('login'); return false;">Login here</a>
    </div>
</div>
        <div class="form-group">
    <label for="register-role">Role</label>
    <select id="register-role" name="role" required style="width: 100%; padding: 12px 15px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;">
        <option value="">Select Role</option>
        <option value="user">User</option>
        <option value="admin">Admin</option>
        <option value="staff">Staff</option>
    </select>
</div>

    <script>
        function switchTab(tab) {
            // Update tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');

            // Update forms
            const forms = document.querySelectorAll('.form-section');
            forms.forEach(f => f.classList.remove('active'));
            
            if (tab === 'login') {
                document.getElementById('login-form').classList.add('active');
            } else {
                document.getElementById('register-form').classList.add('active');
            }
        }
    </script>
</body>
</html>
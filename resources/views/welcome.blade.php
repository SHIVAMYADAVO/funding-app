<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Admin Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(to right, #ff9966, #ff5e62);
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 15px 25px rgba(0, 0, 0, 0.3);
            width: 400px;
            text-align: center;
            transition: transform 0.3s;
        }
        .login-container:hover {
            transform: scale(1.05);
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #d63384;
            font-size: 26px;
            font-weight: bold;
        }
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #444;
        }
        .input-group input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            background: #f9f9f9;
            transition: 0.3s;
        }
        .input-group input:focus {
            background: #eaeaea;
            outline: none;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #d63384;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: bold;
        }
        .btn:hover {
            background: #b81b6e;
        }
        .register-link {
            margin-top: 15px;
            display: block;
            color: #fff;
            font-weight: bold;
            text-decoration: none;
            transition: 0.3s;
        }
        .register-link:hover {
            color: #f8d7da;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Donation Admin Login</h2>
        @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
        <form action="{{ route('authenticate') }}" method="post" >
            @csrf

            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            @error('email')
            <p class="invalid-feedback">{{ $message }}</p>
            @enderror
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            @error('password')
            <p class="invalid-feedback">{{ $message }}</p>
            @enderror
            <button type="submit" class="btn">Login</button>
            <a href="#" class="register-link">Forgot Password?</a>
        </form>
    </div>
</body>
</html>

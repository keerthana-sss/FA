<!DOCTYPE html>
<html>
<head>
    <title>TripHub Auth Home</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        body { font-family: Arial; padding: 20px; }
        .card { border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; width: 350px; }
        textarea { width: 100%; height: 150px; margin-top: 10px; }
    </style>
</head>
<body>

    <h1>TripHub Authentication Test Panel</h1>

    <!-- REGISTER -->
    <div class="card">
        <h3>Register</h3>
        <form id="registerForm">
            <input type="text" name="name" placeholder="Name" required /><br><br>
            <input type="email" name="email" placeholder="Email" required /><br><br>
            <input type="password" name="password" placeholder="Password" required /><br><br>
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required /><br><br>
            <button type="submit">Register</button>
        </form>
    </div>

    <!-- LOGIN -->
    <div class="card">
        <h3>Login</h3>
        <form id="loginForm">
            <input type="email" name="email" placeholder="Email" required /><br><br>
            <input type="password" name="password" placeholder="Password" required /><br><br>
            <button type="submit">Login</button>
        </form>
    </div>

    <!-- LOGOUT -->
    <div class="card">
        <h3>Logout</h3>
        <button id="logoutBtn">Logout</button>
    </div>

    <!-- RESPONSE OUTPUT -->
    <h3>API Response</h3>
    <textarea id="responseBox" readonly></textarea>

    <script>
        const responseBox = document.getElementById('responseBox');

        function showResponse(data) {
            responseBox.value = JSON.stringify(data, null, 2);
        }

        // REGISTER
        document.getElementById('registerForm').addEventListener('submit', function(e){
            e.preventDefault();
            
            const data = {
                name: this.name.value,
                email: this.email.value,
                password: this.password.value,
                password_confirmation: this.password_confirmation.value
            };

            axios.post('/api/auth/register', data)
                 .then(res => {
                     showResponse(res.data);
                     localStorage.setItem('access_token', res.data.data.token.access_token);
                 })
                 .catch(err => showResponse(err.response.data));
        });

        // LOGIN
        document.getElementById('loginForm').addEventListener('submit', function(e){
            e.preventDefault();

            const data = {
                email: this.email.value,
                password: this.password.value
            };

            axios.post('/api/auth/login', data)
                 .then(res => {
                     showResponse(res.data);
                     localStorage.setItem('access_token', res.data.data.token.access_token);
                 })
                 .catch(err => showResponse(err.response.data));
        });

        // LOGOUT
        document.getElementById('logoutBtn').addEventListener('click', function(){
            const token = localStorage.getItem('access_token');

            if (!token) {
                showResponse({ error: "No token found. Please login first." });
                return;
            }

            axios.post('/api/auth/logout', {}, {
                headers: { Authorization: "Bearer " + token }
            })
            .then(res => {
                showResponse(res.data);
                localStorage.removeItem('access_token');
            })
            .catch(err => showResponse(err.response.data));
        });
    </script>

</body>
</html>

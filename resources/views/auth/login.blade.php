<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <h1>Login</h1>
    <form id="loginForm">
        <input type="email" name="email" placeholder="Email" required /><br><br>
        <input type="password" name="password" placeholder="Password" required /><br><br>
        <button type="submit">Login</button>
    </form>

    <div id="response"></div>

    <script>
        const form = document.getElementById('loginForm');
        const responseDiv = document.getElementById('response');

        form.addEventListener('submit', function(e){
            e.preventDefault();

            const data = {
                email: form.email.value,
                password: form.password.value
            };

            axios.post('/api/auth/login', data)
                 .then(res => {
                     responseDiv.innerHTML = JSON.stringify(res.data, null, 2);
                     // optionally save token in localStorage for logout
                     localStorage.setItem('access_token', res.data.data.token.access_token);
                 })
                 .catch(err => {
                     responseDiv.innerHTML = JSON.stringify(err.response.data, null, 2);
                 });
        });
    </script>
</body>
</html>

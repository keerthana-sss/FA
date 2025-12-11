<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <h1>Register</h1>
    <form id="registerForm">
        <input type="text" name="name" placeholder="Name" required /><br><br>
        <input type="email" name="email" placeholder="Email" required /><br><br>
        <input type="password" name="password" placeholder="Password" required /><br><br>
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required /><br><br>
        <button type="submit">Register</button>
    </form>

    <div id="response"></div>

    <script>
        const form = document.getElementById('registerForm');
        const responseDiv = document.getElementById('response');

        form.addEventListener('submit', function(e){
            e.preventDefault();

            const data = {
                name: form.name.value,
                email: form.email.value,
                password: form.password.value,
                password_confirmation: form.password_confirmation.value
            };

            axios.post('/api/auth/register', data)
                 .then(res => {
                     responseDiv.innerHTML = JSON.stringify(res.data, null, 2);
                 })
                 .catch(err => {
                     responseDiv.innerHTML = JSON.stringify(err.response.data, null, 2);
                 });
        });
    </script>
</body>
</html>

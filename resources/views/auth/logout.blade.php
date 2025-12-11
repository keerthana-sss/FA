<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <h1>Logout</h1>
    <button id="logoutBtn">Logout</button>

    <div id="response"></div>

    <script>
        const logoutBtn = document.getElementById('logoutBtn');
        const responseDiv = document.getElementById('response');

        logoutBtn.addEventListener('click', function(){
            const token = localStorage.getItem('access_token');
            if (!token) {
                responseDiv.innerHTML = 'No token found. Login first.';
                return;
            }

            axios.post('/api/auth/logout', {}, {
                headers: { 'Authorization': 'Bearer ' + token }
            })
            .then(res => {
                responseDiv.innerHTML = JSON.stringify(res.data, null, 2);
                localStorage.removeItem('access_token');
            })
            .catch(err => {
                responseDiv.innerHTML = JSON.stringify(err.response.data, null, 2);
            });
        });
    </script>
</body>
</html>

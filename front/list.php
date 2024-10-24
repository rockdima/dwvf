<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>

<body>
    <div id="usersList">
        <h3>Users List</h3>
        <table>
            <thead>
                <tr>
                    <td>Username</td>
                    <td>Email</td>
                    <td>Action</td>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div id="overlay">
        <div class="popup userData">
            <h2>Users' Data</h2>
            <div class="data"></div>
            <button onclick="popup();">Close Popup</button>
        </div>
    </div>

</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="public/js/script.js"></script>

</html>
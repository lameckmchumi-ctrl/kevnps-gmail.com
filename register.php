
<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            background:#f2f2f2;
        }
        .box{
            width:400px;
            margin:50px auto;
            background:white;
            padding:20px;
            border-radius:10px;
            box-shadow:0 0 10px #aaa;
        }
        input,button{
            width:100%;
            padding:10px;
            margin:6px 0;
        }
        button{
            background:blue;
            color:white;
            border:none;
            font-weight:bold;
            cursor:pointer;
        }
        /* POPUP */
        .popup{
            display:none;
            position:fixed;
            top:0; left:0;
            width:100%; height:100%;
            background:rgba(0,0,0,0.6);
            justify-content:center;
            align-items:center;
        }
        .popup-box{
            background:white;
            padding:20px;
            border-radius:10px;
            text-align:center;
            width:300px;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>REGISTER USER</h2>
    <form action="register_user.php" method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <input type="text" name="district" placeholder="District" required>
        <input type="text" name="ward" placeholder="Ward" required>
        <input type="text" name="street" placeholder="Street" required>
        <input type="text" name="house_no" placeholder="House No" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">REGISTER</button>
    </form>
    <br>
    <a href="index.html">⬅ Rudi Home</a>
</div>

<!-- POPUP -->
<div class="popup" id="popup">
    <div class="popup-box">
        <h3 id="title"></h3>
        <p id="msg"></p>
        <button onclick="closePopup()">OK</button>
    </div>
</div>

<script>
function closePopup(){
    document.getElementById("popup").style.display="none";
}

const p=new URLSearchParams(window.location.search);
if(p.has("error")){
    document.getElementById("title").innerText="ERROR ❌";
    document.getElementById("msg").innerText=p.get("error");
    document.getElementById("popup").style.display="flex";
}
if(p.has("success")){
    document.getElementById("title").innerText="SUCCESS ✅";
    document.getElementById("msg").innerText=p.get("success");
    document.getElementById("popup").style.display="flex";
}
</script>

</body>
</html>

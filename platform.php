<html>
<head>
    <title>Lead Gen</title>
</head>
<body>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '',
      xfbml      : true,
      version    : 'v2.8'
    });
  };
  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
function FBLogin(){
    FB.getLoginStatus(function(response) {
  if (response.status === 'connected') {
    console.log('Logged in.');
  }
  else {
    FB.login();
  }
});
}
function subapp(page_id,page_access_token,page_name){
    console.log("Page ID: " + page_id);
    document.getElementById(page_id).innerHTML = page_name + " <em> Subscribed!</em> page ID: "+page_id;
    FB.api('/' + page_id + '/subscribed_apps',
           'post',
           {access_token: page_access_token},
           function(response){
            console.log(response);
           })
}
// Only works after `FB.init` is called
function myFacebookLogin() {
  FB.login(function(response){
      console.log("Successufuly Loged in ", response);
      FB.api('/me/accounts',function(response){
          console.log("Successfully retrieved pages", response);
          var pages = response.data;
          var ul = document.getElementById('list');
          for(var i = 0, len = pages.length; i < len ; i++){
              var page = pages[i];
              var li = document.createElement('li');
              var a = document.createElement('a');
              a.href = "#";
              a.id = page.id;
              a.onclick = subapp.bind(this,page.id,page.access_token,page.name);
              a.innerHTML = page.name;
              li.appendChild(a);
              ul.appendChild(li);
          }
      })
  }, {scope: 'manage_pages'});
}
</script>
<center>
    <h2>Lead Gen</h2>
    <button onclick="myFacebookLogin()">Login with Facebook</button>
    <ul id="list"></ul>
</center>
</body>
</html>

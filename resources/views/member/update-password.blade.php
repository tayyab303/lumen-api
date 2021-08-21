<!DOCTYPE html>
<html lang="en">
<head>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
 
    <title>Proprietors | Update Password</title>
    <style>
        .form-gap {
    padding-top: 150px;
}
    </style>
</head>
<body>
    

 <div class="form-gap"></div>
 <div class="container">
     <div class="row">
         <div class="col-md-4 col-md-offset-4">
             <div class="panel panel-default">
               <div class="panel-body">
                 <div class="text-center">
                   <h3><i class="fa fa-lock fa-4x"></i></h3>
                   <h2 class="text-center">Update Password!</h2>
                   <div class="panel-body">
     


                     <form onsubmit="return validateForm()" action="{{route('updateMemberPassword')}}" id="register-form" role="form" autocomplete="off" class="form" method="post">
                        <span class="text-danger" id="message"></span>
                        <span class="text-success" id="success_message"></span>
                        <input type="hidden" class="hide" name="token" id="token" value="{{$token}}"> 
     
                       <div class="form-group">
                         <div class="input-group">
                           <span class="input-group-addon"><i class="fa fa-lock color-blue"></i></span>
                           <input id="newpassword" name="newpassword" placeholder="New Password" class="form-control"  type="password"  required>
                           
                        </div>
                         <br/>
                         <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock color-blue"></i></span>
                            <input id="cpassword" name="cpassword" placeholder="Confirm Password" class="form-control"  type="password"  required>
                            
                        </div>
                       </div>
                       <div class="form-group">
                         <input name="recover-submit" class="btn btn-lg btn-primary btn-block" value="Update Password" type="submit">
                       </div>
                       
                     </form>
     
                   </div>
                 </div>
               </div>
             </div>
           </div>
     </div>
 </div>


 <script>
     function validateForm(){
         let newpassword = document.getElementById("newpassword");
         let cpassword = document.getElementById("cpassword");
         let message = document.getElementById("message");
         let success_message = document.getElementById("success_message");

         if(newpassword.value.trim() === cpassword.value.trim()){
              success_message.innerText = "Your new password has been updated. Redirecting to login page..."
              message.innerText = '';
             return true;
         }else if(newpassword.value.length < 8 || cpassword.value.length < 8){
              message.innerText = "Password length must be minimum 8 characters"
              success_message.innerText = '';
              return false;
         }
         else{
                message.innerText = "Both password's are not same...";
                success_message.innerText = '';
             return false;
         }
     }
 </script>
</body>
</html>


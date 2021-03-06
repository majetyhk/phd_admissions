<?PHP
session_start();
require_once("QOB/qob.php");

require_once("backendFunctions.php");

if(isset($_SESSION['userId']))
{
  if(isset($_POST['Submit']))
  {
    $userId=$_SESSION['userId'];

    $oldPassword=$_POST['oldpwd'];

    $newPassword=$_POST['newpwd'];
    
    $confirmNewPassword=$_POST['confirmNewPassword'];

    if($newPassword==$confirmNewPassword)
    {
      $oldPasswordHash=hash("sha512",$oldPassword.PASSSALT);

      $user=getUserByID($userId);

      if($user['password']==$oldPasswordHash)
      {
        //echo "Old Password == New Password <br/>";

        $newPasswordHash=hash("sha512", $newPassword.PASSSALT);

        $values[]=array($newPasswordHash => 's');

        $values[]=array($userId => 's');

        $changePasswordSQL="UPDATE registered_users SET password=? WHERE userId=?";

        $con=new QoB();

        $result=$con->update($changePasswordSQL,$values);

        if($con->error=="")
        {
          displayAlertAndRedirect("Password changed successfully.","forms.php");

          //echo "Password Changed Successfully."

          //RedirectToURL("forms.php");
        }
        else
        {
          notifyAdmin("Conn. Error : $con->error while Changing Password", $userId);

          displayAlertAndRedirect("Some Error occured. Please Try Again Later. Admin will be notified.","forms.php");

          //RedirectToURL("forms.php");
        }
      }
      else
      {
        //echo "Old password not equal to new password <br/>.";
        displayAlert("Old password doesnt match. Try again.");
      }
    }
    else
    {
      displayAlert("Passwords in New Password and Confirm New Password fields doesn't match. Try Again.");
    }
  }
}
else
{
  displayAlertAndRedirect("Please login to continue.", "login.php");

  //RedirectToURL("login.php");
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
  <head>
   <?php require_once("header.php");
    ?>
  </head>

  <body>
    <?php require_once("header_logo.php"); ?>
    <div >
      <form id='changepwd' action='change-pwd.php' method='post' accept-charset='UTF-8'>
        <div style="padding-top: 20px;" class='container'>
          <div style="font-size: 26px;"><center><strong>Change Password</strong></center></div>

           <div style="float: right; right: 0px; padding-top: 20px;">
            <a class="waves-effect waves-light btn" href='forms.php'>Home</a>
          </div>

          <input type='hidden' name='submitted' id='submitted' value='1'/>
          <div class='short_explanation'><font color=red>&nbsp;*</font> required fields</div>
          <div class="row">
            <div class="input-field col s6">
            <input type='password' name='oldpwd' id='oldpwd' maxlength="50" />
            <label for='oldpwd' >Old Password<font color=red>&nbsp;*</font>&nbsp;:</label>
            </div>
          </div>     
          
          <div class="row">
            <div class="input-field col s6">
            <input type='password' name='newpwd' id='newpwd' maxlength="50" />
            <label for='newpwd' >New Password<font color=red>&nbsp;*</font>&nbsp;:</label>
            </div>
          </div>

          <div class="row">
            <div class="input-field col s6">
            <input type='password' name='confirmNewPassword' id='confirmNewPassword' maxlength="50" />
            <label for='confirmNewPassword' >Confirm New Password<font color=red>&nbsp;*</font>&nbsp;:</label>
            </div>
          </div>

          <div class='container'>
              <button class="waves-effect waves-light btn" type='submit' name='Submit' value='Submit' >Submit<i class="mdi-content-send right"></i></button>
          </div>
        </div>
      </form>
     
    </div>
   
  </body>
</html>
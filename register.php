<?PHP
session_start();
require_once('QOB/qob.php');
require_once('backendFunctions.php');
require_once('helperFunctions.php');
//var_dump($_POST);

 if(isset($_SESSION['userId']))
  {
    RedirectToURL("forms.php");
  }
  else
  {
    if(isset($_POST['submit']))
    {

      $conObj = new QoB();
      $emailAddress = trim($_POST['emailAddress']);
      $discipline = trim($_POST['discipline']);
      $modeOfRegistration = trim($_POST['registrationMode']);
      
      $password = trim($_POST['password']);
      $confirmPassword = trim($_POST['confirmPassword']);
      if(validateRegisterFormEntries($_POST))
      {
        if($password==$confirmPassword)
        {
          $passwordHash=hash("sha512",$password.PASSSALT);
          $getMailSQL = "SELECT emailAddress FROM registered_users WHERE emailAddress = ?";
          $values1[]=array($emailAddress => 's');
          $result1 = $conObj->fetchAll($getMailSQL,$values1);
          if($conObj->error == "")
          {
            if($result1 != "")
            {
              // displayAlert("Fetched Something");
              displayAlert("Your Email is already registered. Proceed to Candidate Login Page to login. Please click on resend confirmation link if you haven't received the confirmation mail.");
            }
            else
            {
              $conObj->startTransaction();
              $insertUserSQL = "INSERT INTO registered_users(emailAddress,password,discipline,mode) values(?,?,?,?)";
              $values[]=array($emailAddress => 's');
              $values[]=array($passwordHash => 's');
              $values[]=array($discipline => 's');
              $values[]=array($modeOfRegistration => 's');
              $result=$conObj->insert($insertUserSQL, $values);
              if($conObj -> error=="")
              {
                $userId=$conObj->getInsertId();
                if(sendEmailConfirmationLink($conObj,$_POST,$userId))
                {
                  displayAlert("Registration Successfull. Check your email to confirm registration.");
                  $conObj->completeTransaction();
                  RedirectToURL("confirmreg.php");
                }
                else
                {
                  $conObj->rollbackTransaction();
                  displayAlert("Some Error Occured. Please Try Again");
                }
              }
              else
              {
                notifyAdmin("Conn. Error. $conObj->error while registration",$emailAddress);
                displayAlert("Some Error Occured. Admin has been notified. Please Try Again Later.");
              }
              
            }
          }
          else
          {
            notifyAdmin("Conn. Error. $conObj->error while registration in checking email address",$emailAddress);
            displayAlert("Some Error Occured. Admin has been notified. Please Try Again Later.");
          }
        }
        else
        {
          displayAlert("Entered Password and Confirm Password doesn't match.");
        }
      }
    }
  }
?>
<!--****************************************************************-->

<!--
************************************************************************
register.php : Register users for further login

Inputs :    Email Address  
            Password
            Confirm Password  
Dropdowm :  Discipline
            Mode of Registration
Outputs :   NIL  
Buttons :   Submit
            Resend Confirmation Mail
************************************************************************
-->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
    <head>
          <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
          <!--AUTHORS-->
          <meta name="author" content="Kuldeep Gunta">
          <meta name="author" content="Deepanshu">
          <meta name="author" content="Majety Hari Krishna">
          <meta name="author" content="Gantasala Hemanth">
          <!--AUTHORS-->

          <title>Register</title>
          <!-- <link rel="STYLESHEET" type="text/css" href="style/fg_membersite.css" /> -->
          <!-- Fontawesome CSS -->
          <link rel="stylesheet" href="css/font-awesome.min.css">
          <link href="default.css" rel="stylesheet" type="text/css" media="all" />
          <!-- Matrialize CSS -->
          <link rel="stylesheet" type="text/css" href="css/materialize.min.css" />

          <!-- JQUERY JS-->
          <script type="text/javascript" src="js/jquery-2.1.3.min.js"></script>

          <!-- Materialize JS -->
          <script type="text/javascript" src="js/materialize.min.js"></script>

          <!--<script type='text/javascript' src='scripts/gen_validatorv31.js'></script>-->
    
    </head>

    <!--*********************NAVIGATION BAR****************************-->
    <body>
      <?php require_once("header_logo.php"); ?>
      <?php include_once("menu.php"); ?>
      
    <!--***************************************************************-->

    
    <div id="registerPage" class="container">
      <!--***********************LOGO**********************************-->
     <!--  <div id="logo">
        <img src="images/iiitdm.svg" width="180" height="180" alt="IIITD&M logo" />
      </div> -->
      <!--*************************************************************-->
      
      <!--********************REGISTER FORM*****************************-->
      <div >
        <form id='register' method='POST' action="register.php" accept-charset='UTF-8'>
            <div id="loghead" class="center">Register</div>
            <!-- <input type='hidden' name='submitted' id='submitted' value='1'/> -->
              
              <div id="emailAddress" class="input-field">
                <i class="mdi-action-account-circle prefix"></i>
                <input type="email" name='emailAddress' id='emailAddress' maxlength="50">
                <label for='username' >Email</label>
                <span id='login_username_errorloc' class='error'></span>
              </div>

              <div id="password" class="input-field">
                <i class="mdi-action-lock-outline prefix"></i>
                <input type="password" name='password' id='password' maxlength="50" />
                <label for='password' >Password</label>
                <span id='login_password_errorloc' class='error'></span>
              </div>

              <div id="confirmPassword" class="input-field">
                <i class="mdi-action-https prefix"></i>
                <input type="password" name='confirmPassword' id='confirmPassword' maxlength="50" />
                <label for='confirmPassword' >Confirm Password</label>
                <span id='login_password_errorloc' class='error'></span>
              </div>

              <div name="Discipline" id="disciplineId" class="input-field col s12">Select a discipline<br/>
                  <input class="with-gap" checked value="Computer Engineering" <?php if(isset($discipline)&&$discipline=='Computer Engineering') echo 'checked="checked"'; ?> name="discipline" type="radio" id="coe"  />
                  <label for="coe">Computer Engineering</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <input class="with-gap" value="Electronics" <?php if(isset($discipline)&&$discipline=='Electronics') echo 'checked="checked"'; ?> name="discipline" type="radio" id="electronics"  />
                  <label for="electronics">Electronics</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <input class="with-gap" value="Mechanical" <?php if(isset($discipline)&&$discipline=='Mechanical') echo 'checked="checked"'; ?> name="discipline" type="radio" id="mech"  />
                  <label for="mech">Mechanical Engineering</label>&nbsp;&nbsp;
                  <input class="with-gap" value="Mathematics" <?php if(isset($discipline)&&$discipline=='Mathematics') echo 'checked="checked"'; ?> name="discipline" type="radio" id="maths"  />
                  <label for="maths">Maths</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <input class="with-gap" value="Physics" <?php if(isset($discipline)&&$discipline=='Physics') echo 'checked="checked"'; ?> name="discipline" type="radio" id="physics"  />
                  <label for="physics">Physics</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              </div>

              <div name="ModeOfRegistration" id="modeOfRegistrationId" class="input-field col s12">Choose your Mode of Registration
                  <input class="with-gap" checked value="HTTRA" <?php if(isset($modeOfRegistration)&&$modeOfRegistration=='HTTRA') echo 'checked="checked"'; ?> name="registrationMode" type="radio" id="rhttra"  />
                  <label for="rhttra">Regular HTTRA</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <input class="with-gap" value="NHTTRA" <?php if(isset($modeOfRegistration)&&$modeOfRegistration=='NHTTRA') echo 'checked="checked"'; ?> name="registrationMode" type="radio" id="rnhttra"  />
                  <label for="rnhttra">Regular NHTTRA</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <input class="with-gap" value="Internal" <?php if(isset($modeOfRegistration)&&$modeOfRegistration=='Internal') echo 'checked="checked"'; ?> name="registrationMode" type="radio" id="inter"  />
                  <label for="inter">Internal</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <input class="with-gap" value="External" <?php if(isset($modeOfRegistration)&&$modeOfRegistration=='External') echo 'checked="checked"'; ?> name="registrationMode" type="radio" id="ext"  />
                  <label for="ext">External</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <!-- <select name="modeOfRegistration">
                  <option value="" selected>Choose your Mode of Registration</option>
                  <option value="httra">Regular HTTRA</option>
                  <option value="nhttra">Regular NHTTRA</option>
                  <option value="internal">Internal</option>
                  <option value="external">External</option>
                </select>
                <label>Mode of registration</label> -->
              </div><br/>

              <!--*****************************SUBMIT BUTTON*************************-->
              <div id="submit_button" class="center"><button class="waves-effect waves-light btn-large" type='submit' name='submit' value='Submit' >Submit<i class="mdi-content-send right"></i></button></div>
              <!--*******************************************************************-->

            <div class='short_explanation'><a href='resendConfirmationEmail.php'>Resend Confirmation Mail?</a></div>
        </form>
        <!--*************************************************************-->

      </div>
    </div>
    </body>
  <!--*****************************FOOTER********************************-->
  <div id="index_copyright" class="container center">
  <p>Copyright (c) 2013 <a href="http://www.iiitdm.ac.in">IIITD&M Kancheepuram</a>. All rights reserved. </p>
  </div>
  <!--*******************************************************************-->
</html>

<script type="text/javascript">
$(window, document, undefined).ready(function() {


  $('select').material_select();

  $('input').blur(function() {
    var $this = $(this);
    if ($this.val())
      $this.addClass('used');
    else
      $this.removeClass('used');
  });

  var $ripples = $('.ripples');

  $ripples.on('click.Ripples', function(e) {

    var $this = $(this);
    var $offset = $this.parent().offset();
    var $circle = $this.find('.ripplesCircle');

    var x = e.pageX - $offset.left;
    var y = e.pageY - $offset.top;

    $circle.css({
      top: y + 'px',
      left: x + 'px'
    });

    $this.addClass('is-active');

  });

  $ripples.on('animationend webkitAnimationEnd mozAnimationEnd oanimationend MSAnimationEnd', function(e) {
    $(this).removeClass('is-active');
  });

  $(".dropdown-button").dropdown();

});
</script>
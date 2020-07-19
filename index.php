<?php 
$f3 = require('../../../AboveWebRoot/fatfree-master/lib/base.php');
$f3->set('AUTOLOAD','autoload/;../../../AboveWebRoot/autoload/');   

$db = DatabaseConnection::connect();    
$f3->set('DB', $db);

$f3->set('DEBUG',3);    
$f3->set('UI','ui/'); 

new \DB\SQL\Session($f3->get('DB'));
if (!$f3->exists('SESSION.userName')) $f3->set('SESSION.userName', 'UNSET');
if (!$f3->exists('SESSION.logInMsg')) $f3->set('SESSION.logInMsg', 'Please log in:');
if (!$f3->exists('SESSION.signUpMsg')) $f3->set('SESSION.signUpMsg', 'Please sign up:');
if (!$f3->exists('SESSION.resetMsg')) $f3->set('SESSION.resetMsg', 'Please enter your new password:');

//homepage
$f3->route('GET /',
  function ($f3) {
    $f3->set('html_title','Home');
    $f3->set('content','home.html');
  //  $f3->set('page','home');
    echo Template::instance()->render('layout.html');
  }
);

//about us page
$f3->route('GET /about',
  function($f3) {
   $f3->set('html_title','About');
    $f3->set('content','about.html');
  //  $f3->set('page','about');
    echo template::instance()->render('layout.html');
  }
);

//communities page
$f3->route('GET /community',
  function($f3) {
    $controller = new SimpleController;
    $controller->getCommunity();
   $f3->set('html_title','Community');
    $f3->set('content','community.html');
 //   $f3->set('page','community');
    echo template::instance()->render('layout.html');
  }
);

//communities page
$f3->route('GET /community/@type',
  function($f3) {

  $controller = new SimpleController;
    $controller->getCommunityList($f3->get('PARAMS.type'));

   $f3->set('html_title',$f3->get('PARAMS.type'));
    $f3->set('content','communityList.html');
    echo template::instance()->render('layout.html');
  }
);


//one introduction demo page
$f3->route('GET /publication/@id', //productid
  function($f3) {
    $controller = new SimpleController;
    $controller->getPublication($f3->get('PARAMS.id'));
    $f3->set('html_title','publication');
    $f3->set('content','publication.html');
    echo template::instance()->render('layout.html');
  }
);



//me
$f3->route('GET /login',
  function($f3) {
    $f3->set('html_title','Log in');
    $f3->set('content','login.html');
    $f3->set('SESSION.logInMsg', 'Please log in:');
    echo template::instance()->render('layout.html');
  }
);

$f3->route('POST /login',
  function($f3) {
    $controller = new SimpleController;
    if ($controller->loginUser($f3->get('POST.uname'), $f3->get('POST.password'))) {  
    //success  
      $f3->set('SESSION.userName', $f3->get('POST.uname'));    
      $f3->reroute('/account');
    }else{
      $f3->set('SESSION.logInMsg','Wroung input. Please try again.');
       $f3->set('html_title','Log in');
    $f3->set('content','login.html');
    echo template::instance()->render('layout.html');
    }
   
   }
);


//me
$f3->route('GET /account',
  function($f3) {
    $f3->set('html_title','Log in');
    $f3->set('content','account.html');
    echo template::instance()->render('layout.html');
  }
);

$f3->route('GET /account/@username', //productid
  function($f3) {
    $controller = new SimpleController;
    $controller->getUserPublish($f3->get('PARAMS.username'));
    $f3->set('user',$f3->get('PARAMS.username'));
    $f3->set('html_title',$f3->get('PARAMS.username')."'s Publish");
    $f3->set('content','userPublish.html');
    echo template::instance()->render('layout.html');
  }
);


$f3->route('GET /orderForm',
  function($f3) {
    $f3->set('wantid',$f3->get('GET.id'));
    $f3->set('html_title','Order Form');
    $f3->set('content','orderForm.html');
    echo template::instance()->render('layout.html');
  }
);


$f3->route('POST /orderForm',
  function($f3) {
    $controller = new SimpleController;
    echo $f3->get('POST.pro_want');
    $controller-> setOrder($f3->get('POST.pro_want'),$f3->get('SESSION.userName'),$f3->get('POST.pro_own'));
    $f3->reroute('/applySuccess');
  }
);

$f3->route('GET /applySuccess',
  function($f3) {
    $f3->set('html_title','Apply Success');
    $f3->set('content','applySuccess.html');
    echo template::instance()->render('layout.html');
  }
);

$f3->route('GET /account/resetPassword',
  function($f3) {
    $f3->set('SESSION.resetMsg', 'Please enter your new password:');
    $f3->set('html_title','Reset Password');
    $f3->set('content','resetPassword.html');
    echo template::instance()->render('layout.html');
  }
);

$f3->route('POST /account/resetPassword',
  function($f3) {
    $controller = new SimpleController;
    if(strlen($f3->get('POST.password'))<8 || strlen($f3->get('POST.passwordCon'))<8 ){
      $f3->set('SESSION.resetMsg', 'Password length should at least 8. Please try again:');
    }
    elseif($controller->resetPassword($f3->get('SESSION.userName'), $f3->get('POST.password'), $f3->get('POST.passwordCon'))){
      $f3->set('SESSION.resetMsg', 'Reset successful.');
    }else{
       $f3->set('SESSION.resetMsg', 'Different password. Please try again.');
    }
    $f3->set('html_title','Reset Password');
    $f3->set('content','resetPassword.html');
    echo template::instance()->render('layout.html');
  }
);

//my publish
$f3->route('GET /account/myOrder',
  function($f3) {
    $controller = new SimpleController;
    $controller->getMyOrder($f3->get('SESSION.userName'));
    $f3->set('html_title','My Order');
    $f3->set('content','myOrder.html');
    echo template::instance()->render('layout.html');
  }
);

//my publish
$f3->route('GET /publish',
  function($f3) {
    $controller = new SimpleController;
    $controller->getCommunity();

    if($f3->get('GET.id')){
      $controller->getContent($f3->get('GET.id'));
      //$f3->set('existPub','true');
    }//else{//a new one
      //$f3->set('existPub','false');
    //}
    
    $f3->set('html_title','Publish');
    $f3->set('content','publish.html');
    
    echo template::instance()->render('layout.html');
  }
);

$f3->route('POST /publish',
  function($f3) {
  //  $is = new ImageServer;
  //  $is->upload();

    $formdata = array();      
    $formdata["title"] = $f3->get('POST.title');
    $formdata["abstract"] = $f3->get('POST.abstract');    
    $formdata["story"] = $f3->get('POST.story'); 
    $formdata["community"] = $f3->get('POST.community_name'); 
    $formdata["community1"] = $f3->get('POST.sel_community');
    $formdata["seller"] = $f3->get('SESSION.userName');
    $formdata["email"] = $f3->get('POST.email'); 
    $formdata["phone"] = $f3->get('POST.phone');
    $formdata["id"] = $f3->get('POST.id');

    $controller = new SimpleController;
    $productid = $controller->setPublish($formdata);
   // echo $productid;

    $f3->reroute('/publication/'.$productid);

  }
);

$f3->route('POST /deletePublish',
  function($f3) {
    $controller = new SimpleController;
    $controller-> deletePublish($f3->get('POST.id'));
    $f3->reroute('/account/'.$f3->get('SESSION.userName'));
  }
);


//signup page
$f3->route('GET /signup',
  function($f3) {
    $f3->set('SESSION.signUpMsg', 'Please sign up:');
    $f3->set('html_title','Sign Up');
    $f3->set('content','signup.html');
    $f3->set('page','signup');
    echo template::instance()->render('layout.html');
  }
);

$f3->route('POST /signup',
  function($f3) {
    $formdata = array();      
    $formdata["name"] = $f3->get('POST.uname');
    $formdata["pass"] = $f3->get('POST.password');    
    $formdata["passCon"] = $f3->get('POST.passwordCon'); 

    //check if same password
    if($formdata["pass"]===$formdata["passCon"]){
      $controller = new SimpleController;
      //check password length
      if(strlen($formdata["pass"])<8){
        $f3->set('SESSION.signUpMsg','Password length should at least 8. Please try again:');
      }
      //check unique name
      elseif(!$controller->uniqueName($formdata)){
        $f3->set('SESSION.signUpMsg','User name exists. Please try again:');
      }
      //success
      else{
        $controller->signupUser($formdata);
     //   $f3->set('SESSION.signUpMsg','Success. You can log in now.');
        $f3->reroute('/signupSuccess');
      }
      
    }else{
      $f3->set('SESSION.signUpMsg','Different password. Please try again.');
      
    }
    $f3->set('html_title','Sign Up');
    $f3->set('content','signup.html');
    echo template::instance()->render('layout.html');
    
  }
);

$f3->route('GET /signupSuccess',
  function($f3) {
   // $f3->set('SESSION.signUpMsg', 'Please sign up:');
    $f3->set('html_title','Sign Up Success');
    $f3->set('content','signupSuccess.html');
   // $f3->set('page','signup');
    echo template::instance()->render('layout.html');
  }
);

$f3->route('POST /searchResult',
  function($f3) {

    $controller = new SimpleController;
    $controller->getSearchResult($f3->get('POST.search'));
    
   $f3->set('page','search');
    $f3->set('html_title','Search Result');
  $f3->set('content','searchResult.html');
  echo template::instance()->render('layout.html');


  }
);


$f3->route('POST /logout',
  function($f3) {
    $f3->set('SESSION.userName', 'UNSET');
    $f3->set('SESSION.logInMsg','You have logged out.');
    $f3->reroute('/login');
  }
);

//contact
$f3->route('GET /help',
  function($f3) {
   $f3->set('html_title','Help');
    $f3->set('content','help.html');
    $f3->set('page','help');
    echo template::instance()->render('layout.html');
  }
);



$f3->run();

?>


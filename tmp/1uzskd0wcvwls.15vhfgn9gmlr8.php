<!DOCTYPE html>
<html>
   <head>
      <title><?= $html_title ?></title>
      <meta charset='utf8' />
      <style type="text/css">
        h1 {Color: rgba(49,112,143,1);font-family:Georgia,"Times New Roman",Times,serif;padding:0px 0px 10px 50px;font-size:2.3vw;}
        p {font-size:1.3vw;font-family:didot,times,helvetica,arial, serif;margin:1% 15%;font-weight:bold;Color: #615f5f}
        #colour {border:1px solid #DADADA;Color: #888;height: 30px;width:70%;font-size:1.1vw; margin-top: 1%}
        #name {border:1px solid #DADADA;Color: #888;height: 30px;width:70%;font-size:1.1vw; margin-top: 1%}
        #address {border:1px solid #DADADA;Color: #888;height: 30px;width:70%;font-size:1.1vw; margin-top: 1%}
         #DOB {border:1px solid #DADADA;Color: #888;height: 30px;width:70%;font-size:1.1vw; margin-top: 1%}
      </style>
   </head>
   <body>
      <?php echo $this->render($content,NULL,get_defined_vars(),0); ?>
   </body>
</html>

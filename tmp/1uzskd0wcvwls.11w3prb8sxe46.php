<!-- 
A very simple input form View template:
note that the form method is POST, and the action
is the URL for the route that handles form input.
 -->

<p>This is a simple form</p>

<form id="form1" name="form1" method="post" action="<?= $BASE ?>/simpleform">
<p>  Please enter your name: 
  <input name="name" type="text" placeholder="Enter name" id="name" size="50" /></p>

<p>  Please enter your address: 
  <input name="address" type="text" placeholder="Enter address" id="address" size="50" /></p>

<p>  Please enter your date of birth: 
  <input name="DOB" type="text" placeholder="YYYY/MM/DD" id="DOB" size="50" /></p>

<p>Choose a colour: 
  <select name="colour" id="colour">
    <option value="blue">Blue</option>
    <option value="red" selected="selected">Red</option>
    <option value="green">Green</option>
  </select>
</p>
<p>
  <input type="submit" name="Submit" value="Submit" />
</p>
</form>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<html>
<input type="button" id="btnTest" value="test click" >
<input type="button" id="btnTest1" value="test click" onclick ="funtionclick()" >

</html>
<script type="text/javascript">
$(document).ready(function() {
    
    $("#btnTest").click(function(){
       
       alert("da click");
       
    });
});
function funtionclick(){
    alert("da click");
}
</script> 
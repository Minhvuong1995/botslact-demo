
<script src="/bot/bot/web/assets/f963e8c5/jquery.js"></script>
<h2> Bot Detail </h2>
<?php
$rooturl = Yii::getAlias('@web');
$edit =0;
if(isset($info_bot)){
    $edit =1;
}
?>
<form id="saveform" action="<?php echo $rooturl.'/index.php?r=bot%2Fsavebot'?>" method="POST">
    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
    <input type="hidden" name="id_bot" value="<?php if($edit&&isset($info_bot['id_bot'])) echo $info_bot['id_bot']; ?>" />
<table id='dvLst'>
    <tr>
        <td>Id Bot </td><td><?php if($edit) echo $info_bot['id_bot']; ?></td>
    </tr>
    <tr>
        <td> Channel</td>
        <td>
            <select style="width: 100%" id="group_id" name="group_id"">
            <?php
            foreach($channel as $key => $value){
                
                if(($edit) && $key== $info_bot['group_id']){
                    echo "<option  value=".$key.' selected ="selected" >'.$value.'</option>';
                }
                else{
                    echo "<option  value=".$key.">".$value."</option>";
                }
            }
            ?>
            </select>
        </td>
    </tr>

    <tr>
        <td>Name</td>
        <td><input type="text" name="name" id="name" value="<?php if($edit) echo $info_bot['name']; ?>"></td>
    </tr>
    <tr>
        <td>Content</td>
        <td><textarea name="content" id="content"rows="4" style="width: 100%;" ><?php if($edit) echo $info_bot['content']; ?> </textarea></td>
    </tr>
    <tr>
        <td>Time send</td>
        <td><input type="text" name="time_send" id="time_send" value="<?php if($edit) echo $info_bot['time_send']; ?>"></td>
    </tr>
    <tr>
        <td>Date Send</td>
        <td><input type="text" name="date_send" id="date_send" value="<?php if($edit) echo $info_bot['date_send']; ?>"></td>
    </tr>
    <tr>
        <td>Month Send</td>
        <td><input type="text" name="month_send" id="month_send" value="<?php if($edit) echo $info_bot['month_send']; ?>"></td>
    </tr>
    <tr>
        <td>Days of Week</td>
        <td><input type="text" name="date_of_week" id="date_of_week" value="<?php if($edit) echo $info_bot['date_of_week']; ?>"></td>
    </tr>
</table>
<br>
<div style = "float:right;">
    <input type="submit" value="Save">
    <input type="button" id="btnCancel" value="Cancel">  
</div>
</form>
<script type="text/javascript">
$(document).ready(function() {
    $('form').bind("keypress", function(e) {
        if (e.keyCode == 13) {               
        e.preventDefault();
        return false;
        }
    });
    $("#btnCancel").click(function(){
        window.location="<?php echo $rooturl.'/index.php?r=bot%2F'?>"
    });

});
</script>
<?php $rooturl = Yii::getAlias('@web'); ?>
<script src="<?php echo $rooturl ?>/assets/f963e8c5/jquery.js"></script>
<h2> Local Channel </h2>
<br>

<form id="saveform" action="<?php echo $rooturl.'/index.php?r=bot%2Fchannel'?>" method="POST">
<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
<p style="float:left"> Name :  </p>
<input type="text" style="float:left" name="name" id="name">
<p style="float:left"> ID Channel : </p>
<input type="text" style="float:left" name="id_slack_channel" id="id_slack_channel">
<input id="btnAdd" class="button1" style="float:left" type="button" value="Add">
</form>
<table id = "dvLst">
    <tr>
        <th style="width: 50px;" >ID</th>
        <th style="width: 70%;">Name</th>
        <th style="width: 300px;">Channel ID</th>
        <th style="width: 70px;">Action</th>
    </tr>
    <?php
        foreach($channel as $value){
            echo '<tr id ="tr'.$value['id'].'" > <td>'.$value['id'].' </td> <td> '.$value['name'].' </td> <td> '.$value['id_slack_channel'].' </td> <td>   <input type="button" value="Delete" class="button1" onclick ="deletechannel('.$value['id'] .')"> </td> </tr>';
        }
    ?>
    
</table>
<br>
<input type="button" id="btnCancel" style="float:right;margin-right: 20px;"   class="button1" value="Close">

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
    $("#btnAdd").click(function(){
        $('#saveform').submit();
    });

});

function deletechannel(id){
    if (confirm('Are you sure to delete channel id:'+id)) {
        // set csrfToken page
        $.ajaxSetup({
            data: <?= \yii\helpers\Json::encode([
                \yii::$app->request->csrfParam => \yii::$app->request->csrfToken,
            ]) ?>
        });
        $.ajax({
            url: "<?php echo $rooturl.'/index.php?r=bot%2Fdel' ?>",
            type: 'Post',
            data: {
            id: id
            },
            success: function(data) {
                if(data ==1){
                    alert('Delete successfully');
                    $("#tr"+id).hide(1000);
                }
                else{
                    alert('Delete Error');
                }
            
            },
            error(){
                alert('Delete Error, connect false');
            }

        });
    }else
    {
      //cancel click
    }
}

</script>



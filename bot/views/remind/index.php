<!DOCTYPE html>
<html>
<?php $root_url = Yii::getAlias('@web'); ?>
<script src="<?php echo $root_url ?>/assets/f963e8c5/jquery.js"></script>
<h2> Remind Manage </h2>
<tr class="link-add"> <a class="link-add" href="<?php echo $root_url . '/index.php?r=remind%2Fedit' ?>">ADD REMIND CHANNEL</a></tr>
<table id='dvLst'>
    <tr>
        <th style="width: 5%;">Id</th>
        <th style="width: 35%;">Name</th>
        <th style="width: 35%;">Id Channel</th>
        <th style="width: 20%;">Action</th>
    </tr>
    <?php foreach ($info_remind as $remind) { ?>
        <tr id="remind<?php echo $remind['id']; ?>">
            <td><?php echo $remind['id'];  ?></td>
            <td><?php echo $remind['name'];  ?></td>
            <td><?php echo $remind['id_channel'];  ?></td>
            <td>
                <input style="border-bottom: inset; font-size: initial;" class="button1" type="button" value="Edit" onclick="edit(<?php echo $remind['id']; ?>)">
                <input style="border-bottom: inset; font-size: initial;" class="button1" type="button" value="Delete" onclick="del(<?php echo $remind['id']; ?>)">
            </td>
        </tr>
    <?php } ?>
</table>

</html>
<script type="text/javascript">
    $(document).ready(function() {


    });

    function edit(id) {
        window.location = "<?php echo $root_url . '/index.php?r=remind%2Fedit&id=' ?>" + id;
    }
    //delete bot from channel
    function del(id) {
        if (confirm('Are you sure to delete remind id:' + id)) {
            // set csrfToken page
            $.ajaxSetup({
                data: <?= \yii\helpers\Json::encode([
                            \yii::$app->request->csrfParam => \yii::$app->request->csrfToken,
                        ]) ?>
            });
            $.ajax({
                url: "<?php echo $root_url . '/index.php?r=remind%2Fdelete' ?>",
                type: 'Post',
                data: {
                    id: id
                },
                success: function(data) {
                    if (data == 1) {
                        alert('Delete successfully');
                        $("#remind" + id).hide(1000);
                    } else {
                        alert('Delete Error');
                    }

                },
                error() {
                    alert('Delete Error, connect false');
                }
            });
        } else {
            //cancel click
        }
    }
</script>
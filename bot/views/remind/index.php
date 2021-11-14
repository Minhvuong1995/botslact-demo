<!DOCTYPE html>
<html>
<?php $root_url = Yii::getAlias('@web'); ?>
<script src="<?php echo $root_url ?>/assets/f963e8c5/jquery.js"></script>
<h2> Remind Manage </h2>
<tr class="link-add"> <a class="link-add" href="<?php echo $root_url.'/index.php?r=remind%2Fedit'?>">ADD REMIND CHANNEL</a></tr>
<table id='dvLst'>
    <tr>
        <th style="width: 5%;">Id</th>
        <th style="width: 35%;">Name</th>
        <th style="width: 35%;">Id Channel</th>
        <th style="width: 20%;">Action</th>
    </tr>
    <?php foreach ($info_remind as $remind) { ?>
        <tr>
            <td><?php echo $remind['id'];  ?></td>
            <td><?php echo $remind['name'];  ?></td>
            <td><?php echo $remind['id_channel'];  ?></td>
            <td>
                <input style="border-bottom: inset; font-size: initial;" class="button1" type="button" value="Edit" onclick="edit(<?php echo $remind['id']; ?>)">
                <input style="border-bottom: inset; font-size: initial;" class="button1" type="button" value="Delete" onclick="delete(<?php echo $remind['id']; ?>)">
            </td>
        </tr>
    <?php } ?>
</table>

</html>
<script type="text/javascript">
    $(document).ready(function() {


    });
</script>
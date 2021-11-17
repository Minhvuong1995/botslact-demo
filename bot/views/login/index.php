<?php $root_url = Yii::getAlias('@web'); ?>
<script src="<?php echo $root_url ?>/assets/f963e8c5/jquery.js"></script>
<?php
$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
$session = Yii::$app->session;
?>
<a href="<?php echo $root_url . '/index.php?r=login%2Fauth&authclient=google'; ?>">
    Login with google
</a>
<script type="text/javascript">
    $(document).ready(function() {
        <?php if (isset($session['login_error'])) {
            echo ("alert(' " . $session['login_error'] . "');");
        } ?>
    });
</script>
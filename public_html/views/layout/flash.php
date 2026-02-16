<?php
$success = flash('success');
$error = flash('error');
$info = flash('info');
?>
<?php if ($success): ?>
    <div id="flash-success" class="alert alert-success mb-4 flex items-center justify-between">
        <span><?= escape($success) ?></span>
        <button type="button" data-dismiss="flash-success" class="btn btn-light">x</button>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div id="flash-error" class="alert alert-error mb-4 flex items-center justify-between">
        <span><?= escape($error) ?></span>
        <button type="button" data-dismiss="flash-error" class="btn btn-light">x</button>
    </div>
<?php endif; ?>
<?php if ($info): ?>
    <div id="flash-info" class="alert alert-info mb-4 flex items-center justify-between">
        <span><?= escape($info) ?></span>
        <button type="button" data-dismiss="flash-info" class="btn btn-light">x</button>
    </div>
<?php endif; ?>

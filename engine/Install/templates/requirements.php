<?= t('Before start system must check your server for requirements. <br/>Just look at the table below and follow the instructions.') ?>
<?
$success = TRUE;
?>
<table id="requirements" class="bordered-table zebra-striped">
    <thead>
    <th>#</th>
    <th><?= t('Name') ?></th>
    <th><?= t('Current') ?></th>
    <th><?= t('Required') ?></th>
    <th><?= t('Test') ?></th>
</thead>
<tbody>
    <?
    $success = FALSE;
    $php_version = phpversion();
    $passed = version_compare($php_version, '5.2.6', '>=');
    $success = $passed ? TRUE : FALSE;
    ?>
    <tr class="<?= $passed ? 'success' : 'failure' ?>">
        <td>0.</td>
        <td><?= t('PHP Version') ?></td><td>
            <?= $php_version ?>
        </td><td>
            5.2.6
        </td>
        <td >
            <span class="label <?= $passed ? 'success' : 'important' ?>"><?= t($passed ? 'Passed' : 'Error') ?></span>
        </td>
    </tr>

    <?
    $passed = function_exists('spl_autoload_register');
    $success = $passed ? TRUE : FALSE;
    ?>
    <tr class="<?= $passed ? 'success' : 'failure' ?>">
        <td>1.</td>
        <td colspan="3"><?= t('SPL Library') ?></td>
        <td >
            <span class="label <?= $passed ? 'success' : 'important' ?>"><?= t($passed ? 'Passed' : 'Error') ?></span>
        </td>
    </tr>
    <?
    $passed = class_exists('ReflectionClass');
    $success = $passed ? TRUE : FALSE;
    ?>
    <tr class="<?= $passed ? 'success' : 'failure' ?>">
        <td>2.</td>
        <td colspan="3"><?= t('Reflections') ?></td>
        <td >
            <span class="label <?= $passed ? 'success' : 'important' ?>"><?= t($passed ? 'Passed' : 'Error') ?></span>
        </td>
    </tr>
    <?
    $passed = function_exists('filter_list');
    $success = $passed ? TRUE : FALSE;
    ?>
    <tr class="<?= $passed ? 'success' : 'failure' ?>">
        <td>3.</td>
        <td colspan="3"><?= t('Filters') ?></td>
        <td >
            <span class="label <?= $passed ? 'success' : 'important' ?>"><?= t($passed ? 'Passed' : 'Error') ?></span>
        </td>
    </tr>
    <?
    $passed = extension_loaded('iconv');
    $success = $passed ? TRUE : FALSE;
    ?>
    <tr class="<?= $passed ? 'success' : 'failure' ?>">
        <td>4.</td>
        <td colspan="3"><?= t('Iconv extension') ?></td>
        <td >
            <span class="label <?= $passed ? 'success' : 'important' ?>"><?= t($passed ? 'Passed' : 'Error') ?></span>
        </td>
    </tr>
    
    <?
    if (isset($_SERVER['REQUEST_URI']) OR isset($_SERVER['PHP_SELF']) OR isset($_SERVER['PATH_INFO'])) {
        $passed = TRUE;
    }
    else {
        $passed = FALSE;
    } 
    $success = $passed ? TRUE : FALSE;
    ?>
    <tr class="<?= $passed ? 'success' : 'failure' ?>">
        <td>5.</td>
        <td colspan="3"><?= t('URL rewrite') ?></td>
        <td >
            <span class="label <?= $passed ? 'success' : 'important' ?>"><?= t($passed ? 'Passed' : 'Error') ?></span>
        </td>
    </tr>
</tbody>
</table>
<? if ($success): ?>
    <p align="center">
        <a href="<?= l('install/site') ?>" class="button"><?= t('Continue') ?></a>
    </p>
    <?else:?>
    <?=error(t('Some of requirements are not satisfied.'))?>
<? endif; ?>
<?php
require_once 'lib/loader.php';
?>
<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
    </head>
    <body>
        <div class="container">
        <h1><?php echo $config->get('strings.page_title'); ?></h1>
        <p><?php echo $config->get('strings.page_description'); ?></p>
        <table class="table table-striped">
            <thead>
                <tr>                
                    <th><?php echo $config->get('strings.column_name'); ?></th>
                    <th><?php echo $config->get('strings.frontend_url'); ?></th>
                    <th><?php echo $config->get('strings.backend_url'); ?></th>
                    <th><?php echo $config->get('strings.column_username'); ?></th>
                    <th><?php echo $config->get('strings.column_password'); ?></th>
                    <th><?php echo $config->get('strings.column_status'); ?></th>
                </tr>                
            </thead>
            <tbody>
                <?php foreach($app->getSites() as $site) : ?>
                <tr>
                    <td>
                        <?php echo $site['name']; ?>
                    </td>
                    <td>
                        <a href="/<?php echo $site['folder']; ?>"><?php echo $config->get('strings.frontend_url'); ?></a>
                    </td>
                    <td>
                        <a href="/<?php echo $site['folder']; ?>/administrator"><?php echo $config->get('strings.backend_url'); ?></a>
                    </td>
                    <td>
                        <?php echo $site['username']; ?>
                    </td>
                    <td>
                        <?php if($app->isAuthorized()) : ?>
                        <?php echo $site['password']; ?>
                        <?php else: ?>
                        *******
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo $config->get('strings.status_'.$site['status']); ?>
                    </td>
                    <td>
                        <?php foreach($site['actions'] as $action): ?>
                            <?php $actionUrl = 'index.php?task='.$action.'&site='.$site['number']; ?>
                            <?php $actionLabel = $config->get('strings.action_'.$action); ?>
                            <a href="<?php echo $actionUrl; ?>"><?php echo $actionLabel; ?></a>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </body>
</html>

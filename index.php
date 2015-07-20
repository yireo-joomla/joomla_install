<?php
require_once 'lib/loader.php';
?>
<html>
    <head>
	    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
    </head>
    <title><?php echo $config->get('strings.page_title'); ?></title>
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
	                <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($app->getSites() as $site) : ?>
                <tr>
                    <td>
                        <?php echo $site['name']; ?>
                    </td>
                    <td>
                        <a href="<?php echo $rootUrl; ?><?php echo $site['folder']; ?>"><?php echo $config->get('strings.frontend_url'); ?></a>
                    </td>
                    <td>
                        <a href="<?php echo $rootUrl; ?><?php echo $site['folder']; ?>/administrator"><?php echo $config->get('strings.backend_url'); ?></a>
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
        <div class="container">
	        <h3>Normal (pull request is mergable and has testinstructions)</h3>
	        <div>
		        Hello @[username] (usting the "real github name")<br />
		        <br />
		        Thank you for your contribution.<br />
		        <br />
		        The last comment here was on [date]. So the question is, Is this issue/pull request still valid? <br />
		        If no reply is received within 4 weeks we will close this issue.<br />
		        <br />
		        Thanks for understanding!
	        </div>

	        <h3>No testinstructions</h3>
	        <div>
		        Hello @[username] (usting the "real github name")<br />
		        <br />
		        Thank you for your contribution.<br />
		        <br />
		        The last comment here was on [date]. So the question is, Is this issue/pull request still valid?<br />
		        if so please provide clear test instructions to be able to test / reproduce this issue.<br />
		        If no reply is received within 4 weeks we will close this issue.<br />
		        <br />
		        Thanks for understanding!
	        </div>

	        <h3>Merge conflicts</h3>
	        <div>
		        Hello @[username] (usting the "real github name")<br />
		        <br />
		        Thank you for your contribution.<br />
		        <br />
		        The last comment here was on [date]. So the question is, Is this issue/pull request still valid?<br />
		        If the issue is still valid can you please rebase to the current staging or re-do the PR so we are able to merge.<br />
		        If no reply is received within 4 weeks we will close this issue.<br />
		        <br />
		        Thanks for understanding!
	        </div>
        </div>

        <hr/>
        <a href="https://github.com/yireo/joomla_install">https://github.com/yireo/joomla_install</a>
    </body>
</html>

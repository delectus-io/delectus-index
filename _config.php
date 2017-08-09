<?php

global $project, $theme;
global $databaseConfig;

require __DIR__ . '/../framework/conf/ConfigureFromEnv.php';

// Set the site locale
i18n::set_locale('en_NZ');
\Config::inst()->update(
	'SSViewer', 'theme', $theme ?: 'frontend'
);

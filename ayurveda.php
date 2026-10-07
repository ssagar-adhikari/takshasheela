<?php

require __DIR__.'/includes/site.php';
require_once __DIR__.'/includes/about-content.php';

$content = about_content('about-ayurveda');
require __DIR__.'/includes/about-view.php';

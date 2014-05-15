twitterCSVconverter
===================

Convert CSV file to JSON file of twitter

## Quick Start

    <?php
	require_once('./twitterCSVconverter.php');
	mb_internal_encoding('UTF-8');
	ini_set('memory_limit','-1');
	ini_set('max_execution_time',0);
	new twitterCSVconverter('./json/', './twitter.csv', 'KEY,ID,ScreenName,UserName,Body,Date,URL,Source');


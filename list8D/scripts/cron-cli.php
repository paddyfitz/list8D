<?php
	
	// Initialize the application path and autoloading
	echo "Initialising...\n";
	defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
	set_include_path(implode(PATH_SEPARATOR, array(
												   APPLICATION_PATH . '/../library',
												   APPLICATION_PATH . '/models',
												   get_include_path(),
							 )));
	
	require_once 'Zend/Loader/Autoloader.php';
	require_once 'CronParser.php';
	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->registerNamespace("List8D");
	$autoloader->registerNamespace("LegacyModel_");
	$resourceLoader = new Zend_Application_Module_Autoloader(array(
																   'namespace' => 'List8D_',
																   'basePath'  => dirname(__FILE__).'/../application',
																   ));		
																   
	//! Enviroment
 	define('APPLICATION_ENV','staging');
 	
	// Initialize Zend_Application
	$application = new Zend_Application(
										APPLICATION_ENV,
										APPLICATION_PATH . '/configs/application.ini'
	);
	
	echo "Retrieving database...\n";
	
	// Initialize and retrieve DB resource
	$bootstrap = $application->getBootstrap();
	$bootstrap->bootstrap('db');
    Zend_Registry::set('dbResource', $bootstrap->getPluginResource('db'));
    
	$db = $bootstrap->getPluginResource('db')->getDbAdapter();
	
	//do we need to load a file?
	if($_SERVER["argv"][1] == "add" || $_SERVER["argv"][1] == "edit" || $_SERVER["argv"][1] == "delete"){
		
		$action = $_SERVER["argv"][1];
		
		echo "Action: ".$action."...\n";
		echo "Reading file...\n";

		/*
		 * format of input file must be:
		 * array(
				"script" => "filename.php",
				"minute" => "*",
				"hour" => "*",
				"day_of_month" => "*",
				"month" => "*",
				"day_of_week" => "*",
				),
		 */
		$process_file = APPLICATION_PATH . '/../data/'.$_SERVER["argv"][2];

		$fh = fopen($process_file, 'rb');
		$process_string = fread($fh, filesize($process_file));
		fclose($fh);

		$proc_setup = "\$cron_array = array(";
		$array_end = ");";

		eval($proc_setup.$process_string.$array_end);

		echo "File contents:\n";
		var_dump($cron_array);
		
		if($action == "add"){
			echo "Retrieving current cron entries...\n";

			foreach($cron_array as $cron){
				$results = $db->query("SELECT * from cron");
				echo "results obtained...\n";
				$duplicate = false;
				echo "Looking through existing CRON entries to find ".$cron["script"]."...\n";
				$count = 1;
				foreach($results as $row){
					echo "Looking at entry number ".$count." with entry ".$row['script']."...\n";
					$count++;
					if($cron["script"] == $row['script']){
						//we already have an entry, don't insert
						echo "Existing entry found for ".$cron["script"].", skipping this item...\n";
						$duplicate = true;		
						echo "Setting \$duplicate to true...\n";
					}
				}
				if($duplicate){
					echo "Current state of \$duplicate is: true...\n";
				}
				else{
					echo "Current state of \$duplicate is not true...\n";
				}

				if(!$duplicate){
					echo "Inserting entry for ".$cron["script"]."...\n";

					$insert = $db->insert("cron", $cron);

					echo "Entry insterted...\n";
				}
				else{
					echo "No insert occurred...\n";
				}
			}
		}
		else if($action == "edit"){
			foreach($cron_array as $cron){
				echo "Editing entry...\n";
				$where = "script = '".$cron["script"]."'";
				$delete = $db->update("cron", $cron, $where);
				echo "Entry updated...\n";
			}
			
		}
		else if($action == "delete"){
			foreach($cron_array as $cron){
				echo "Deleting entry...\n";
				$where = "script = '".$cron["script"]."'";
				$delete = $db->delete("cron", $where);
				echo "Entry deleted...\n";
			}
		}
		
	} 
	else{
  	
		echo "Reading cron table...\n";
	
		//first get the manual ones
		$results = $db->query("SELECT * from cron");
		echo "Results obtained...\n";
	
		foreach($results as $row){
			//we need to check the cron frequency, and ascertain whether to run...
			$job_failed = false;
			$entry_time = array(
								"minutes" => $row['minute'],
								"hours" => $row['hour'],
								"mday" => $row['day_of_month'],
								"mon" => $row['month'],
								"wday" => $row['day_of_week'],
								"last_run" => $row['last_run'],
								"finished" => $row['finished'],
								"fail_message" => $row['fail_message'],
								);
		
			echo "Data from row:\n";
			var_dump($entry_time);
		
			$current_time = getDate();
		
			$needs_to_run = false;
		
			$cron_string = $row['minute']." ".$row['hour']." ".$row['day_of_month']." ".$row['month']." ".$row['day_of_week'];
		
			$cron_parser = new CronParser($cron_string);
		
			//getLastRan method - returns array (0=minute, 1=hour, 2=dayOfMonth, 3=month, 4=week, 5=year)
			$lastRan = $cron_parser->getLastRan();
			$cron_ran = mktime ( $lastRan[1] ,$lastRan[0],0 , $lastRan[3] ,$lastRan[2], $lastRan[5]);
		
			echo "Job should have last run at: ".strftime("%Y-%m-%d %H:%M",$cron_ran)."...\n";
			echo "The current time is: ".strftime("%Y-%m-%d %H:%M",time())."...\n";
		
			//does the last run entry in the database match?
			$last_db_run = "";
			if($row["last_run"]){
				$last_db_run = strtotime($row["last_run"]);
				echo "Job last ran at: ".strftime("%Y-%m-%d %H:%M",$last_db_run)."...\n";
			}
		
			//if it's never run, and the current time is greater than when it should have run
			if($last_db_run == "" && time() > $cron_ran){
				echo "Job has never run, current time calculated to be after when it should have last run. Running job...\n";
				$needs_to_run = true;
			}
			//if it has run, and the last time it ran is before when it should have last run...
			else if($last_db_run != "" && $last_db_run < $cron_ran){
				if($row["finished"] == 1){
					echo "Job last ran before its calculated last run time, and finished successfully. Job is due. Running job...\n";
					$needs_to_run = true;
				}
				else{
					echo "Job failed its last run...\n";
				}
			}
		
			if($needs_to_run){
			
				echo "Updating last run time and setting ...\n";
			
				$update_last_ran = array(
									"last_run" => $current_time['year']."-".$current_time['mon']."-".$current_time['mday']." ".$current_time['hours'].":".$current_time['minutes'],
									"finished" => false,
									);
			
				echo "Setting last run time: ".$update_last_ran['last_run']."\n";
				$where = "script = '".$row["script"]."'";
				$insert = $db->update("cron", $update_last_ran, $where);
				try{
					exec("php ".$row['script']);
					$update_finished = array("finished" => true);
					echo "Job finished, updating finished flag...\n";
					$insert = $db->update("cron", $update_finished, $where);
				}
				catch(Exception $e){
					echo "Something went wrong, setting message...\n";
					$update_message = array("fail_message" => $e->getMessage());
					$insert = $db->update("cron", $update_message, $where);
				}
			}
			else if(!$needs_to_run && $job_failed == false){
				echo "Job does not need to run yet...\n";
			}
			else if(!$needs_to_run && $job_failed == true){
				echo "Job failed last time...\n";
			}
		}
	}
	
?>
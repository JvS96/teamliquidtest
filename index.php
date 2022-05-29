<?php

/**
 * Please write robust and secure code, as if this was a website on the internet.
 * We will use different test CSV files in evaluating the code. Treat the supplied CSV file and
 * parameters as user input. We have set up a MySQL server to use for this project, the database
 * layout can be found in the SQL file and the relevant data to access said server is set in
 * the DATABASECONFIG constant. You are free to move code around as long as the functionality
 * correctly works in the end. The target php version is php8.x.
 */
class CSVHandler {

	private $fileName;

//	private const DATABASECONFIG = [
//		'server' => '188.226.152.164',
//		'port' => 33306,
//		'user' => 'teamliquid',
//		'password' => 'teamliquidtest',
//		'name' => 'teamliquid',
//	];
    //My local connection for test
    private const DATABASECONFIG = [
        'server' => 'localhost',
        'port' => 3306,
        'user' => 'root',
        'password' => '',
        'name' => 'teamliquid'
    ];

    protected function db_connection(){
        $server = CSVHandler::DATABASECONFIG['server'];
        $user = CSVHandler::DATABASECONFIG['user'];
        $password = CSVHandler::DATABASECONFIG['password'];
        $database = CSVHandler::DATABASECONFIG['name'];
        $port = CSVHandler::DATABASECONFIG['port'];

        $db_connection = new mysqli("$server","$user","$password","$database", "$port");
        if(!isset($db_connection)){
            echo "Connection to database interrupted";
        }else{
            return $db_connection;
        }
    }

    private function insert_data($get_name, $get_title){
        $db_conn = $this->db_connection();
        $insert_query = $db_conn->query("
          INSERT INTO `testtable` (`thing_name`, `thing_title`)
          VALUES ('$get_name','$get_title')
        ");
        return $insert_query;
    }

    private function display_data(){
        $db_conn = $this->db_connection();
        $display_query = $db_conn->query("SELECT `thing_id`, `thing_name`, `thing_title` FROM `testtable` LIMIT 50");
        return $display_query;
    }

	/**
	 * Load the file
	 */
	public function __construct( $file ) {
		$this->fileName = 'A:/laragon/www/teamliquidtest/' . $_REQUEST["file"];
	}

	/**
	 * Output the file into a textarea
	 * Use the data to produce a html table
	 */
	public function show() {
        //Show data from CSV file on a table input data
        $csv_file = fopen($this->fileName, 'r');
        $csv_arr = fgetcsv($csv_file);
        $encode_csv_data = json_encode($csv_arr);
        $remove_tags_from_data = str_replace(['"' , "[" , "]"] , "", $encode_csv_data);
        $get_file_data = explode(";",$remove_tags_from_data);
        $html = '<form action="" method="post">';
        $html .= '<table>';
            foreach ($get_file_data AS $display_data){
                $html .= '<th>' . $display_data . '</th>';
            }
            $html .= '<body>';
                while($csv_file_line = fgetcsv($csv_file)){
                    $encode_table_data = json_encode($csv_file_line);
                    $remove_tags_from_tabel_data = str_replace(['"' , "[" , "]"], "", $encode_table_data);
                    $get_table_data_arr = explode(";",$remove_tags_from_tabel_data);
                    global $pass_data;
                    $html .= '<tr>';
                        foreach ($get_table_data_arr AS $table_data){
                            $html .= '<td><textarea class="form-control" id="output" name="myTextarea" readonly="">'.$table_data.'</textarea></td>';
                        }
                    $pass_data[] = $get_table_data_arr;
                    $html .= '</tr>';
                }
            $html .= '</body>';
        $html .= '</table>';
        $html .= '<input class="btn btn-success" type="submit" name="import" value="Import">';
        $html .= '</form>';
        return $html;
	}

	/**
	 * Clear all lines in the table and import data into a SQL database
	 */
	public function import() {
        //Import data to DB (Insert into db)
        if(isset($_POST['import'])){
            global $pass_data;
            foreach ($pass_data AS $key => $pass_csv_data){
                //Here we get data to insert
                $get_name = $pass_csv_data[1];
                $get_title = $pass_csv_data[2];

                $insert_csv_data = $this->insert_data("$get_name", "$get_title");
            }
            if(isset($insert_csv_data)){
                $call_db_data = $this->makeTableFromDB();
                echo $call_db_data;
                echo '<script language="javascript">
                        alert("Import successfully saved")
                      </script>';
            }
        }else{
            $call_db_data = $this->show();
            echo $call_db_data;
        }
	}

	/**
	 * Read data from SQL database and output as html table again
	 */
	public function makeTableFromDB() {
        $result = $this->display_data();
        $html = '<table>';
            $html .= '<th>ID</th>';
            $html .= '<th>Name</th>';
            $html .= '<th>Title</th>';
            $html .= '<body>';
                foreach ($result as $get_items) {
                    $html .= '<tr>';
                    foreach ($get_items as $display_items) {
                        $html .= '<td><textarea class="form-control" readonly="">'.$display_items.'</textarea></td>';
                    }
                    $html .= '<tr>';
                }
            $html .= '</body>';
        $html .= '</table>';
        $html .= '<a href="index.php" class="btn btn-danger">Back</a>';
        return $html;
	}
}
?><!DOCTYPE html>
<html>
	<head>
        <title>CSV Handler</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    </head>
	<body style="margin: 2% 0px 0px 2%;">
		<?php

		if ( isset( $_GET[ 'file' ] ) ) {
			$csvHandler = new CSVHandler( $_GET[ 'file' ] );
			$csvHandler->show();
			$csvHandler->import();
			$csvHandler->makeTableFromDB();
		} else {
			echo '<ul>';
			echo '<li><a href="?file=test.csv">test.csv</a></li>';
			echo '<li><a href="?file=test2.csv">test2.csv</a></li>';
			echo '</ul>';
		}
		?>
	</body>
</html>
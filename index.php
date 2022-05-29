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

    private function db_connection(){
        $server = CSVHandler::DATABASECONFIG['server'];
        $user = CSVHandler::DATABASECONFIG['user'];
        $password = CSVHandler::DATABASECONFIG['password'];
        $database = CSVHandler::DATABASECONFIG['name'];
        $port = CSVHandler::DATABASECONFIG['port'];

        $db_connection = new mysqli("$server","$user","$password","$database", "$port");
        return $db_connection;
    }

    private function insert_data(){

    }

    private function display_data(){
        $get_display_data = $this->db_connection();
        $display_query = $get_display_data->query("SELECT * FROM `teamliquid`.`testtable`");
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
                    $html .= '<tr>';
                        foreach ($get_table_data_arr AS $table_data){
                            $html .= '<td><textarea>'.$table_data.'</textarea></td>';
                        }
                    $html .= '</tr>';
                }
            $html .= '</body>';
        $html .= '</table>';
        $html .= '<input type="submit" name="import" value="Import" onclick="import()"/>';
        $html .= '</form>';

        echo $html;
	}

	/**
	 * Clear all lines in the table and import data into a SQL database
	 */
	public function import() {
        //Import data to DB (Insert into db)

	}

	/**
	 * Read data from SQL database and output as html table again
	 */
	public function makeTableFromDB() {
        //Once data is imported to db, display all data from DB?
        $result = $this->display_data();
        $html = '<table>';
            foreach ($result AS $display_db_headers){
                foreach ($display_db_headers as $keys => $display_db_headers_arr) {
                    $html .= '<th>' . $keys . '</th>';
                }
            }
//        $html .= '<th>id</th>';
//        $html .= '<th>author</th>';
//        $html .= '<th>title</th>';
            $html .= '<body>';
                $html .= '<tr>';
                    foreach ($result as $get_items) {
                        foreach ($get_items as $display_items) {
                            $html .= '<td><textarea>'.$display_items.'</textarea></td>';
                        }
                    }
                $html .= '<tr>';
            $html .= '</body>';
        $html .= '</table>';
        echo $html;
	}

}
?><!DOCTYPE html>
<html>
	<head><title>CSV Handler</title></head>
	<body>
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
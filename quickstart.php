<html>
<body>
<?php
require_once 'F:/wamp64/www/google-api-php-client-2.2.2/vendor/autoload.php';
putenv('GOOGLE_APPLICATION_CREDENTIALS=client_secret_691169095140-5o7sjamoj25uvih4hfvark543g8uen8f.apps.googleusercontent.com (1).json');
define('SCOPES', implode('AIzaSyDtXiU5Hf6PE2thSUVX647DvBu60Q3b2IQ', array(Google_Service_Sheets::SPREADSHEETS)));
 
$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->setScopes(SCOPES);
$service = new Google_Service_Sheets($client);

//database connectivity
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sheet_api";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT id, name, phone FROM sheet_table";
$result = $conn->query($sql);
$content="";

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $content=$content."<br> id: ". $row["id"]. " - Name: ". $row["name"]. " phone" . $row["phone"] . "<br>";
		//$content= $content."\n".$row["id"]." ".$row["Name"]." ".$row["phone"];
		
    }
} else {
    echo "0 results";
}
echo "$content\n\n\n";

//echo gettype('$content');
$conn->close();


/* using online sql query
$dbLink = mysqli_connect("localhost", "root", "", "sheet_api");
if ( !$dbLink )
  die("Couldn't connect to database");
 
$stmt = $dbLink->prepare("SELECT id, name, phone FROM sheet_table");
//$stmt->bind_param("s", <Some variable>);
$stmt->execute();
 
$data = array();
$rowCount = 0;
$stmt->bind_result($id,$name, $phone);
while ( $stmt->fetch() )
{
  ++$rowCount;
  $row = array(is_null($id) ? "" : $id, 
               is_null($name) ? "" : $name,
			   is_null($phone) ? "" : $phone);
  array_push($data, $row);
}
*/

$spreadsheetId = "1_eDuHbL3mWKUERBBolYdsVjaM8c1TR29SVTq5O2XzYw";
$optParams = ['valueInputOption' => 'RAW'];
$requestBody = new Google_Service_Sheets_ValueRange();
$requestBody->setMajorDimension("ROWS");
$requestBody->setValues($content);
//$requestBody->setValues($data);
$range = "SheetName!A2:B";
$response = $service->spreadsheets_values->update($spreadsheetId, $range, $requestBody, $optParams);
 
// Clear out old data
$range = sprintf("SheetName!A%d:B", 20);
$service->spreadsheets_values->clear($spreadsheetId, $range, new Google_Service_Sheets_ClearValuesRequest());

$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();
 
if (count($values) == 0) {
  print "No data found.\n";
} else {
  foreach ($values as $row) {
    printf("%s\n", $row[0]);
  }
}

?>
</html>
</body>
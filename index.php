<?php
class dbConfig
{
	private $dbDriver = "mysql"; 	//databaseOpo?
	private $host = "localhost"; 	//namaHost
	private $username = "root"; 	//username
	private $password = ""; 		//password
	private $database = "test";		//namaDatabaseNya

	protected $connection;

	public function __construct(){
		try{
			$this->connection = new PDO($this->dbDriver.':host='.$this->host.';dbname='.$this->database,$this->username,$this->password);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e){
			die("Koneksi error: " . $e->getMessage());
		}
	}
}

class paging extends dbConfig
{
	public $baris;

	public function getrow($page)
	{
		try{
			$mulai = ($page-1)*$this->baris+1;
			$query = "SELECT * FROM users LIMIT $mulai, $this->baris";
			$result = $this->connection->prepare($query);
			$result->execute();
			$rows = array();
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				$rows[] = $row;
			}
		}
		catch (PDOException $e){
			die("Koneksi error: " . $e->getMessage());
		}
		return $rows;
	}
	public function page(){
		try{
			$query = "SELECT * FROM users";
			$result = $this->connection->prepare($query);
			$result->execute();
			$halaman = $result->rowCount();
		}
		catch (PDOException $e){
			die("Koneksi error: " . $e->getMessage());
		}

		$baris = $this->baris;
		$page = ceil($halaman/$baris);
		return $page;

	}
}

$pagging = new paging();

$pagging->baris = 2; //jumlah baris perhalaman

$page = isset($_GET['page']) ? $_GET['page'] : 1;

foreach ($pagging->getrow($page) as $value) { //getdata
	echo $value['name']." | ".$value['age']." | ".$value['email']."<br>";
}

for ($i=1; $i <= $pagging->page() ; $i++) { //page
	echo "<a href='?page=$i'>$i</a>"." | ";
}

?>
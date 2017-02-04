<?php

 $db = new DB('test', 'root', '', 'localhost', 'visitors');
 $ret = $db->connect();
 $con = $db->con;
 
 if ($ret) 
 {   
    foreach (getallheaders() as $name => $value) {
        if ($name == 'User-Agent') {
            $userAgent = $value;
        }   
    }
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    $url = $_SERVER['HTTP_REFERER'];

    $viewDate = date("Y-m-d H:i:s", time());

    $visitor = new Visitor($ip, $userAgent, $viewDate, $url, $con);
    $visitor->visit();
     
    $image = new Image ('Test Image');
    $image->createImage();
 }
 
class Image 
{ 
    
    protected $imageName;
    
    public function __construct($imageName) 
    {
      $this->imageName = $imageName;
    }
   
    public function createImage() 
    { 
        
        # Create a blank image and add some text
        $im = imagecreatetruecolor(120, 120);
        $textColor = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  $this->imageName, $textColor);

        #Set the content type header - in this case image/jpeg
        header('Content-Type: image/jpeg');

        # Output the image
        imagejpeg($im);

        # Free up memory
        imagedestroy($im);
    } 
}

class DB 
{
	protected $dbName;
	protected $dbUser;
	protected $dbPass;
	protected $dbHost;
        protected $table;
        public $con;
        
        public function __construct($dbName, $dbUser, $dbPass, $dbHost, $table) 
        {
          $this->dbName = $dbName;
          $this->dbUser = $dbUser;
          $this->dbPass = $dbPass;
          $this->dbHost = $dbHost;
          $this->table = $table;
        }
	
	# Open a connect to the database.
	# Check if necessary table exist
	public function connect() 
        {
	
		$connectDb = new mysqli( $this->dbHost, $this->dbUser, $this->dbPass, $this->dbName );
                $this->con = $connectDb;
		
		if (( mysqli_connect_errno() )OR((mysqli_num_rows(mysqli_query($connectDb, "SHOW TABLES LIKE '".$this->table."'")) != 1) )) {
			printf("Connection failed: %s\
                            ", mysqli_connect_error());
			exit();
		}
		return true;
		
	}
}  

class Visitor
{
    protected $ip;
    protected $userAgent;
    protected $viewDate;
    protected $url;
    protected $count = 0;
    protected $con;
    
     public function __construct($ip, $userAgent, $viewDate, $url, $con) 
     {
          $this->ip = $ip;
          $this->userAgent = $userAgent;
          $this->viewDate = $viewDate;
          $this->url = $url;
          $this->con = $con;
     }
     
     public function visit() 
     {
        $ret = $this->checkIfVisitorExist($this->ip, $this->userAgent, $this->url); 
        if (!$ret) {

            $this->createNewVisitor($this->ip, $this->userAgent, $this->url, $this->viewDate); 
            
        } else {

           $id = (int) $ret['id'];
           $lastCount = (int) $ret['views_count']; 

           $this->updateVisitor($id, $lastCount, $this->viewDate); 
        }
     }
     
     private function checkIfVisitorExist($ip, $userAgent, $url) 
     {
        $result = mysqli_query($this->con, "SELECT * FROM visitors WHERE ip_address='$ip' AND user_agent='$userAgent' AND page_url='$url' LIMIT 1");
        
        if(mysqli_num_rows($result) > 0 ) {
           return $result->fetch_array(MYSQLI_ASSOC);
        }

        return false;
     }
     
     private function createNewVisitor($ip, $userAgent, $url, $viewDate) 
     {
         
        $sql = "INSERT INTO visitors (ip_address, user_agent, view_date, page_url, views_count) VALUES ('".$ip."', '".$userAgent."', '".$viewDate."', '".$url."', '1')";
        
        $ret = $this->con->query($sql);
        
        if ($ret === TRUE) {
            $this->con->close();
            return true;
        } 

        return false;
     }
     
     private function updateVisitor($id, $lastCount,  $viewDate) 
     {
        $newCount =  $lastCount+1;
        
        $sql = "UPDATE visitors SET views_count='".$newCount."', view_date='".$viewDate."'  WHERE id='".$id."'";
        
        $ret = $this->con->query($sql);

        if ($ret === TRUE) {
            $this->con->close();
            return true;
        } 

        return false;
     }
    
}



<?php

  class Database{
  
    /**
     * Set Host
     *
     * @access private 
     * @var string
    */
    private $host = DB_SERVER;
    /**
     * Database user
     *
     * @access private 
     * @var string
    */
    private $user = DB_SERVER_USERNAME;
    /**
     * Database password
     *
     * @access private 
     * @var string
    */
    private $pass = DB_SERVER_PASSWORD;
    /**
     * Database name
     *
     * @access private 
     * @var string
    */
    private $dbname = DB_DATABASE;
    /**
     * Database type
     *
     * @access private 
     * @var string
    */
    private $dbtype = 'mysql' /* DB_TYPE */;
    /**
     * Database charset
     *
     * @access private 
     * @var string
    */
    private $dbcharset = DB_CHARSET;
    /**
     * Instance of Database connection via PDO
     *
     * @access public 
     * @var object
    */
    public $dbh = NULL;
    /**
     * Instance of PDO Statement
     *
     * @access public 
     * @var object
    */
    public $stmt;
    
    /**
     * Enable a database connection and set the connection parameter
    */
    public function __construct(){
    
      if ( $this->dbh === NULL || !is_object( $this->dbh ) || ( is_object ( $this->dbh ) && ( $this->dbh instanceof mysqli === FALSE ) ) ) {
      
        try {
        
          $this->dbh = new mysqli( $this->host, $this->user, $this->pass, $this->dbname );
          
          $this->dbh->set_charset( $this->dbcharset );
        
        }
        // Catch any errors
        catch ( mysqli_sql_exception $e )
        {
          /*
          die('<p><strong>Error:</strong> '. $e->getMessage(). '</p>
          <p><strong>File:</strong> '. $e->getFile(). '</br>
          <p><strong>Line:</strong> '. $e->getLine(). '</p>');
          */
          SimpleLogger :: logException( $e );
          //Redirect
          header("Location: error.html");
          exit();
        }
      }
    }
  }
?>
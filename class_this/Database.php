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
    
      if ( $this->dbh === NULL || !is_object( $this->dbh ) || ( is_object ( $this->dbh ) && ( $this->dbh instanceof PDO === FALSE ) ) ) {
      
        // Set DSN
        $dsn = $this->dbtype . ':host=' . $this->host . ';dbname=' . $this->dbname;
        
        // Set options
        $options = array( /**
                           * Set default database charset
                          */
                          PDO :: MYSQL_ATTR_INIT_COMMAND => "SET NAMES '" . $this->dbcharset . "'", 
                          /**
                           * Disable emulated prepared statements
                          */
                          PDO :: ATTR_EMULATE_PREPARES => false, 
                          /**
                           * Set default fetch mode
                          */
                          PDO :: ATTR_DEFAULT_FETCH_MODE => PDO :: FETCH_ASSOC, 
                          /**
                           * Include UPDATED QUERIES in to rowcount() function
                          */
                          // PDO :: MYSQL_ATTR_FOUND_ROWS => true, 
                          /**
                           * Enable persitente database connection
                          */
                          PDO :: ATTR_PERSISTENT => true, 
                          /**
                           * Error mode is exception
                          */
                          PDO :: ATTR_ERRMODE => PDO :: ERRMODE_EXCEPTION 
                        );
        
        try {
          $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        }
        // Catch any errors
        catch(PDOException $e)
        {
          /*
          die('<p><strong>Error:</strong> '. $e->getMessage(). '</p>
          <p><strong>File:</strong> '. $e->getFile(). '</br>
          <p><strong>Line:</strong> '. $e->getLine(). '</p>');
          */
          SimpleLogger :: logException($e);
          //Redirect
          header("Location: error.html");
          exit();
        }
      }
    }
    
    /**
     * Prepare a statement
     *
     * @param string $query
     *    a valid SQL statement template for the target database server
    */
    public function query($query){
    
      try {
        $this->stmt = $this->dbh->prepare($query);
      }
      // Catch any errors
      catch(PDOException $e)
      {
        /*
        die('<p><strong>Error:</strong> '. $e->getMessage(). '</p>
        <p><strong>File:</strong> '. $e->getFile(). '</br>
        <p><strong>Line:</strong> '. $e->getLine(). '</p>');
        */
        SimpleLogger :: logException($e);
        //Redirect
        header("Location: error.html");
        exit();
      }
    }
    
    /**
     * Binds a value to a corresponding named or question mark placeholder in the SQL statement that was used to prepare the statement.
     *
     * @param string $param
     *    parameter identifier
     *    this will be a parameter name of the form :name
     *    using question mark placeholders, this will be the 1-indexed position of the parameter
     * @param string $value
     *    the value to bind to the parameter
     * @param string $type
     *    explicit data type for the parameter using the PDO::PARAM_* constants
    */
    public function bind($param, $value, $type = null){
    
      // if value not typed
      if (is_null($type)) {
        switch (true) {
          case is_int($value):
            $type = PDO :: PARAM_INT;
            break;
          case is_bool($value):
            $type = PDO :: PARAM_BOOL;
            break;
          case is_null($value):
            $type = PDO :: PARAM_NULL;
            break;
          default:
            $type = PDO :: PARAM_STR;
        }
      }
      try {
        $this->stmt->bindValue($param, $value, $type);
      }
      // Catch any errors
      catch(PDOException $e)
      {
        /*
        die('<p><strong>Error:</strong> '. $e->getMessage(). '</p>
        <p><strong>File:</strong> '. $e->getFile(). '</br>
        <p><strong>Line:</strong> '. $e->getLine(). '</p>');
        */
        SimpleLogger :: logException($e);
        //Redirect
        header("Location: error.html");
        exit();
      }
    }
    
    /**
     * Execute a SQL statement that was prepare for delete, insert or update.
     * 
     * @return bool 
    */
    public function execute($params = NULL){
    
      try {
        return $this->stmt->execute($params);
      }
      // Catch any errors
      catch(PDOException $e)
      {
        /*
        die('<p><strong>Error:</strong> '. $e->getMessage(). '</p>
        <p><strong>File:</strong> '. $e->getFile(). '</br>
        <p><strong>Line:</strong> '. $e->getLine(). '</p>');
        */
        SimpleLogger :: logException($e);
        //Redirect
        header("Location: error.html");
        exit();
      }
    }
    
    /**
     * Execute a SQL statement that was prepare for select and return all results.
     * 
     * @return array 
    */
    public function resultset(){
    
      try {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
      }
      // Catch any errors
      catch(PDOException $e)
      {
        /*
        die('<p><strong>Error:</strong> '. $e->getMessage(). '</p>
        <p><strong>File:</strong> '. $e->getFile(). '</br>
        <p><strong>Line:</strong> '. $e->getLine(). '</p>');
        */
        SimpleLogger :: logException($e);
        //Redirect
        header("Location: error.html");
        exit();
      }
    }
    
    /**
     * Execute a SQL statement that was prepare for select and return a single result.
     * 
     * @return array 
    */
    public function single(){
    
      try {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
      }
      // Catch any errors
      catch(PDOException $e)
      {
        /*
        die('<p><strong>Error:</strong> '. $e->getMessage(). '</p>
        <p><strong>File:</strong> '. $e->getFile(). '</br>
        <p><strong>Line:</strong> '. $e->getLine(). '</p>');
        */
        SimpleLogger :: logException($e);
        //Redirect
        header("Location: error.html");
        exit();
      }
    }
    
    public function rowCount(){
    
      try {
        return $this->stmt->rowCount();
      }
      // Catch any errors
      catch(PDOException $e)
      {
        /*
        die('<p><strong>Error:</strong> '. $e->getMessage(). '</p>
        <p><strong>File:</strong> '. $e->getFile(). '</br>
        <p><strong>Line:</strong> '. $e->getLine(). '</p>');
        */
        SimpleLogger :: logException($e);
        //Redirect
        header("Location: error.html");
        exit();
      }
    }
    
    /**
    * * Returns the last inserted id
    * 
    * @return int 
    */
    public function lastInsertId(){
    
      try {
        return $this->dbh->lastInsertId();
      }
      // Catch any errors
      catch(PDOException $e)
      {
        /*
        die('<p><strong>Error:</strong> '. $e->getMessage(). '</p>
        <p><strong>File:</strong> '. $e->getFile(). '</br>
        <p><strong>Line:</strong> '. $e->getLine(). '</p>');
        */
        SimpleLogger :: logException($e);
        //Redirect
        header("Location: error.html");
        exit();
      }
    }
    
    /**
     * Initiates a transaction 
    */
    public function beginTransaction(){
    
      try {
        return $this->dbh->beginTransaction();
      }
      // Catch any errors
      catch(PDOException $e)
      {
        /*
        die('<p><strong>Error:</strong> '. $e->getMessage(). '</p>
        <p><strong>File:</strong> '. $e->getFile(). '</br>
        <p><strong>Line:</strong> '. $e->getLine(). '</p>');
        */
        SimpleLogger :: logException($e);
        //Redirect
        header("Location: error.html");
        exit();
      }
    }
    
    public function endTransaction(){
    
      try {
        return $this->dbh->commit();
      }
      // Catch any errors
      catch(PDOException $e)
      {
        /*
        die('<p><strong>Error:</strong> '. $e->getMessage(). '</p>
        <p><strong>File:</strong> '. $e->getFile(). '</br>
        <p><strong>Line:</strong> '. $e->getLine(). '</p>');
        */
        SimpleLogger :: logException($e);
        //Redirect
        header("Location: error.html");
        exit();
      }
    }
    
    public function cancelTransaction(){
    
      try {
        return $this->dbh->rollBack();
      }
      // Catch any errors
      catch(PDOException $e)
      {
        /*
        die('<p><strong>Error:</strong> '. $e->getMessage(). '</p>
        <p><strong>File:</strong> '. $e->getFile(). '</br>
        <p><strong>Line:</strong> '. $e->getLine(). '</p>');
        */
        SimpleLogger :: logException($e);
        //Redirect
        header("Location: error.html");
        exit();
      }
    }
    
    public function debugDumpParams(){
    
      try {
        return $this->stmt->debugDumpParams();
      }
      // Catch any errors
      catch(PDOException $e)
      {
        /*
        die('<p><strong>Error:</strong> '. $e->getMessage(). '</p>
        <p><strong>File:</strong> '. $e->getFile(). '</br>
        <p><strong>Line:</strong> '. $e->getLine(). '</p>');
        */
        SimpleLogger :: logException($e);
        //Redirect
        header("Location: error.html");
        exit();
      }
    }
  }
?>
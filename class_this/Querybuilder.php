<?php
/**
* ###################################################################################
* Bigware Shop 3.0
* Release Datum: 30.05.2016
* 
* Bigware Shop
* http://www.bigware.de
* 
* Copyright (c) 2018 Bigware LTD
* $Id: pdo.php 0001 2016-05-30 19:47:11Z Gulliver72
* 
* Released under the GNU General Public License
* ##################################################################################
* 
* Simple and smart SQL query builder for PDO.
* 
* @category Library
* @version 0.9.2
* @author guncebektas <info@guncebektas.com> 
* @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @link http://guncebektas.com
* @link http://github.com/guncebektas/lenkorm ->write   : will show you the query string
->run  : will run the query
->result  : will return the result of selected result (only one row)
->results : will return the results of query (multi row)

otherwise you will only create query string!

insert_id, find, columns, insert methods will be exacuted directly 

Examples:

1. THIS WILL SELECT ALL ROWS IN SLIDES TABLE
select('slides')->results(); 



2. INSERT ARRAY INTO SLIDES TABLE 

insert('slides')->values(array('slide_img'=>$_POST['slide_img'], 
'slide_title'=>$_POST['slide_title'],
'slide_text'=>$_POST['slide_text'],
'slide_href'=>$_POST['slide_href']));



3. UPDATE SLIDES TABLE 

update('slides')->values(array('slide_img'=>$_POST['slide_img'], 
'slide_title'=>$_POST['slide_title'],
'slide_text'=>$_POST['slide_text'],
'slide_href'=>$_POST['slide_href']))->where('slide_id = 1');


PS 1: you can put array into values like values($_POST) if columns match with the index of array

PS 2: use security function in where clause to block SQL injection like 
->where('slide_id = '.security($_GET['slide_id']));
*/

  require('../func_this/security.php');
  
  class Querybuilder {
  /**
  * Connection to database 
  * db object with connection of database
  * 
  * @access public 
  * @var object 
  */
  public $db;
  /**
  * * Query string
  * 
  * @access public 
  * @var string 
  */
  public $query;
  
  /**
  * * Type of query such as insert or update, important to determine when the query will run
  * 
  * @access public 
  * @var string 
  */
   private $type;
  
  /**
  * * Values for update and insert statements
  * 
  * @access public 
  * @var string 
  */
  private $values;
  
  /**
  * * Caching with memcache
  * 
  * @access public 
  * @var bool 
  */
  public $memcache = false;
  public $cache_time = 600;
  
  public function __construct() {
  
    $this -> setConnection();
  }
  private function setConnection() {
  
    $this -> db = new Database();
  }
  /**
  * * Returns the selected row from selected table with 
  * the match of first column
  * 
  * @example find('coupons', 5);
  * @param string $table name of the table in the database
  * @param int $id unique id of table which is in the first column of table
  * @return array 
  */
  public function find( $table, $id ) {
  
    try
    {
      $columns = $this -> column( security( $table ) );
      
      return $this -> select( security( $table ) ) -> where( $columns['Field'] . ' = ' . security( $id ) ) -> limit( 1 );
    }
    catch(PDOException $e)
    {
      /*
      die('<p><strong>Error:</strong> '. $e->getMessage(). '</p>
      <p><strong>File:</strong> '. $e->getFile(). '</br>
      <p><strong>Line:</strong> '. $e->getLine(). '</p>');
      */
      SimpleLogger::logException($e);
      //Redirect
      header("Location: error.html");
      exit();
    }
  }
  /**
  * * Selects the table
  * 
  * @example select('coupons')->where('coupon_id = 5')->result();
  * @param string $table name of the table in the database
  * @return string 
  */
  public function select( $table ) {
  
    $this -> query = 'SELECT * FROM ' . $table . ' ';
    
    return $this;
  }
    /**
    * * LEFT JOIN function
    * 
    * @example select('contents')->left('categories ON categories.category_id = contents.category_id')->where('author_id = 2')->results();
    * @param string $condition clause for left join
    * @return string 
    */
    public function left( $condition ) {
    
         $this -> query .= 'LEFT JOIN ' . $condition . ' ';
        
         return $this;
         } 
    /**
    * * USING clause
    * 
    * @example select('contents')->left('categories')->using('category_id')->where('content_id = 2')->result();
    * @param string $column column name for using clause
    * @return string 
    */
    public function using( $column )
    
    {
         $this -> query .= ' USING (' . $column . ')';
        
         return $this;
         } 
    /**
    * * Insert and Update methods are determining private variable type and these two methods are working with values method
    * 
    * Insert prepares the statement and runs it with the given variables
    * Update prepates the statement but where methods runs it because of the syntex
    * 
    * @example insert('coupons')->values(array[]);
    * @param string $table table name
    * @return string 
    */
    public function insert( $table )
    
    {
         $this -> type = 'insert';
        
         $this -> query = 'INSERT INTO ' . $table . ' ';
        
         return $this;
         } 
    public function replace( $table )
    
    {
         $this -> type = 'insert';
        
         $this -> query = 'REPLACE INTO ' . $table . ' ';
        
         return $this;
         } 
    public function update( $table )
    
    {
         $this -> type = 'update';
        
         $this -> query = 'UPDATE ' . $table . ' SET ';
        
         return $this;
         } 
    /**
    * * Delete from table, if key is not empty method will delete row by the first column match
    * 
    * @example delete('coupons')->where('coupon_id = 5');
    * @param string $table table name
    * @param int $id unique id to match with the first column of table
    * @return deletes from the table
    */
    public function delete( $table, $id = '' )
    
    {
         if ( empty( $id ) ) {
            $this -> query = 'DELETE FROM ' . $table . ' ';
            
             return $this;
             } else {
                  // Key is not empty, so delete by first column match
                  $columns = $this -> column( $table );
                  $this -> delete( $table ) -> where( '' . $columns['Field'] . ' = "' . $id . '"' ) -> limit( 1 );
                  return $this;
             } 
        } 
    /**
    * * Alter table
    * 
    * @param string $table table name
    * @return string 
    */
    public function alter( $table )
    
    {
         $this -> query = 'ALTER TABLE ' . $table . ' ';
        
         return $this;
         } 
    /**
    * * Rename table
    * 
    * @example alter('slides')->rename_to('carousel');
    * @param string $new_name table name
    * @return runs query
    */
    public function rename_to( $new_name )
    
    {
         $this -> query .= 'RENAME TO ' . $column . ' ' . $datatype;
        
         $this -> query( $this -> query );
         } 
    /**
    * * Add column into table
    * 
    * @example alter('slides')->add_column('slide_index','slide_id');
    * @param string $column column name
    * @param string $datatype data type
    * @return runs query
    */
    public function add_column( $column, $datatype )
    
    {
         $this -> query .= 'MODIFY COLUMN ' . security( $column ) . ' ' . security( $datatype );
        
         $this -> query( $this -> query );
         } 
    /**
    * * Drop column from table
    * 
    * @example alter('slides')->drop_column('slides');
    * @param string $column column name
    * @param string $datatype data type
    * @return runs query
    */
    public function drop_column( $column )
    
    {
         $this -> query .= 'DROP COLUMN ' . security( $column );
        
         $this -> query( $this -> query );
         } 
    /**
    * * Add index into table
    * 
    * @example alter('slides')->add_index('slide_index','slide_id');
    * @param string $name table name
    * @param string $column column name
    * @return runs query
    */
    public function add_index( $name, $column )
    
    {
         $this -> query .= 'ADD INDEX ' . security( $name ) . ' (' . security( $column ) . ')';
        
         $this -> query( $this -> query );
         } 
    /**
    * * Increase a value
    * 
    * @example update('coupons')->increase('coupon_amount')->where('coupon_id = 2');
    * @param string $column column name of table
    * @param int $ optional $value  amount to increase
    * @return string 
    */
    public function increase( $column, $value = 1 )
    
    {
         $column = security( $column );
         $this -> query .= $column . ' = ' . $column . ' + ' . ( int )$value . ' ';
        
         return $this;
         } 
    /**
    * * Decrease a value
    * 
    * @example update('coupons')->decrease('coupon_amount', 4)->where('coupon_id = 2');
    * @param string $column column name of table
    * @param int $ optional $value  amount to decrease
    * @return string 
    */
    public function decrease( $column, $value = 1 )
    
    {
         $column = security( $column );
         $this -> query .= $column . ' = ' . $column . ' - ' . ( int )$value . ' ';
        
         return $this;
         } 
    /**
    * * Values method prepares the query for insert and update methods
    *   It also runs the query for insert queries, update queries will run after where clause is completed
    * 
    * @example insert('coupons')->values(array[]);
    * @param array $values the array to insert or update
    * @return string 
    */
    public function values( $values )
    
    {
         $this -> values = $values;
        
         $keys = array_keys( $values );
         $vals = array_values( $values );
        
        /**
        /* INSERT INTO books (title,author) VALUES (:title,:author); */
        if ($this->type == 'insert') {
            $row = '(';
            for ($i = 0; $i < count($values); $i++) {
                $row .= $keys[$i];
                if ($i != count($values) - 1) {
                    $row .= ', ';
                } else {
                    $row .= ') VALUES (';
                }
            }
            for ($i = 0; $i < count($values); $i++) {
              $row .= ':'.$keys[$i];
                if ($i != count($values) - 1) {
                    $row .= ', ';
                } else {
                    $row .= ')';
                }
            }
            $this->query .= $row;
            $query = $this->db->prepare($this->query);
/*
            // If the values are formed as an array than encode it
            foreach ($values AS $value){
              if (is_array($value))
                $value = json_encode($value);
            
              $res[] = $value;
            }
*/
            /*
            echo $this->query;
            // Bind params
            foreach ($keys AS $key){
              $this->bindParam(':'.$key, $key);
            }
            */
            $query->execute($res);
        }
        /**
        * UPDATE books SET title=:title, author=:author
        */
        elseif ( $this -> type == 'update' ) {
            for ( $i = 0; $i < count( $values ); $i++ ) {
                $this -> query .= security( $keys[$i] ) . ' = :' . security( $keys[$i] ) . ' ';
                 if ( $i != count( $values ) - 1 ) {
                    $this -> query .= ', ';
                     } 
                } 
            
            return $this;
             } 
        } 
    /**
    * * Where condition
    * 
    * @param string $condition condition to appand select, update, delete etc...
    * @return string , if prepended query has update method it also exacutes update
    */
    public function where( $condition )
    
    {
         $this -> query .= ' WHERE ' . $condition;
        
         if ( $this -> type == 'update' ) {
            $query = $this -> db -> prepare( $this -> query );
            
             // If the values are formed as an array than encode it
            foreach ( $this -> values AS $value ) {
                if ( is_array( $value ) )
                     $value = json_encode( $value );
                
                 $res[] = $value;
                 } 
            
            $query -> execute( $res );
            
             return $this;
             } else {
            return $this;
             } 
        } 
    /**
    * * Which columns, condition will replace with *
    * 
    * @param string $codition clause to replace with *
    * @return string 
    */
    public function which( $condition )
    
    {
         $this -> query = str_replace( '*', $condition, $this -> query );
        
         return $this;
         } 
    /**
    * * Group condition
    * 
    * @param string $codition group by clause
    * @return string 
    */
    public function group( $condition )
    
    {
         $this -> query .= ' GROUP BY ' . security( $condition );;
        
         return $this;
         } 
    /**
    * * Having condition
    * 
    * @param string $condition having clause
    * @return string 
    */
    public function have( $condition )
    
    {
         $this -> query .= ' HAVING ' . $condition;
        
         return $this;
         } 
    /**
    * * Order condition
    * 
    * @param string $condition order by clause
    * @return string 
    */
    public function order( $condition )
    
    {
         $this -> query .= ' ORDER BY ' . security( $condition );
        
         return $this;
         } 
    /**
    * * Limit condition
    * 
    * @example select('contents')->where('author_id = 2')->order('content_time DESC')->limit(100);
    * @param int $limit 
    * @return string 
    */
    public function limit( $limit = 3000 )
    
    {
         $this -> query .= ' LIMIT ' . security( $limit ) . ' ';
        
         return $this;
         } 
    /**
    * * Offset condition
    * 
    * @param int $offset 
    * @return string 
    */
    public function offset( $offset = 3000 )
    
    {
         $this -> query .= ' OFFSET ' . security( $offset ) . ' ';
        
         return $this;
         } 
    /**
    * * Return the columns of table
    * 
    * @example column('coupons')
    * @param string $table 
    * @return array 
    */
    public function column( $table )
    
    {
         $query = $this -> query( 'SHOW COLUMNS FROM ' . security( $table ) );
        
         return $query -> fetch();
         } 
  /**
  * * Creates the table
  * 
  * @example create('coupons')->which(coupon_id int(11) AUTO_INCREMENT PRIMARY KEY)->run();
  * @param string $table name of the table in the database
  * @return string 
  */
    public function create( $table )
    
    {
         $this -> query = 'CREATE TABLE IF NOT EXISTS ' . security( $table ) . ' (*)';
        
         return $this;
         } 
    /**
    * * Writes query string to screen, not works with methods, which returns data set, such as find, coluns etc...
    * 
    * @example select('coupons')->where('coupon_id = 5')->write();
    * @return writes query string to screen
    */
    final public function write()
    
    {
         echo $this -> query;
         } 
    /**
    * * Runs the query
    * 
    * @param  $return will return query, no need to change it
    * @return if $return is true function returns query
    */
    final public function run( $return = false )
    
    {
         if ( $return ) {
            $this -> db -> query ( $this -> query );
            return $this -> db -> stmt;

         } else {
        
            $this -> query;
          }
         } 
    /**
    * * Run and get the value of query
    * 
    * @example select('coupons')->where('coupon_id = 5')->result();
    * @example select('coupons')->where('coupon_id = 5')->result('coupon_name);
    * @param string $ optional  $key
    * @return if $key is empty it returns an array else a string
    */
    final public function result( $key = '' )
    
    {
         if ( !$this -> memcache ) {
            $query = $this -> run( true );
            
             if ( !$key ) {
                return $query -> fetch();
                 } else {
                $result = $query -> fetch();
                
                 return $result[$key];
                 } 
            } 
        
        $memcache = new Memcache();
         $memcache -> connect( '127.0.0.1', 11211 ) or die( 'MemCached connection error!' );
        
         $data = $memcache -> get( 'query-' . md5( $this -> query ) );
        
         if ( !isset( $data ) || $data === false ) {
            $query = $this -> run( true );
            
             if ( !$key ) {
                return $query -> fetch();
                 } else {
                $result = $query -> fetch();
                
                 return $result[$key];
                 } 
            
            $memcache -> set( 'query-' . md5( $this -> query ), $result, MEMCACHE_COMPRESSED, $this -> cache_time );
            
             return $result;
             } else {
            return $data;
             } 
        } 
    /**
    * * Runs and fetchs the result set of the query
    * 
    * @example select('coupons')->where('coupon_id = 5')->results();
    * @return array results set
    */
    final public function results( $cache = true )
    
    {
         if ( !$this -> memcache || $cache == false ) {
            $res = $this -> run( true );
            $results = $res -> fetchAll( PDO :: FETCH_ASSOC );
  SimpleLogger :: dump($results);
            
             return $results;
             } 
        
        $memcache = new Memcache();
         $memcache -> connect( '127.0.0.1', 11211 ) or die( 'MemCached connection error!' );
        
         $data = $memcache -> get( 'query-' . md5( $this -> query ) );
         if ( !isset( $data ) || $data === false ) {
            $res = $this -> run( true );
            $results = $res -> fetchAll( PDO :: FETCH_ASSOC );
            
             $memcache -> set( 'query-' . md5( $this -> query ), $results, MEMCACHE_COMPRESSED, $this -> cache_time );
            
             return $results;
             } else {
            return $data;
             } 
        } 
    /**
    * * Gather results as pair, is very useful when working with lists
    * 
    * @param string $key 
    * @param string $values 
    * @return array data set as pairs
    */
    final public function results_pairs( $key, $values = '' )
    
    {
         $results = $this -> results();
        
         foreach ( $results as $result ) {
            foreach ( $values as $value ) {
                $res[$result[$key]][$value] = $result[$value];
                 } 
            } 
        
        return $res;
         } 
    /**
    * * Number of rows
    * 
    * @example select('users')->num_rows();
    * @return integer 
    */
    final public function num_rows()
    
    {
         $query = $this -> run( true );
         return $query -> num_rows();
        
         $results = $query -> fetch_array();
         return count( $results );
         } 
    } 
/**
* Extend PDOStatement for some methods
*/
class _pdo_statement extends PDOStatement
 {
    /**
    * Set the rule of fetchAll. Values will be returned as PDO::FETCH_ASSOC in fetch_array and fetch_assoc functions
    */
    public function fetch_array()
    
    {
         return $this -> fetchAll( PDO :: FETCH_ASSOC );
         } 
    public function fetch_assoc( $result )
    
    {
         return $this -> fetchAll( PDO :: FETCH_ASSOC );
         } 
    /**
    * Return number of rows
    */
    public function num_rows()
    
    {
         return $this -> rowcount();
         } 
    /**
    * Return affected wors
    */
    public function affected_rows()
    
    {
         return $this -> rowcount();
         } 
    }
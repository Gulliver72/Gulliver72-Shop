<?php
class BigLoader {

  public static function load($klassenname) {
    if (strpos ($klassenname, '.') !== false || strpos ($klassenname, '/') !== false || strpos ($klassenname, '\\') !== false || strpos ($klassenname, ':') !== false) {
      return false;
    }
    
    $arr = explode('_', $klassenname);
    $dir = '';
    if (isset($arr[1])) $dir = strtolower ($arr[1]) . DIRECTORY_SEPARATOR;
    $pfad = __DIR__ . DIRECTORY_SEPARATOR . $dir . $klassenname . '.php';
  
    if (file_exists($pfad)) {
      require_once $pfad;
    } else {
      return false; 
    }
  }
}
/*
class BIGAutoloader {
  private static $basisPfad = null;
  public static function bigload ($klasse) {
    if (!defined('BIGAUTO')) define('BIGAUTO', '1');
    // Zuerst wird der Basis-Pfad der Klassenbibliothek ausfindig gemacht. 
    // Da die Datei Autoloader.php direkt darin liegt, ist der Basis-Pfad natürlich das Verzeichnis, in dem sich diese Datei befindet, also dirname (__FILE__). 
    // Der Pfad wird zwischengespeichert, um bei weiteren Aufrufen Zeit zu sparen. 
    if (self::$basisPfad === null) self::$basisPfad = dirname (__FILE__);
    // Nun wird überprüft, ob die Klasse zur Klassenbibliothek gehört, in diesem Fall wird dies daran erkannt, ob die Klasse mit BIGWARE anfängt. 
    if (substr ($klasse, 0, 7) !== "BIGWARE") return;
    // Besonderheit hier ist die Überprüfung auf schädliche Zeichen: 
    // Im Normalfall sind diese Zeichen in Klassennamen in PHP sowieso nicht erlaubt, 
    // allerdings reicht call_user_func diese Zeichen unter Umständen direkt an __autoload durch. 
    // Damit wäre es unter Umständen möglich, dass fremde Inhalte per include eingebunden werden können. 
    // Dies wird durch diese Abfrage verhindert. 
    if (strpos ($klasse, '.') !== false || strpos ($klasse, '/') !== false || strpos ($klasse, '\\') !== false || strpos ($klasse, ':') !== false) {
      return;
    }
    // Nun wird der Name der Klasse aufgeteilt in mehrere Elemente. 
    // Hier wird angenommen, dass die Klasse in CamelCase vorliegt, das heißt, 
    // dass in dem Klassennamen Wörter aneinandergereiht werden, deren erster Buchstabe immer großgeschrieben wird.
    $teile = preg_split ('/(?<=.)(?=\p{Lu}\P{Lu})|(?<=\P{Lu})(?=\p{Lu})/U', substr ($klasse, 7));
    // Die Teile werden nun wieder zusammengefügt mit Hilfe der Konstante DIRECTORY_SEPARATOR, 
    // das ist der Verzeichnistrenner unter dem jeweiligen Betriebssystem, unter Windows also \, unter Linux oder Mac OS X /. 
    // Zudem wird die Endung .php angehängt und der vorher bestimmte Basispfad berücksichtigt. 
    $pfad = self::$basisPfad . DIRECTORY_SEPARATOR . join (DIRECTORY_SEPARATOR, $teile) . '.php';
    // Falls diese Datei existiert, wird diese eingebunden. 
    if (file_exists ($pfad)) include_once $pfad;
  }
}

/*
namespace MyApp\core;

class Autoloader {

  private $namespace;

  public function __construct($namespace = null) {
    $this->namespace = $namespace;
  }

  public function register() {
    spl_autoload_register(array($this, 'loadClass'));
  }

  public function loadClass($className) {
    if($this->namespace !== null) {
      $className = str_replace($this->namespace . '\\', '', $className);
    }
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $file = ROOT_PATH . $className. '.php';
    if(file_exists($file)) {
      require_once $file;
    }
  }
}
*/
?>
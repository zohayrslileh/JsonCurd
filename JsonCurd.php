<?php


/**
 * A tool that allows you to smoothly manage json files (create - modify - add - readers - delete - and more...),
 * you can also navigate to any specific part of the json file you want, easily perform the above operations on it,
 * and then save the file again.
 * The system also saves a virtual copy of the file after modification to be used without modifying the real file,
 * and it can be saved permanently whenever you want.
 * Most of the uses are in managing configuration files, and complex json files.
 *
 *
 * PHP version 7.4
 *
 *
 * @category   system
 * @package    tools
 * @author     Zohayr SLILEH <zohayrslileh@gmail.com>
 * @copyright  2022 - ESDApps
 * @version    SVN: 1.0.1
 * @link       http://pear.php.net/package/PackageName
 */

class JsonCurd {

    /**
     * JSON path
     */
    private string $filepath;
    private array $json = [];
    private array $map = [];

    /**
     * JSON __construct
     * @param string $filepath
     */
    function __construct( string $filepath )
    {
        $this->filepath = $filepath;

        // Try read json data
        try {

            $this->json = json_decode(
                file_get_contents( $this->filepath ),
                true
            );
        } catch(\Error $e) {

            $this->json = [];
        }

    }

    /**
     * JSON map method
     * @param string $route
     * @return array
     */
    private function map( string $route ) {

        // Explode route
        $map = explode("/", $route);

        // Remove first slash if exists
        if( $route[0] === '/' )
            array_shift( $map );

        // Append route
        else
            $map = array_merge($this->map, $map);

        return $map;
    }

    
    /**
     * Generate method
     * @param $params
     * @return array
     */
    private function generate( $params = null ) {

        $newJson = $params;

        // Check map is exists
        foreach( array_reverse( $this->map ) as $child ) {

            // Check child
            if( $child === '' ) continue;

            $newJson = [
                $child => $newJson
            ];
        }

        return $newJson;
    }

    /**
    * Convert array to object
    * @param array $array the array do you want convert to object
    * @return object
    */
    public static function object( array $array ) {
        
        return json_decode(json_encode(
            $array
        ), false);
    }

    /**
     * Organize method
     * @param string $JsonString
     */
    private static function organize( string &$JsonString ) {

        $JsonString = str_replace("{", "{\n    ", $JsonString);
        $JsonString = str_replace("}", "\n}\n    ", $JsonString);
        $JsonString = str_replace("[", "[\n    ", $JsonString);
        $JsonString = str_replace("]", "\n]\n    ", $JsonString);
        $JsonString = str_replace(",", ",\n    ", $JsonString);
    }

    /**
     * Open method
     * @param string $filepath
     * @return self
     */
    static function open( string $filepath ) {

        return new self( $filepath );
    }


    /**
     * Create method
     * @param string $filepath
     * @return self
     */
    static function create( string $filepath ) {

        // Create file if not exists
        if( !file_exists( $filepath ) )
            file_put_contents( $filepath, "");

        return new self( $filepath );
    }

    /**
     * Delete method
     * @param string $filepath
     */
    static function delete( string $filepath ) {

        unlink( $filepath );
    }

    /**
     * Go method
     * @param string $route
     * @return
     */
    public function go( string $route ) {

        $this->map = $this->map( $route );

        return $this;
    }


    /**
     * Read method
     * @return object
     */
    public function read() {

        $json = $this->json;

        // Check map is exists
        foreach( $this->map as $child ) {

            // Check is array
            if( !is_array( $json ) ) {

                $json = null;
                break;
            }

            // Check child
            if( $child === '' ) continue;

            $json = $json[ $child ];

        }

        return is_array( $json ) ? self::object( $json ) : $json;
    }


    /**
     * Set method
     * @param $params
     * @param bool $append
     * @return
     */
    public function set( $params, bool $append = false ) {

        // Check is append
        if( $append ) {
            
            // Read old data
            $readOld = (array) $this->read();

            array_push($readOld, ...$params);

            // Update params
            $params = $readOld;
        }

        // Update json
        $this->json = (array) array_replace_recursive(
            $this->json,
            $this->generate( $params )
        );

        return $this;
    }

    /**
     * Append method
     * @param $params
     * @return
     */
    public function append( $params ) {

        return $this->set($params, true);
    }


    /**
     * delete method
     * @param array $keys
     * @return
     */
    public function remove( array $keys ) {

        $readOld = (array) $this->read();

        // Check found old data
        if( !$readOld ) return $this;

        // Remove keys
        foreach( $keys as $key ) {

            unset($readOld[ $key ]);
        }

        // Set route null and Set cleared data
        return $this->set( null )->set( $readOld );
    }
    

    /**
     * Save method
     * @param bool $organize
     */
    function save( bool $organize = false ) {

        try {

            $newData = json_encode( $this->json );

            // Organize json string before save
            if( $organize )
                self::organize( $newData );

            file_put_contents( $this->filepath, $newData );

            return true;
        } catch(\Error $e) { 
            
            return false;
        }
    }
}

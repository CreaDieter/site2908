<?php
class Website_Service_Cache {

    /**
     * An instance of Website_Service_Cache
     * @var Website_Service_Cache
     */
    private static $instance;

    /**
     * an array to store the types in
     * @var array
     */
    private $ttl_by_type = array();


    /**
     * Constructor for the Website_Service_Cache class
     */
    private function __construct(){
        $this->ttl_by_type = array(
            'short'             => 300 ,    // 5 minutes
            'default'           => 7200,    // 2 hours
            'halfday'           => 43200,   // 12 hours
            'day'               => 86400,   // 24 hours
            'week'              => 604800,  // 7 days
            'month'             => 2592000, // 30 days (1 month)
            'longterm'          => 15811200 // 183 days (6 months)
        );
    }


    /**
     * @param $name
     * @return mixed
     */
    public function load($name){
        return Pimcore_Model_Cache::load($name);
    }


    /**
     * @param $name string  cacheKey
     * @param $data object   data to be cached
     * @param $type mixed  type of the cache model or the time in seconds
     * @param $tags array   tags for the cache model
     */
    public function write($name, $data, $type, $tags){
        // set ttl
        $ttl = $this->ttl_by_type['default'];
        if (is_string($type) && array_key_exists($type, $this->ttl_by_type)) $ttl = $this->ttl_by_type[$type];
        elseif (is_int($type)) $ttl = $type;

        Pimcore_Model_Cache::storeToCache($data, $name, $tags, $ttl);
    }

    /**
     * @param $name string cacheKey
     */
    public function delete($name){
        Pimcore_Model_Cache::remove($name);
    }


    /**
     * Get an instance of Website_Service_Cache if it doesn't exist yet (singleton)
     *
     * @return Website_Service_Cache
     */
    public static function getInstance() {
        if (!Website_Service_Cache::$instance instanceof self) {
            Website_Service_Cache::$instance = new self();
        }
        return Website_Service_Cache::$instance;
    }

}
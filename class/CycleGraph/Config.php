<?php

namespace CycleGraph;

class Config {

	private $_options=array();


	public function __construct($filename){
		$this->_options = $this->_loadConfig($filename);

		if (!empty($this->_options['phpsettings'])) {
            $this->setPhpSettings($this->_options['phpsettings']);
        }
	}

	/**
	 * Get all options
	 * @return array 
	 */
	public function getOptions(){
		return $this->_options;
	}

	/**
	 * Return the requested option, support and infinite number of nested layers
	 * Exemples :
	 *		getOption('database','db_name'); // Return the database name
	 *		getOption('database'); // Return the whole database configuration
	 *		getOption('database','slave', 0, 'host'); // Return the host of the first slave database
	 * @return mixed Requested option
	 */
	public function getOption() {
		$args = func_get_args();

		if(count($args) > 0) {
			$arg = array_shift($args);

			return $this->_getOption($this->_options, $arg, $args);
		}
		else {
			return NULL;
		}
	}

	private function _getOption($options, $arg, $args) {
		if(is_array($options) && isset($options[$arg])) {
			if(count($args) > 0) {
				$next_arg = array_shift($args);

				return $this->_getOption($options[$arg], $next_arg, $args);
			}
			else {
				return $options[$arg];
			}
		}
		else {
			return NULL;
		}
	}

	/**
	 * Set one ore more options
	 * Example : 
	 *		setOption('database', 'dbname', 'MyDatabaseName'); //Set the database.dbname to 'MyDatabaseName'
	 *		setOption('database', array('dbname'=>'MyDatabaseName', 'port'=>3306)); //Set an array of options
	 * @param string key, [key2]... Any number of nested key can be passed as argument.
	 * @param mixed Value
	 */
	public function setOption(){
		$args = func_get_args();

		if(count($args) < 2){
			throw new \InvalidArgumentException('At least one key and one value must be provided');
		}
		else {
			$value = array_pop($args);
			$key = array_shift($args);

			$this->_setOption($this->_options, $key, $value, $args);
		}
	}

	private function _setOption(&$options, $key, $value, $args){
		if(count($args) == 0){
			$options[$key] = $value;
			return;
		}
		else {
			if(!isset($options[$key])){
				$options[$key] = array();
			}

			$new_key = array_shift($args);
			$this->_setOption($options[$key], $new_key, $value, $args);
		}
	}

	/**
     * Load configuration file of options recursively.
     *
     * @param  string $file
     * @throws Exception When invalid configuration file is provided
     * @return array
     */
    private function _loadConfig($file)
    {
		return parse_ini_file($file, true);
    }


	/**
     * Merge options recursively
     *
     * @param  array $array1
     * @param  mixed $array2
     * @return array
     */
    public function _mergeOptions(array $array1, $array2 = null)
    {
        if (is_array($array2)) {
            foreach ($array2 as $key => $val) {
                if (is_array($array2[$key])) {
                    $array1[$key] = (array_key_exists($key, $array1) && is_array($array1[$key]))
                                  ? $this->_mergeOptions($array1[$key], $array2[$key])
                                  : $array2[$key];
                } else {
                    $array1[$key] = $val;
                }
            }
        }
        return $array1;
    }

    /**
     * Set PHP configuration settings
     *
     * @param  array $settings
     * @param  string $prefix Key prefix to prepend to array values (used to map . separated INI values)
     */
    public function setPhpSettings(array $settings, $prefix = '')
    {
        foreach ($settings as $key => $value) {
            $key = empty($prefix) ? $key : $prefix . $key;
            if (is_scalar($value)) {
                ini_set($key, $value);
            } elseif (is_array($value)) {
                $this->setPhpSettings($value, $key . '.');
            }
        }
    }

	public function getDataPath($create=true){
		$path = $this->getOption('paths', 'data');
		if(!$path) {
			throw new Exception('Missing data path configuration');
		}

		if($create && !is_dir($path)){
			FileSystem::mkdir($path, 0750, true);
		}

		return $path;
	}

	public function getTmpPath($create=true){
		$path = $this->getDataPath() . 'tmp/';
		if($create && !is_dir($path)){
			FileSystem::mkdir($path, 0750, true);
		}

		return $path;
	}
}
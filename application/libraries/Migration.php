<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package CodeIgniter
 * @author  EllisLab Dev Team
 * @copyright   Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright   Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license http://opensource.org/licenses/MIT  MIT License
 * @link    http://codeigniter.com
 * @since   Version 3.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration Class
 *
 * All migrations should implement this, forces up() and down() and gives
 * access to the CI super-global.
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Libraries
 * @author      Reactor Engineers
 * @link
 */
class CI_Migration {

    /**
     * Whether the library is enabled
     *
     * @var bool
     */
    protected $_migration_enabled = FALSE;

    /**
     * Migration numbering type
     *
     * @var	bool
     */
    protected $_migration_type = 'sequential';

    /**
     * Path to migration classes
     *
     * @var string
     */
    protected  $_migration_paths = NULL;

    /**
     * Current migration version
     *
     * @var mixed
     */
    protected $_migration_version = 0;

    /**
     * Database table with migration info
     *
     * @var string
     */
    protected $_migration_table = 'migrations';

    /**
     * Whether to automatically run migrations
     *
     * @var	bool
     */
    protected $_migration_auto_latest = FALSE;

    /**
     * Migration basename regex
     *
     * @var bool
     */
    protected $_migration_regex = NULL;

    /**
     * Error message
     *
     * @var string
     */
    protected $_error_string = '';

    /**
     * Initialize Migration Class
     *
     * @param	array	$config
     * @return	void
     */
    public function __construct($config = array())
    {
        // Only run this constructor on main library load
        if ( ! in_array(get_class($this), array('CI_Migration', config_item('subclass_prefix').'Migration'), TRUE))
        {
            return;
        }

        foreach ($config as $key => $val)
        {
            $this->{'_'.$key} = $val;
        }

        log_message('debug', 'Migrations class initialized');

        // Are they trying to use migrations while it is disabled?
        if ($this->_migration_enabled !== TRUE)
        {
            show_error('Migrations has been loaded but is disabled or set up incorrectly.');
        }

        // If not set, set it
        count($this->_migration_paths) OR $this->_migration_paths = array(APPPATH.'database/migrations/');

        // Add trailing slash if not set
        foreach ($this->_migration_paths as $alias => $path) {
            $this->_migration_paths[$alias] = rtrim($this->_migration_paths[$alias], '/') . '/';
        }

        // Load migration language
        $this->lang->load('migration');

        // They'll probably be using dbforge
        $this->load->dbforge();

        // Make sure the migration table name was set.
        if (empty($this->_migration_table))
        {
            show_error('Migrations configuration file (migration.php) must have "migration_table" set.');
        }

        // Migration basename regex
        $this->_migration_regex = ($this->_migration_type === 'timestamp')
            ? '/^\d{14}_(\w+)$/'
            : '/^\d{3}_(\w+)$/';

        // Make sure a valid migration numbering type was set.
        if ( ! in_array($this->_migration_type, array('sequential', 'timestamp')))
        {
            show_error('An invalid migration numbering type was specified: '.$this->_migration_type);
        }

        // If the migrations table is missing, make it
        if ( ! $this->db->table_exists($this->_migration_table))
        {
            $this->dbforge->add_field(array(
                'version' => array('type' => 'BIGINT', 'constraint' => 20),
                'alias' => array('type' => 'VARCHAR', 'constraint' => 255),
                'ondate'  => array('type' => 'DATETIME')
            ));

            $this->dbforge->add_key('alias');

            $this->dbforge->create_table($this->_migration_table, TRUE);
        }

        // Do we auto migrate to the latest migration?
        if ($this->_migration_auto_latest === TRUE && ! $this->latest())
        {
            show_error($this->error_string());
        }

    }

    // --------------------------------------------------------------------

    /**
     * Migrate to a schema version
     *
     * Calls each migration step required to get to the schema version of
     * choice
     *
     * @param string $type  Any key from _migration_paths, or {module_name}
     * @param	string	$target_version	Target schema version
     *
     * @return	mixed	TRUE if already latest, FALSE if failed, string if upgraded
     */
    public function version($type='all', $target_version)
    {
        // Note: We use strings, so that timestamp versions work on 32-bit systems
        $current_version = $this->get_version($type);

        if ($this->_migration_type === 'sequential')
        {
            $target_version = sprintf('%03d', $target_version);
        }
        else
        {
            $target_version = (string) $target_version;
        }

        $migrations = $this->find_migrations($type);

        if ($target_version > 0 && ! isset($migrations[$target_version]))
        {
            $this->_error_string = sprintf($this->lang->line('migration_not_found'), $target_version);
            return FALSE;
        }

        if ($target_version > $current_version)
        {
            // Moving Up
            $method = 'up';
        }
        else
        {
            // Moving Down, apply in reverse order
            $method = 'down';
            krsort($migrations);
        }

        if (empty($migrations))
        {
            return TRUE;
        }

        $previous = FALSE;

        // Validate all available migrations, and run the ones within our target range
        foreach ($migrations as $number => $file)
        {
            // Check for sequence gaps
            if ($this->_migration_type === 'sequential' && $previous !== FALSE && abs($number - $previous) > 1)
            {
                $this->_error_string = sprintf($this->lang->line('migration_sequence_gap'), $number);
                return FALSE;
            }

            include_once($file);
            $class = 'Migration_'.ucfirst(strtolower($this->_get_migration_name(basename($file, '.php'))));

            // Validate the migration file structure
            if ( ! class_exists($class, FALSE))
            {
                $this->_error_string = sprintf($this->lang->line('migration_class_doesnt_exist'), $class);
                return FALSE;
            }

            $previous = $number;

            // Run migrations that are inside the target range
            if (
                ($method === 'up'   && $number > $current_version && $number <= $target_version) OR
                ($method === 'down' && $number <= $current_version && $number > $target_version)
            )
            {
                $instance = new $class();
                if ( ! is_callable(array($instance, $method)))
                {
                    $this->_error_string = sprintf($this->lang->line('migration_missing_'.$method.'_method'), $class);
                    return FALSE;
                }

                log_message('debug', 'Migrating '.$method.' from version '.$current_version.' to version '.$number);
                call_user_func(array($instance, $method));
                $current_version = $number;
                $this->_update_version($type, $current_version);
            }
        }

        // This is necessary when moving down, since the the last migration applied
        // will be the down() method for the next migration up from the target
        if ($current_version <> $target_version)
        {
            $current_version = $target_version;
            $this->_update_version($type, $current_version);
        }

        log_message('debug', 'Finished migrating to '.$current_version);

        return $current_version;
    }

    // --------------------------------------------------------------------

    /**
     * Sets the schema to the latest migration
     *
     * @param string $type  Any key from _migration_paths, or {module_name}
     *
     * @return	mixed	TRUE if already latest, FALSE if failed, string if upgraded
     */
    public function latest($type='app')
    {
        $last_migration = $this->get_latest($type);

        // Calculate the last migration step from existing migration
        // filenames and proceed to the standard version migration
        return $this->version($type, $this->_get_migration_number($last_migration));
    }

    // --------------------------------------------------------------------

    /**
     * Retrieves the latest migration version available.
     *
     * @param string $type  Any key from _migration_paths, or {module_name}
     *
     * @return bool|string
     */
    public function get_latest($type='app')
    {
        $migrations = $this->find_migrations($type);

        if (empty($migrations))
        {
            $this->_error_string = $this->lang->line('migration_none_found');
            return FALSE;
        }

        return basename(end($migrations));
    }

    //--------------------------------------------------------------------



    /**
     * Sets the schema to the migration version set in config
     *
     * @return	mixed	TRUE if already current, FALSE if failed, string if upgraded
     */
    public function current()
    {
        return $this->version($this->_migration_version);
    }

    // --------------------------------------------------------------------

    /**
     * Error string
     *
     * @return	string	Error message returned as a string
     */
    public function error_string()
    {
        return $this->_error_string;
    }

    // --------------------------------------------------------------------

    /**
     * Retrieves list of available migration scripts
     *
     * @return	array	list of migration file paths sorted by version
     */
    public function find_migrations($type='app')
    {
        $migrations = array();

        $path = $this->determine_migration_path($type);

        // Load all *_*.php files in the migrations path
        foreach (glob($path.'*_*.php') as $file)
        {
            $name = basename($file, '.php');

            // Filter out non-migration files
            if (preg_match($this->_migration_regex, $name))
            {
                $number = $this->_get_migration_number($name);

                // There cannot be duplicate migration numbers
                if (isset($migrations[$number]))
                {
                    $this->_error_string = sprintf($this->lang->line('migration_multiple_version'), $number);
                    show_error($this->_error_string);
                }

                $migrations[$number] = $file;
            }
        }

        ksort($migrations);
        return $migrations;
    }

    // --------------------------------------------------------------------

    /**
     * Retrieves current schema version
     *
     * @return	string	Current migration version
     */
    public function get_version($type='app')
    {
        $row = $this->db->select('version')
                        ->where('alias', $type)
                        ->get($this->_migration_table)
                        ->row();

        return $row ? $row->version : '0';
    }

    // --------------------------------------------------------------------

    /**
     * Given the string for the name of the file, will
     * generate the rest of the filename based on the current
     * $config['migration_type'] setting.
     *
     * @param $name
     * @return string The final name (with extension)
     */
    public function make_name($name)
    {
        if (empty($name))
        {
            return null;
        }

        if ($this->_migration_type == 'timestamp')
        {
            $prefix = date('YmdHis');
        }
        else
        {
            $prefix = str_pad($this->get_version() + 1, 3, '0', STR_PAD_LEFT);
        }

        return $prefix .'_'. ucfirst(strtolower($name)) .'.php';
    }

    //--------------------------------------------------------------------

    /**
     * Enable the use of CI super-global
     *
     * @param	string	$var
     * @return	mixed
     */
    public function __get($var)
    {
        return get_instance()->$var;
    }

    //--------------------------------------------------------------------


	/**
	 * Based on the 'type', determines the correct migration path.
	 *
	 * @param $type
	 * @param bool $create
	 *
	 * @return null|string
	 */
    public function determine_migration_path($type, $create=false)
    {
        $type = strtolower($type);

        // Is it a module?
        if (strpos($type, 'mod:') === 0)
        {
            $module = str_replace('mod:', '', $type);

            $path = \Myth\Modules::path($module, 'migrations');

	        // Should we return a 'created' module?
	        // Use the first module path.
	        if (empty($path) && $create === true)
	        {
				$folders = config_item('modules_locations');

		        if (is_array($folders) && count($folders))
		        {
			        $path = $folders[0] . $module .'/migrations';
		        }
	        }

            return rtrim($path, '/') .'/';
        }

        // Look in our predefined groups.
        if (! empty($this->_migration_paths[$type]))
        {
            return rtrim($this->_migration_paths[$type], '/') .'/';
        }

        return null;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the default migration path. This is basically the first
     * path in the migration_paths array.
     *
     * @return string
     */
    public function default_migration_path()
    {
        return key($this->_migration_paths);
    }

    //--------------------------------------------------------------------


    //--------------------------------------------------------------------
    // Protected Methods
    //--------------------------------------------------------------------

    /**
     * Extracts the migration number from a filename
     *
     * @param	string	$migration
     * @return	string	Numeric portion of a migration filename
     */
    protected function _get_migration_number($migration)
    {
        return sscanf($migration, '%[0-9]+', $number)
            ? $number : '0';
    }

    // --------------------------------------------------------------------

    /**
     * Extracts the migration class name from a filename
     *
     * @param	string	$migration
     * @return	string	text portion of a migration filename
     */
    protected function _get_migration_name($migration)
    {
        $parts = explode('_', $migration);
        array_shift($parts);
        return implode('_', $parts);
    }

    // --------------------------------------------------------------------

    /**
     * Stores the current schema version
     *
     * @param   string  $type  Any key from _migration_paths, or {module_name}
     * @param	string	$migration	Migration reached
     * @return	mixed	Outputs a report of the migration
     */
    protected function _update_version($type='all', $migration)
    {
        $this->db->where('alias', $type)
                 ->delete($this->_migration_table);

        return $this->db->insert($this->_migration_table, array(
            'version'   => $migration,
            'alias'     => $type,
            'ondate'    => date('Y-m-d H:i:s')
        ));
    }

    // --------------------------------------------------------------------

}

<?php

/**
 * We are gradually refactoring the moving parts of LESS and Minify support into
 * this class from aHelper where they are too hard to reuse in different contexts
 */
 
class BaseaAssets
{
	static public $lessc = null;
	
	static protected $cache;

  /**
   * Access to an sfCache used to leverage the excellent dependency caching capabilities of lessphp
   * @param string $key
   * @return mixed
   */
  static public function getCached($key)
  {
    $cache = aAssets::getCache();
    $value = $cache->get($key, null);
    if ($value === null)
    {
      return null;
    }
    return unserialize($value);
  }

  /**
   * Interval (lifetime) is in seconds, a full ten days is fine here,
   * it could be indefinite really
   * @param string $key
   * @param mixed $value
   * @param mixed $interval
   */
  static public function setCached($key, $value, $interval=864000)
  {
    $cache = aAssets::getCache();
    $cache->set($key, serialize($value), $interval);
  }

  /**
   * DOCUMENT ME
   * @return mixed
   */
  static public function getCache()
  {
    if (aAssets::$cache)
    {
      return aAssets::$cache;
    }
    $cacheClass = sfConfig::get('app_a_less_cache_class', 'sfFileCache');
    aAssets::$cache = new $cacheClass(sfConfig::get('app_a_less_cache_options', array('cache_dir' => aFiles::getWritableDataFolder(array('a_less_cache')))));
    return aAssets::$cache;
  }
  
  /**
   * Compiles the less source file $path to the CSS file $compiled unless it is up to date
   * according to the high quality dependency checking provided by lessphp
   */
  static public function compileLessIfNeeded($path, $compiled, $options = array())
  {
	  // In production just return if the final compiled file exists.
	  // This is good enough in production because we symfony cc on deployment.
	  // Unserializing the cache file is way faster than recompiling less code
	  // but it's nowhere near instant as jeremy pointed out
	  if (!sfConfig::get('app_a_less_check_dependencies', true))
	  {
  	  if (file_exists($compiled))
  	  {
        return;
  	  }
	  }

	  // Leverage the considerable caching abilities of lessphp directly.
	  // it's smart enough to pay attention to whether imported files
	  // were cached or not, and we're not that smart

	  
    if (!isset(aAssets::$lessc))
    {
      // We do it like factories.yml does it, defaulting to the built in lessc.
      // this is a nice injection point because it's common to subclass lessc
      // The regular lessc class constructor doesn't take useful constructor parameters
      // as far as we're concerned, it doesn't even use its second argument '$opts'.
      // But you can write subclasses that take a useful options array as their
      // first argument
      $factory = sfConfig::get('app_a_lessc', array('class' => 'lessc', 'param' => null));
      $class = $factory['class'];
      $param = $factory['param'];
      aAssets::$lessc = new $class($param);
  		// set a new import directory in app.yml if you want to change our imported files, like a-helpers.less
    }
		// importDir must be reset for each file since they may be in different folders
		$importDir = sfConfig::get('app_a_less_import_directory', dirname($path) . '/');
    aAssets::$lessc->importDir = $importDir;
    
    // The cache key should be the compiled css filename, not the original less filename, because
    // if we choose to compile the same less file to two different places we'll be misled by the
    // cache into thinking both destination files already exist. An awesomer solution might be to
    // keep the compiled css in the cache, but that would slow us down in the more common case where
    // there is only one destination CSS file per source less file
    $info = aAssets::getCached($compiled);
    if (is_null($info))
    {
      $info = $path;
    }
    $lastUpdated = is_array($info) ? $info['updated'] : 0;
    $info = aAssets::cexecute($info, false);
		// Our replacement for cexecute() calls parse() on the contents of a file, so
		// the cache info structure is missing the name of the original requested file.
		// Add that back in so the dependency checking works for the file itself
		if (!isset($info['files'][$path]))
		{
			$info['files'][$path] = filemtime($path);
		}
    if ($info['updated'] !== $lastUpdated)
    {
      // Copy it to our asset cache folder since it has changed
      file_put_contents($compiled, $info['compiled']);
    }
		unset($info['compiled']);
    aAssets::setCached($compiled, $info);
  }
  
  /**
   * @param string $file The name of the file
   * Basename seems wrong, but it's consistent with pathinfo() (http://us2.php.net/pathinfo), which uses filename to refer
   * to the basename without the extension.
   * @return string A unique name for the compiled version of $file
   */
  public static function getLessBasename($file)
  {
    $name = md5($file) . '.less.css';
    if (!sfConfig::get('app_a_minify', false))
    {
      // In dev environments let the developer figure out what the original filename was
      $slug = aTools::slugify($file);
      $name = $slug . '-' . $name;
    }
    return $name;
  }
  
  /**
   * @param array $files An array of paths to assets to include
   * @return string A unique name based on the paths of $files
   */
  public static function getGroupFilename($files)
  {
    // If your CSS files depend on clever aliases that won't work
    // through the filesystem, we can get them by http. We're caching
    // so that's not terrible, but it's usually simpler faster and less
    // buggy to grab the file content.
    // I tried just using $groupFilename as is (after stripping dangerous stuff) 
    // but it's too long for the OS if you include enough to make it unique
    return md5(implode('', $files));    
  }
  
  public static function clearAssetCache(sfFilesystem $fileSystem)
  {
    $assetDir = aFiles::getUploadFolder(array('asset-cache'));
    $fileSystem->remove(sfFinder::type('file')->in($assetDir));
    $cache = aAssets::getCache();
    $cache->clean();
  }
  
  /**
   *
   * Borrowed from lessphp because the original version does not allow
   * dependency injection of the lessc compiler class name. This way
   * we get to use our single existing lessc object, which is faster, and
   * also construct it with a custom class name and parameters
   *
	 * Execute lessphp on a .less file or a lessphp cache structure
	 * 
	 * The lessphp cache structure contains information about a specific
	 * less file having been parsed. It can be used as a hint for future
	 * calls to determine whether or not a rebuild is required.
	 * 
	 * The cache structure contains two important keys that may be used
	 * externally:
	 * 
	 * compiled: The final compiled CSS
	 * updated: The time (in seconds) the CSS was last compiled
	 * 
	 * The cache structure is a plain-ol' PHP associative array and can
	 * be serialized and unserialized without a hitch.
	 * 
	 * @param mixed $in Input
	 * @param bool $force Force rebuild?
	 * @return array lessphp cache structure
	 */
	public static function cexecute($in, $force = false) 
	{
		// assume no root
		$root = null;

		if (is_string($in)) {
			$root = $in;
		} elseif (is_array($in) and isset($in['root'])) {
			if ($force or ! isset($in['files'])) {
				// If we are forcing a recompile or if for some reason the
				// structure does not contain any file information we should
				// specify the root to trigger a rebuild.
				$root = $in['root'];
			} elseif (isset($in['files']) and is_array($in['files'])) {
				foreach ($in['files'] as $fname => $ftime ) {
					if (!file_exists($fname) or filemtime($fname) > $ftime) {
						// One of the files we knew about previously has changed
						// so we should look at our incoming root again.
						$root = $in['root'];
						break;
					}
				}
			}
		} else {
			// TODO: Throw an exception? We got neither a string nor something
			// that looks like a compatible lessphp cache structure.
			return null;
		}

		if ($root !== null) {
			// If we have a root value which means we should rebuild.
			$less = aAssets::$lessc;
			$out = array();
			$out['root'] = $root;
			$out['compiled'] = $less->parse(file_get_contents($root));
			$out['files'] = $less->allParsedFiles();
			$out['updated'] = time();
			return $out;
		} else {
			// No changes, pass back the structure
			// we were given initially.
			return $in;
		}

	}
}

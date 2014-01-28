<?php namespace Jinggo\Minify;

/**
* Minify
*
* @uses     
*
* @category Category
* @package  Package
*/
class Minify {

    /**
     * $files
     *
     * @var mixed
     *
     * @access protected
     */
	protected $files;

    /**
     * $buildpath
     *
     * @var mixed
     *
     * @access protected
     */
	protected $buildpath;

    /**
     * $path
     *
     * @var mixed
     *
     * @access protected
     */
	protected $path;

	/**
	 * $css3
	 * 
	 * @var mixed
	 * 
	 * @access protected
	 */
	protected $css3;

	/**
	 * $browsers
	 * 
	 * @var mixed
	 * 
	 * @access protected
	 */
	protected $browsers;

	/**
	 * $agents
	 * 
	 * @var mixed
	 * 
	 * @access protected
	 */
	protected $agents;

	public function __construct()
	{
		

		$this->browsers = \Config::get('minify::css3_browsers');

		$this->agents = $this->getBrowser();

		$this->css3 = true;
	}

    /**
     * minifyCss
     * 
     * @param mixed $files Description.
     *
     * @access public
     * @return mixed Value.
     */
	private function minifyCss($files)
	{
		$this->files = $files;
	
		$this->createBuildPath();	
				
		$totalmod = $this->doFilesExistReturnModified();

		$filename = md5(str_replace('.css', '', implode('-', $this->files)) . '-' . $totalmod).'.css';
		$output = $this->buildpath . $filename;

		if ( \File::exists($output) ) {
			return $this->absoluteToRelative($output);
		}

		$all = $this->appendAllFiles();	
		$result = \CssMin::minify($all);		
		// $this->cleanPreviousFiles($this->buildpath, $filename);

		\File::put($output, $result);

		return $this->absoluteToRelative($output);
	}

	/**
	 * processFiles
	 * 
	 * @param mixed $type (css or js), $files array of files
	 * 
	 * @access public
	 * @return array 
	 */
	public function process($type,$id)
	{
		$this->path = public_path() . \Config::get("minify::{$type}_path");	
		$this->buildpath = $this->path . \Config::get("minify::{$type}_build_path");

		$user_agent = $this->getBrowser();

		if($type === "css")
		{
			$fconfig = \Config::get('minify::css');
		}
		else if($type === "js")
		{
			$fconfig = \Config::get('minify::js');
		}

		if(!isset($fconfig[$id])) 
		{
			return "Resource $id not found";
		}

		$files 	= $fconfig[$id];

		$file 	= array();

		foreach($files as $f)
		{
			
			if(! \File::exists($this->path.$f)) continue;

			$file[] = $f;

			if($type === "css")
			{
				if($this->css3)
				{
					$target = str_replace(".$type", ".css3.".$type, $f);

					if(! \File::exists($this->path.$target)) continue;
					
					$file[] = $target;
				}
			}
		}


		if($type === "css")
		{
			return $this->minifyCss($file);
		}
		else if($type === "js")
		{
			
			return $this->minifyJs($file);
		}

	}

  	/**
     * minifyJs
     * 
     * @param mixed $files Description.
     *
     * @access public
     * @return mixed Value.
     */
	public function minifyJs($files)
	{		
		$this->files = $files;

		$this->createBuildPath();	
				
		$totalmod = $this->doFilesExistReturnModified();

		$filename = md5(str_replace('.js', '', implode('-', $this->files)) . '-' . $totalmod).'.js';
		$output = $this->buildpath . $filename;

		if ( \File::exists($output) ) {
			return $this->absoluteToRelative($output);
		}
		
		$all = $this->appendAllFiles();	
		$result = \JSMin::minify($all);		
		// $this->cleanPreviousFiles($this->buildpath, $filename);

		\File::put($output, $result);

		return $this->absoluteToRelative($output);
	}

    /**
     * createBuildPath
     * 
     * @access private
     * @return mixed Value.
     */
	private function createBuildPath()
	{		
		if ( ! \File::isDirectory($this->buildpath) )
		{
			\File::makeDirectory($this->buildpath);
		}
	}

    /**
     * cleanPreviousFiles
     * 
     * @access private
     * @return mixed Value.
     */
	private function cleanPreviousFiles($dir, $filename)
	{
		$ext = \File::extension($filename);
		$filename = preg_replace('/[a-f0-9]{32,40}/','', $filename);
		$filename = str_replace('-.' . $ext, '', $filename);

		foreach (\File::files($dir) as $file)
		{
			if ( strpos($file, $filename) !== FALSE ) {
				\File::delete($file);
			}
		}
	}
    /**
     * absoluteToRelative
     * 
     * @param mixed $url Description.
     *
     * @access private
     * @return mixed Value.
     */
	private function absoluteToRelative($url)
	{
		return \URL::asset(str_replace(public_path(), '', $url));		
	}

    /**
     * appendAllFiles
     * 
     * @access private
     * @return mixed Value.
     */
	private function appendAllFiles()
	{		
		$all = '';
		foreach ($this->files as $file)
			$all .= \File::get($this->path . $file);

		if ( ! $all )
			throw new Exception;

		return $all;
	}
    /**
     * doFilesExistReturnModified
     * 
     * @access private
     * @return mixed Value.
     */
	private function doFilesExistReturnModified()
	{
		if (!is_array($this->files))
			$this->files = array($this->files);
	
		$filetime = 0;
				
		foreach ($this->files as $file) {
			$absolutefile = $this->path . $file;

			if ( ! \File::exists($absolutefile)) {			
				throw new \Exception;
			}

			$filetime += \File::lastModified($absolutefile);

		}

		return $filetime;
	}

	public function getBrowser() 
    { 
        $u_agent = $_SERVER['HTTP_USER_AGENT']; 
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";
        $ub = "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        
        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Internet Explorer'; 
            $ub = "MSIE"; 
        } 
        elseif(preg_match('/Firefox/i',$u_agent)) 
        { 
            $bname = 'Mozilla Firefox'; 
            $ub = "Firefox"; 
        } 
        elseif(preg_match('/Chrome/i',$u_agent)) 
        { 
            $bname = 'Google Chrome'; 
            $ub = "Chrome"; 
        } 
        elseif(preg_match('/Safari/i',$u_agent)) 
        { 
            $bname = 'Apple Safari'; 
            $ub = "Safari"; 
        } 
        elseif(preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Opera'; 
            $ub = "Opera"; 
        } 
        elseif(preg_match('/Netscape/i',$u_agent)) 
        { 
            $bname = 'Netscape'; 
            $ub = "Netscape"; 
        } 
        
        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
        
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }
        
        // check if we have a number
        if ($version==null || $version=="") {$version="?";}
        
        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform
            //'pattern'    => $pattern
        );
    }

    public function restrictBrowser($browser,$version)
    {
    	$cbrowser = $this->getBrowser();

    	$restricted = array("Internet Explorer");

    	if(in_array($browser,$restricted))
    	{
    		if(isset($cbrowser['name']) && $cbrowser['name'] === $browser)
    		{
    			if(isset($cbrowser['version']) && $cbrowser['version'] <= $version)
    			{
    				return false;
    			}
    		}
    	}

    	return true;
    }

}
<?php

class Dir extends Base implements IteratorAggregate
{
    private $_values = array();

    function __construct($path,$pattern='*')
    {
        if(substr($path, -1) != "/")    $path .= "/";
        $this->listFile($path,$pattern);
    }

    function listFile($pathname,$pattern='*')
    {
        static $_listDirs = array();
        $guid   =   md5($pathname.$pattern);
        if(!isset($_listDirs[$guid])){
            $dir = array();
            $list   =   glob($pathname.$pattern);
            foreach ($list as $i=>$file){
                    $dir[$i]['filename']    = basename($file);
                    $dir[$i]['pathname']    = realpath($file);
                    $dir[$i]['owner']        = fileowner($file);
                    $dir[$i]['perms']        = fileperms($file);
                    $dir[$i]['inode']        = fileinode($file);
                    $dir[$i]['group']        = filegroup($file);
                    $dir[$i]['path']        = dirname($file);
                    $dir[$i]['atime']        = fileatime($file);
                    $dir[$i]['ctime']        = filectime($file);
                    $dir[$i]['size']        = filesize($file);
                    $dir[$i]['type']        = filetype($file);
                    $dir[$i]['ext']      =  is_file($file)?strtolower(substr(strrchr(basename($file), '.'),1)):'';
                    $dir[$i]['mtime']        = filemtime($file);
                    $dir[$i]['isDir']        = is_dir($file);
                    $dir[$i]['isFile']        = is_file($file);
                    $dir[$i]['isLink']        = is_link($file);
                    //$dir[$i]['isExecutable']= function_exists('is_executable')?is_executable($file):'';
                    $dir[$i]['isReadable']    = is_readable($file);
                    $dir[$i]['isWritable']    = is_writable($file);
            }
            $cmp_func = create_function('$a,$b','
            $k  =  "isDir";
            if($a[$k]  ==  $b[$k])  return  0;
            return  $a[$k]>$b[$k]?-1:1;
            ');

            usort($dir,$cmp_func);
            $this->_values = $dir;
            $_listDirs[$guid] = $dir;
        }else{
            $this->_values = $_listDirs[$guid];
        }
    }

    function getATime()
    {
        $current = current($this->_values);
        return $current['atime'];
    }

    function getCTime()
    {
        $current = current($this->_values);
        return $current['ctime'];
    }

    function getChildren()
    {
        $current = current($this->_values);
        if($current['isDir']){
            return new Dir($current['pathname']);
        }
        return false;
    }

    function getFilename()
    {
        $current = current($this->_values);
        return $current['filename'];
    }

    function getGroup()
    {
        $current = current($this->_values);
        return $current['group'];
    }

    function getInode()
    {
        $current = current($this->_values);
        return $current['inode'];
    }

    function getMTime()
    {
        $current = current($this->_values);
        return $current['mtime'];
    }

    function getOwner()
    {
        $current = current($this->_values);
        return $current['owner'];
    }

    function getPath()
    {
        $current = current($this->_values);
        return $current['path'];
    }

    function getPathname()
    {
        $current = current($this->_values);
        return $current['pathname'];
    }

    function getPerms()
    {
        $current = current($this->_values);
        return $current['perms'];
    }

    function getSize()
    {
        $current = current($this->_values);
        return $current['size'];
    }

    function getType()
    {
        $current = current($this->_values);
        return $current['type'];
    }

    function isDir()
    {
        $current = current($this->_values);
        return $current['isDir'];
    }

    function isFile()
    {
        $current = current($this->_values);
        return $current['isFile'];
    }

    function isLink()
    {
        $current = current($this->_values);
        return $current['isLink'];
    }

    function isExecutable()
    {
        $current = current($this->_values);
        return $current['isExecutable'];
    }

    function isReadable()
    {
        $current = current($this->_values);
        return $current['isReadable'];
    }

    function getIterator()
    {
         return new ArrayObject($this->_values);
    }

    function toArray() {
        return $this->_values;
    }

    function isEmpty($directory)
    {
        $handle = opendir($directory);
        while (($file = readdir($handle)) !== false)
        {
            if ($file != "." && $file != "..")
            {
                closedir($handle);
                return false;
            }
        }
        closedir($handle);
        return true;
    }

    function getList($directory)
    {
        return scandir($directory);
    }

    function delDir($directory,$subdir=true)
    {
        if (is_dir($directory) == false)
        {
            exit("The Directory Is Not Exist!");
        }
        $handle = opendir($directory);
        while (($file = readdir($handle)) !== false)
        {
            if ($file != "." && $file != "..")
            {
            is_dir("$directory/$file")?
                Dir::delDir("$directory/$file"):
                unlink("$directory/$file");
            }
        }
        if (readdir($handle) == false)
        {
            closedir($handle);
            rmdir($directory);
        }
    }

    function del($directory)
    {
        if (is_dir($directory) == false)
        {
            exit("The Directory Is Not Exist!");
        }
        $handle = opendir($directory);
        while (($file = readdir($handle)) !== false)
        {
            if ($file != "." && $file != ".." && is_file("$directory/$file"))
            {
                unlink("$directory/$file");
            }
        }
        closedir($handle);
    }

    function copyDir($source, $destination)
    {
        if (is_dir($source) == false)
        {
            exit("The Source Directory Is Not Exist!");
        }
        if (is_dir($destination) == false)
        {
            mkdir($destination, 0700);
        }
        $handle=opendir($source);
        while (false !== ($file = readdir($handle)))
        {
            if ($file != "." && $file != "..")
            {
                is_dir("$source/$file")?
                Dir::copyDir("$source/$file", "$destination/$file"):
                copy("$source/$file", "$destination/$file");
            }
        }
        closedir($handle);
    }

}

if(!class_exists('DirectoryIterator')) {
    class DirectoryIterator extends Dir {}
}

?>
<?php
namespace Res\Hook\Autoloader;

/**
* psr-4 style autoloader
*/
class Autoloader
{
    /**
     * psr-4 autoload namespace map array
     * @var array
     */
    protected $psr4 = [];

    public function initialize(\Res\Config\Autoload $config)
    {
        $this->psr4 = $config->psr4;
        unset($config);
    }

    public function register()
    {
        if (empty($this->psr4)) {
            throw new \Exception('The psr-4 mapping array can not be empty');
        }
        spl_autoload_register([$this, 'loadClass'], true, true);
    }

    public function loadClass(string $class)
    {
        $class = trim($class, '\\');

        $this->loadClassInNamespace($class);
    }

    protected function loadClassInNamespace(string $class) : bool
    {
        $prefix = substr($class, 0, strrpos($class, '\\'));

        if (isset($this->psr4[$prefix])) {
            if (is_string($this->psr4[$prefix])) {
                $this->psr4[$prefix] = [$this->psr4[$prefix]];
            }
            $class = substr($class, strlen($prefix));
            return $this->loadFile($this->psr4[$prefix], $class);
        }

        // is sub-namespace ?
        foreach ($this->psr4 as $namespace => $directories) {
            if (0 != strpos($prefix, $namespace)) {
                continue;
            }
            if (!is_array($directories)) {
                $directories = [$directories];
            }
            $name = substr($class, strlen($namespace));
            $map = $this->loadFile($directories, $name);
            if ($map) {
                return $map;
            }
        }
        return false;
    }

    protected function loadFile(array $paths, string $class) : bool
    {
        foreach ($paths as $path) {
            $file = $this->resolvePath("{$path}{$class}.php");
            if (file_exists($file)) {
                require $file;
                return true;
            }
            return false;
        }
    }

    protected function resolvePath(string $path) : string
    {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        if (DIRECTORY_SEPARATOR == $path[0]) {
            $prefix = '';
        }
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $realPath = [];
        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            } elseif ('..' == $part) {
                array_pop($realPath);
            } else {
                $realPath[] = $part;
            }
        }

        isset($prefix) && array_unshift($realPath, $prefix);
        return implode(DIRECTORY_SEPARATOR, $realPath);
    }
}

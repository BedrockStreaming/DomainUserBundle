<?php


namespace M6Web\Bundle\DomainUserBundle\Cache;

use M6Web\Bundle\DomainUserBundle\User\UserProvider;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;
use Symfony\Component\Finder\Finder;

class Warmup extends CacheWarmer
{
    const CACHEFILE = 'DomainUserConfig.php';

    /**
     * @var UserProvider;
     */
    protected $userProvider;

    /**
     * @var string
     */
    protected $userdir;

    /**
     * {@inheritDoc}
     *
     */
    public function warmUp($cacheDir)
    {
        if (!is_dir($this->userdir)) {
            throw new \InvalidArgumentException('userdir is not a directory : '.print_r($this->userdir, true).'given');
        }

        $finder = new Finder();
        $finder->files()->in($this->userdir)->name('*.yml');
        $users = [];
        foreach($finder as $file) {
            $user = basename($file, '.yml');
            $export_user = var_export($this->userProvider->getUserByUserName($user), true);
            $code = <<<EOF
<?php

/**
* This code has been auto-generated
* by the M6Web Domain User Bundle
* 
*/

return \$user = {$export_user};

EOF;
            $this->writeCacheFile(self::getCachePath($cacheDir, $user), $code);
        }
    }

    public static function getCachePath($cacheDir, $username)
    {
        return $cacheDir.'/DomainUserConfig_'.$username.'.php';
    }

    /**
     * @param string $dir
     *
     * @return $this
     */
    public function setUserDir($dir)
    {
        $this->userdir = $dir;
        return $this;
    }

    /**
     * @param $p UserProvider
     *
     * @return $this
     */
    public function setUserProvider($p)
    {
        $this->userProvider = $p;
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function isOptional()
    {
        return false; // this warmup is not optionnal
    }


}

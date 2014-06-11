<?php
/**
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2010-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license          LGPL Lesser General Public License <http://www.gnu.org/copyleft/lesser.html>
 * @link             http://hizup.com
 */

class H2OTpl
{

    const
    NAME = 'H2OTpl',
    AUTHOR = 'Pierre-Henry Soria',
    VERSION = '2.0';

    private
    $_sTplDirFile,
    $_sCacheDirFile,
    $_sTplDir,
    $_sCacheDir,
    $_sEncoding = 'utf-8', // See here: http://en.wikipedia.org/wiki/Character_encoding;
    $_sTplExt = '.tpl.php',
    $_sCacheExt = '.cache.php',
    $_bAutoEscape = true,
    $_bHtmlCompress = true,
    $_bCaching = true,
    $_iExpire = 3600;

    public function setTplDir($sPath)
    {
        if (is_dir($sPath))
            $this->_sTplDir = $sPath;
        else
            throw new \Exception('Template Path is not found: ' . $sPath);
    }

    public function setCacheDir($sPath)
    {
        if (is_dir($sPath))
            $this->_sCacheDir = $sPath;
        else
            throw new \Exception('Template Cache Path is not found: ' . $sPath);
    }

    public function setTplExt($sExt)
    {
        if (false !== strpos($sExt, '.'))
            $this->_sTplExt = $sExt;
        else
            throw new \Exception('Template Extension must have a dot. e.g., ".php"' . $sExt);
    }

    public function setCacheExt($sExt)
    {
        if (false !== strpos($sExt, '.'))
            $this->_sCacheExt = $sExt;
        else
            throw new \Exception('Template Cache Extension must have a dot. e.g., ".php"' . $sExt);
    }

    public function setEncoding($sEncoding)
    {
        $this->_sEncoding = $sEncoding;
    }

    public function setAutoEscape($bAuto)
    {
        $this->_bAutoEscape = (bool) $bAuto;
    }

    public function setHtmlCompress($bCompress)
    {
        $this->_bHtmlCompress = (bool) $bCompress;
    }

    public function setCaching($bCaching)
    {
        $this->_bCaching = (bool) $bCaching;
    }

    public function setCacheExpire($iLifeTime)
    {
        $this->_iExpire = (int) $iLifeTime;
    }

    public function __set($sKey, $mVal)
    {
        // Protect string variables if the automatic escape is enabled
        if ($this->_bAutoEscape && is_string($mVal))
            $mVal = $this->escape($mVal);

        $this->$sKey = $mVal;
    }

    public function escape($sVal)
    {
        return htmlspecialchars($sVal, ENT_QUOTES, $this->_sEncoding);
    }

    public function display($sFile)
    {
        $sFile .= $this->_sTplExt;
        $this->_checkTplDir();
        $this->_sTplDirFile = $this->_sTplDir . $sFile;
        if (file_exists($this->_sTplDirFile))
        {
            if ($this->_bCaching)
                $this->cache($sFile);
            else
                include_once ($this->_sTplDirFile);
        }
        else
        {
             exit('<p style="color:#FF0000;text-align:center"><b>ERROR : The template file &quot; ' . $this->_sTplDirFile . ' &quot; is not found!</b></p>');
        }
    }

    private function cache ($sFile)
    {
        $this->_checkCacheDir();
        $this->_sCacheDirFile = $this->_sCacheDir . substr($sFile, 0, strpos($sFile, '.')) . $this->_sCacheExt;
        ob_start();
        include($this->_sTplDirFile);
        $sOutput = ob_get_contents();
        ob_end_clean();
        if (is_dir($this->_sCacheDir))
        {
            if (!file_exists($this->_sCacheDirFile) || filemtime($this->_sCacheDirFile) < filemtime($this->_sTplDirFile) || filemtime($this->_sCacheDirFile) > time() - $this->_iExpire)
            {
                if ($this->_bHtmlCompress)
                    $sOutput = $this->_htmlCompress($sOutput);
                $sContent = '<!-- Cached on ' . gmdate('d M Y H:i:s') . ' -->' . "\n" . $sOutput;
                file_put_contents($this->_sCacheDirFile, $sContent);
            }
            readfile($this->_sCacheDirFile);
        }
        else
        {
            exit('<p style="color:#FF0000;text-align:center"><b>ERROR : The cache folder &quot; ' . $this->_sCacheDir . ' &quot; is not found!</b></p>');
        }
    }

    public function clearCache()
    {
        $this->_checkCacheDir();
        if ($rHandle = opendir($this->_sCacheDir))
        {
            while (false !== ($sFile = readdir($rHandle)))
            {
                if (substr($sFile, -10) == $this->_sCacheExt)
                    @unlink($this->_sCacheDir . $sFile);
            }
            closedir($rHandle);
        }
    }

    private function _checkTplDir()
    {
        if (substr($this->_sTplDir,- 1) != '/')
            $this->_sTplDir .= '/';
    }

    private function _checkCacheDir()
    {
        if (substr($this->_sCacheDir,- 1) != '/')
            $this->_sCacheDir .= '/';
    }

    private function _htmlCompress($sContent)
    {
        preg_match_all('!(<(?:code|pre).*>[^<]+</(?:code|pre)>)!', $sContent, $aPre); // Exclude pre or code tags
        $sContent = preg_replace('!<(?:code|pre).*>[^<]+</(?:code|pre)>!', '#pre#', $sContent); // Removing all pre or code tags
        $sContent = preg_replace('#<!–[^\[].+–>#', '', $sContent); // Removing HTML comments
        $sContent = preg_replace('/[\r\n\t]+/', '', $sContent); // Remove new lines, spaces, tabs
        $sContent = preg_replace('/>[\s]+</', '><', $sContent); // Remove new lines, spaces, tabs
        if (!empty($aPre[0]))
            foreach ($aPre[0] as $sTag)
                $sContent = preg_replace('!#pre#!', $sTag, $sContent, 1); // Putting back pre|code tags

        return $sContent;
    }

}

<?php

/**
 * Ein Less-Compiler. Benutzt im Hintergrund die lessphp-Library der less-Extension
 */
class LessCompiler extends CApplicationComponent
{
    /**
     * @var string Alias des Verzeichnisses mit den Less-Dateien
     */
    public $source = '';
    
    /**
     * @var string Alias des Verzeichnisses mit den Css-Dateien
     */
    public $destination = '';
    
    /**
     * @var array Zu kompilierende Dateien
     */
    public $files = array();
    
    /**
     * @var boolean Kompilieren erzwingen?
     */
    public $forceCompile = false;
    
    
    /**
     * Initialisiert den Component und kompiliert alle Dateien
     */
    public function init()
    {
        if($this->forceCompile || $this->checkCompile()) {
            $this->compileAll();
        }
    }
    
    
    /**
     * Kompiliert alle Dateien
     */
    protected function compileAll()
    {
        include_once(Yii::getPathOfAlias('lib.lessphp') . '/lessc.inc.php');
        $parser = new lessc();
        $parser->setImportDir(Yii::getPathOfAlias($this->source));
        
        if (!YII_DEBUG) {
            $parser->setFormatter("compressed");
        }
        
        foreach($this->files as $file) {
            $lessFile = Yii::getPathOfAlias($this->source) . '/' . $file;
            $cssFile = Yii::getPathOfAlias($this->destination) . '/' . $this->getFileName($file) . '.css';
            
            try {
                $parser->compileFile($lessFile, $cssFile);
            } catch (exception $e) {
                throw new CException(__CLASS__.': Failed to compile ' . $lessFile . ' with message: '.$e->getMessage().'.');
            }
        }
    }
    
    /**
     * Prüft, ob sich Dateien geändert haben
     */
    protected function checkCompile()
    {
        // Die am letzten geänderte kompilierte css-Datei im Destination-Verzeichnis suchen
        $destinationLastModified = 0;
        foreach($this->files as $file) {
            $cssFile = Yii::getPathOfAlias($this->destination) . '/' . $this->getFileName($file) . '.css';
            if(is_file($cssFile)) {
                $destinationLastModified = max($destinationLastModified, $this->getLastModified($cssFile));
            } else {// Diese Datei ist gar noch nicht kompilliert
                return true;
            }
        }
        
        // Die am letzten geänderte Datei im Source-Verzeichnis suchen
        $sourceLastModified = 0;
        foreach(scandir(Yii::getPathOfAlias($this->source)) as $file) {
            $lessFile = Yii::getPathOfAlias($this->source) . '/' . $file;
            if(is_file($lessFile)) {
                $sourceLastModified = max($sourceLastModified, $this->getLastModified($lessFile));
            }
        }
        
        return $sourceLastModified > $destinationLastModified;
    }
    
    /**
     * Liefert die letzte Änderung einer Datei
     * @path string der Dateipfad
     */
    protected function getLastModified($path)
    {
        $stat = stat($path);
        return $stat['mtime'];
    }
    
    /**
     * Liefert den Dateinamen (ohne Erwiterung) eines Pfades
     * @path string der Dateipfad
     */
    protected function getFileName($path)
    {
        $path_parts = pathinfo($path);
        return $path_parts['filename'];
    }
}

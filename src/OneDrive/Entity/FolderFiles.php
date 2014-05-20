<?php
namespace OneDrive\Entity;

/**
 * Class FolderFiles
 * @package OneDrive\Entity
 *
 * @property
 */
class FolderFiles {

    /**
     * @var File []
     */
    public $files;

    public $paging;

    public function __construct($filds)
    {
        $this->files = array();
        foreach ($filds['data'] as $file){
            $this->files[] = new File($file);
        }

        if ($filds['paging']){
            foreach ($filds['paging'] as &$page){
                $urlObj = parse_url($page);
                parse_str($urlObj['query'],$page);
            }
            unset($page);
        }
        $this->paging = $filds['paging'];
    }
} 
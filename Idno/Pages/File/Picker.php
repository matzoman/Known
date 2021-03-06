<?php

    /**
     * File picker
     */

    namespace Idno\Pages\File {

        use Idno\Core\Idno;
        use Idno\Files\File;

        class Picker extends \Idno\Common\Page
        {

            function getContent()
            {

                $template = 'file/picker/image';
                $t          = \Idno\Core\Idno::site()->template();
                $t->title   = 'Image picker';
                $t->hidenav = true;
                $t->body    = $t->draw($template);
                echo $t->draw('shell');
            }

            function post()
            {
                if (\Idno\Core\Idno::site()->session()->isLoggedOn()) {
                    if (!empty($_FILES['file']['tmp_name'])) {
                        if (!\Idno\Core\Idno::site()->triggerEvent("file/upload", [], true)) {
                            exit;
                        }
                        if (\Idno\Entities\File::isImage($_FILES['file']['tmp_name'])) {
                            $return = false;
                            $file = false;
                            if ($file = \Idno\Entities\File::createThumbnailFromFile($_FILES['file']['tmp_name'], $_FILES['file']['name'], 1024)) {
                                $return = true;
                                $returnfile = new \stdClass;
                                $returnfile->file = ['_id' => $file];
                                $file = $returnfile;
                            } else if ($file = \Idno\Entities\File::createFromFile($_FILES['file']['tmp_name'], $_FILES['file']['name'], $_FILES['file']['type'], true)) {
                                $return = true;
                            }
                            if ($return) {
                                $t       = \Idno\Core\Idno::site()->template();
                                $t->file = $file;
                                echo $t->draw('file/picker/donejs');
                                exit;
                            }
                        } else {
                            Idno::site()->session()->addErrorMessage("You can only upload images.");
                        }
                    }
                    $this->forward($_SERVER['HTTP_REDIRECT']);
                }
            }

        }

    }
<?php

namespace ItBlaster\SeoBundle\Controller;

use ItBlaster\SeoBundle\Model\SeoFile;
use Sonata\AdminBundle\Controller\CRUDController;

class SeoFileAdminController extends CRUDController
{
    protected $web_dir = '/../web/';

    public function deleteAction($id)
    {
        if ($this->getRestMethod() == 'DELETE') {
            /** @var SeoFile $seo_file */
            $seo_file = $this->admin->getObject($id);
            $this->deleteFile($seo_file->getName());
        }

        return parent::deleteAction($id);
    }

    public function createAction()
    {
        $result = parent::createAction();

        if ($this->getRestMethod() == 'POST' && $this->admin->getForm()->isValid()) {
            /** @var SeoFile $seo_file */
            $seo_file = $this->admin->getSubject();
            $this->writeFile($seo_file->getName(), $seo_file->getContent());
        }

        return $result;
    }

    public function editAction($id = NULL)
    {
        $result = parent::editAction($id);

        if ($this->getRestMethod() == 'POST' && $this->admin->getForm()->isValid()) {
            /** @var SeoFile $seo_file */
            $seo_file = $this->admin->getSubject();
            $this->writeFile($seo_file->getName(), $seo_file->getContent());
        }

        return $result;
    }

    /**
     * Записать файл
     * @param $name
     * @param $content
     * @throws \Exception
     */
    public function writeFile($name, $content)
    {
        // Проыеряем, что название не пустое
        if (empty($name)) {
            return;
        }
        // Проверяем существование файла
        $file_path = $this->get('kernel')->getRootDir() . $this->web_dir . $name;
        if (!file_exists($file_path)) {
            try {
                touch($file_path);
            } catch (\Exception $e) {
                throw new \Exception('Не удалось создать файл ' . $name);
            }
        }
        // Проверяем права
        $file_perms = fileperms($file_path);
        if ($file_perms & 0777 !== 0666) {
            try {
                chmod($file_path, '0666');
            } catch (\Exception $e) {
                throw new \Exception('Не удалось отредактировать права на файл ' . $name);
            }
        }
        // Записываем содержимое
        try {
            file_put_contents($file_path, $content, LOCK_EX);
        } catch (\Exception $e) {
            throw new \Exception('Не удалось отредактировать файл ' . $name);
        }
    }

    protected function deleteFile($name)
    {
        // Проыеряем, что название не пустое
        if (empty($name)) {
            return;
        }
        $file_path = $this->get('kernel')->getRootDir() . $this->web_dir . $name;
        if (file_exists($file_path)) {
            try {
                unlink($name);
            } catch (\Exception $e) {
                throw new \Exception('Не удалось удалить файл ' . $name);
            }
        }
    }
}

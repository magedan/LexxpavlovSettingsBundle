<?php

namespace Lexxpavlov\SettingsBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Lexxpavlov\SettingsBundle\Entity\Category;
use Lexxpavlov\SettingsBundle\Service\Settings as SettingsService;

class CategoryAdmin extends AbstractAdmin
{
    private ?SettingsService $settings = null;

    public function setSettings(SettingsService $settings): void
    {
        $this->settings = $settings;
    }

    public function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('name', null, [
                'route' => [
                    'name' => 'edit',
                ],
            ])
            ->add('comment')
        ;
    }

    public function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name')
            ->add('comment')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
        ;
    }

    public function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('name')
            ->add('comment')
        ;
    }

    /**
     * @param Catergory $object
     */
    protected function postPersist(object $object): void
    {
        $this->clearCache($object);
    }

    /**
     * @param Catergory $object
     */
    protected function postUpdate(object $object): void
    {
        $this->clearCache($object);
    }

    /**
     * @param Catergory $object
     */
    protected function preRemove(object $object): void
    {
        $this->clearCache($object);
    }

    /**
     * @param Category $object
     */
    private function clearCache(Category $object)
    {
        $this->settings->clearGroupCache($object->getName());
    }
}

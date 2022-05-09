<?php

namespace Lexxpavlov\SettingsBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Lexxpavlov\SettingsBundle\DBAL\SettingsType;
use Lexxpavlov\SettingsBundle\Entity\Settings;
use Lexxpavlov\SettingsBundle\Entity\Category;
use Lexxpavlov\SettingsBundle\Service\Settings as SettingsService;

class SettingsAdmin extends AbstractAdmin
{
    private ?SettingsService $settings = null;

    public function setSettings(SettingsService $settings): void
    {
        $this->settings = $settings;
    }

    public function configureListFields(ListMapper $listMapper): void
    {
        $useCategoryComment = false;

        $listMapper
            ->addIdentifier('name', null, [
                'route' => [
                    'name' => 'edit',
                ],
            ])
            ->add('category', null, array(
                'associated_property' => function(Category $cat) use ($useCategoryComment) {
                    return $useCategoryComment && $cat->getComment() ? $cat->getComment() : $cat->getName();
                },
                'sortable' => true,
                'sort_field_mapping' => array('fieldName' => 'name'),
                'sort_parent_association_mappings' => array(array('fieldName' => 'category'))
            ))
            ->add('type', ChoiceType::class, array('choices' => SettingsType::getReadableValues(), 'catalogue' => 'messages'))
            ->add('value', null, array('template' => '@LexxpavlovSettings/Admin/list_value.html.twig'))
            ->add('comment')
        ;
    }

    public function configureFormFields(FormMapper $formMapper): void
    {
        $valueType = $this->isNewForm()
            ? 'Lexxpavlov\SettingsBundle\Form\Type\SettingValueType'
            : 'setting_value';
        $formMapper
            ->add('name')
            ->add('category', ModelListType::class)
            ->add('type', ChoiceType::class, array(
                'choices' => SettingsType::getChoices(),
                'attr' => array('data-sonata-select2'=>'false'),
            ))
            ->add('value', $valueType)
            ->add('comment')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $useCategoryComment = false;

        $categoryOptions = $this->isNewForm()
            ? [
                'choice_label' => function (Category $cat) use ($useCategoryComment) {
                    return $useCategoryComment && $cat->getComment() ? $cat->getComment() : $cat->getName();
                },
            ] : [];
        $datagridMapper
            ->add('category', null, ['field_options' => $categoryOptions])
            ->add('name')
            ->add('type', null, [
                'field_type' => ChoiceType::class,
                'field_options' => [
                    'choices' => SettingsType::getChoices(),
                ],
            ])
        ;
    }

    public function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('name')
            ->add('type')
            ->add('value')
            ->add('comment')
        ;
    }

    protected function configure(): void
    {
        $this->setFormTheme(array_merge(
            $this->getFormTheme(),
            ['@LexxpavlovSettings/Form/setting_value_edit.html.twig']
        ));
    }

    /**
     * @param Settings $object
     */
    protected function postPersist(object $object): void
    {
        $this->clearCache($object);
    }

    /**
     * @param Settings $object
     */
    protected function postUpdate(object $object): void
    {
        $this->clearCache($object);
    }

    /**
     * @param Settings $object
     */
    protected function preRemove(object $object): void
    {
        $this->clearCache($object);
    }

    /**
     * @param Settings $object
     */
    private function clearCache(Settings $object)
    {
        $this->settings->clearCache($object->getName());

        if ($object->getCategory()) {
            $this->settings->clearGroupCache($object->getCategory()->getName());
        }
    }

    /**
     * @return bool
     */
    protected function isNewForm()
    {
        return method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');
    }
}

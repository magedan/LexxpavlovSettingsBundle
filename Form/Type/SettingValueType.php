<?php

namespace Lexxpavlov\SettingsBundle\Form\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SettingValueType extends AbstractType
{
    private ContainerInterface $container;

    private ?string $htmlWidget;

    public function __construct(ContainerInterface $container, ?string $htmlWidget)
    {
        $this->container = $container;
        $this->htmlWidget = $htmlWidget;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $choiceViewClass = ChoiceView::class;
        $choiceList = array(
            new $choiceViewClass('off', 'off', "Off"),
            new $choiceViewClass('on', 'on', "On"),
        );

        $view->vars = array_replace($view->vars, array(
            'required' => false,
            'multiple' => false,
            'expanded' => false,
            'empty_data' => null,
            'attr' => array('data-lexxpavlov-settings'=>'true', 'data-sonata-select2'=>'false'),
            'preferred_choices' => null,
            'choices' => $choiceList,
            'choice_translation_domain' => 'messages',
            'placeholder' => null,
            'html_widget' => $this->htmlWidget,
        ));
        if ($this->htmlWidget == 'ckeditor') {
            if ($this->container->has('ivory_ck_editor.form.type')) {
                $resolver = new OptionsResolver();
                $builder = $this->container->get('form.factory')->createBuilder();

                $ckeditorType = $this->container->get('ivory_ck_editor.form.type');
                $ckeditorType->configureOptions($resolver);
                $ckeditorType->buildForm($builder, $resolver->resolve());
                $ckeditorType->buildView($view, $builder->getForm(), []);
            } else {
                $view->vars = array_replace($view->vars, array(
                    'base_path' => $this->container->getParameter('lexxpavlov_settings.ckeditor.base_path'),
                    'js_path' => $this->container->getParameter('lexxpavlov_settings.ckeditor.js_path'),
                ));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'setting_value';
    }
}

<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Form\Type;

use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Form\Type\Master\PrefType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @FormType
 */
class AddressType extends AbstractType
{
    /**
     * @var array
     * @Inject("config")
     */
    protected $config;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['pref_options']['required'] = $options['required'];
        $options['addr01_options']['required'] = $options['required'];
        $options['addr02_options']['required'] = $options['required'];

        // required の場合は NotBlank も追加する
        if ($options['required']) {
            $options['pref_options']['constraints'] = array_merge(array(
                new Assert\NotBlank(array()),
            ), $options['pref_options']['constraints']);

            $options['addr01_options']['constraints'] = array_merge(array(
                new Assert\NotBlank(array()),
            ), $options['addr01_options']['constraints']);

            $options['addr02_options']['constraints'] = array_merge(array(
                new Assert\NotBlank(array()),
            ), $options['addr02_options']['constraints']);
        }

        if (!isset($options['options']['error_bubbling'])) {
            $options['options']['error_bubbling'] = $options['error_bubbling'];
        }

        $builder
            ->add($options['pref_name'], PrefType::class, array_merge_recursive($options['options'], $options['pref_options']))
            ->add($options['addr01_name'], TextType::class, array_merge_recursive($options['options'], $options['addr01_options']))
            ->add($options['addr02_name'], TextType::class, array_merge_recursive($options['options'], $options['addr02_options']))
        ;

        $builder->setAttribute('pref_name', $options['pref_name']);
        $builder->setAttribute('addr01_name', $options['addr01_name']);
        $builder->setAttribute('addr02_name', $options['addr02_name']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $builder = $form->getConfig();
        $view->vars['pref_name'] = $builder->getAttribute('pref_name');
        $view->vars['addr01_name'] = $builder->getAttribute('addr01_name');
        $view->vars['addr02_name'] = $builder->getAttribute('addr02_name');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'options' => array(),
            'help' => 'form.contact.address.help',
            'pref_options' => array('constraints' => array(), 'attr' => array('class' => ' p-region-id')),
            'addr01_options' => array(
                'constraints' => array(
                    // new Assert\Length(array('max' => $this->config['address1_len'])),
                    new Assert\Length(array('max' => 32)), // FIXME
                ),
                'attr' => array('class' => 'p-locality')
            ),
            'addr02_options' => array(
                'constraints' => array(
                    // new Assert\Length(array('max' => $this->config['address2_len'])),
                    new Assert\Length(array('max' => 32)), // FIXME
                ),
                'attr' => array('class' => 'p-street-address')
            ),
            'pref_name' => 'pref',
            'addr01_name' => 'addr01',
            'addr02_name' => 'addr02',
            'error_bubbling' => false,
            'inherit_data' => true,
            'trim' => true,
        ));
    }

    public function getBlockPrefix()
    {
        return 'address';
    }
}

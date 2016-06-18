<?php

/**
 * Created by PhpStorm.
 * User: al
 * Date: 18.06.16
 * Time: 21:23
 */

namespace Rispo\YandexKassaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class YandexKassaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'yandexkassa';
    }
}
